<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Services\AuditLogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireUnpaidReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:expire-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire unpaid reservations past their payment limits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $expiredCount = 0;

        // 1. Online Manual Payments (GCash, Maya, Bank Transfer) - Expire after 30 minutes if no proof uploaded
        $onlineThreshold = $now->copy()->subMinutes(30);
        
        $unpaidOnline = Reservation::where('status', 'pending')
            ->whereHas('payment', function ($query) {
                $query->whereIn('payment_method', ['gcash', 'maya', 'bank_transfer'])
                      ->where('payment_status', 'unpaid');
            })
            ->where('created_at', '<=', $onlineThreshold)
            ->get();

        foreach ($unpaidOnline as $reservation) {
            DB::transaction(function () use ($reservation) {
                $reservation->update(['status' => 'cancelled']);
                
                $reservation->user->systemNotifications()->create([
                    'title' => 'Reservation Expired',
                    'message' => "Your reservation {$reservation->reservation_number} has expired because no proof of payment was uploaded within 30 minutes.",
                ]);

                \App\Services\AuditLogService::log('reservation_expired_unpaid', $reservation, [
                    'reservation_number' => $reservation->reservation_number,
                    'reason' => 'Online payment proof not uploaded within 30 minutes threshold.',
                ]);
            });
            $expiredCount++;
        }

        // 2. Pay at Venue - Expire if player does not show up within 15 minutes after start time
        $today = $now->toDateString();
        $timeThreshold = $now->copy()->subMinutes(15)->toTimeString();

        $unpaidVenue = Reservation::where('status', 'pending')
            ->whereHas('payment', function ($query) {
                $query->where('payment_method', 'pay_at_venue')
                      ->where('payment_status', 'unpaid');
            })
            ->where(function ($query) use ($today, $timeThreshold) {
                $query->where('reservation_date', '<', $today)
                      ->orWhere(function ($q) use ($today, $timeThreshold) {
                          $q->where('reservation_date', $today)
                            ->where('start_time', '<=', $timeThreshold);
                      });
            })
            ->get();

        foreach ($unpaidVenue as $reservation) {
            DB::transaction(function () use ($reservation) {
                $reservation->update(['status' => 'cancelled']);
                
                $reservation->user->systemNotifications()->create([
                    'title' => 'Reservation Cancelled (No-Show)',
                    'message' => "Your reservation {$reservation->reservation_number} was cancelled because you did not check in and pay within 15 minutes of the start time.",
                ]);

                \App\Services\AuditLogService::log('reservation_expired_noshow', $reservation, [
                    'reservation_number' => $reservation->reservation_number,
                    'reason' => 'Venue payment check-in time limit (15 mins past start) exceeded.',
                ]);
            });
            $expiredCount++;
        }

        if ($expiredCount > 0) {
            $this->info("Expired {$expiredCount} unpaid/no-show reservations.");
        }

        return Command::SUCCESS;
    }
}
