<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tournament;
use App\Models\TournamentParticipant;
use App\Services\TournamentGeneratorService;
use App\Services\TournamentSchedulerService;
use App\Services\TournamentScoreService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TournamentSeeder extends Seeder
{
    protected TournamentGeneratorService $generator;
    protected TournamentSchedulerService $scheduler;
    protected TournamentScoreService $scoreService;

    public function __construct(
        TournamentGeneratorService $generator,
        TournamentSchedulerService $scheduler,
        TournamentScoreService $scoreService
    ) {
        $this->generator = $generator;
        $this->scheduler = $scheduler;
        $this->scoreService = $scoreService;
    }

    public function run(): void
    {
        // 1. Ensure test users exist
        $player = User::firstWhere('email', 'player@courtconnect.test');
        if (!$player) {
            $player = User::create([
                'name' => 'Alex Rivera',
                'email' => 'player@courtconnect.test',
                'phone' => '+63 911 222 3333',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }

        // Create some additional users to act as members
        $names = ['Liam Neeson', 'Emma Watson', 'John Doe', 'Jane Smith', 'Bruce Wayne', 'Clark Kent', 'Diana Prince'];
        $seededMembers = [];
        foreach ($names as $idx => $name) {
            $email = 'seeded_player' . ($idx + 1) . '@courtconnect.test';
            $user = User::firstWhere('email', $email);
            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'phone' => '+63 911 000 000' . ($idx + 1),
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
            }
            $seededMembers[] = $user;
        }

        // 2. Create a Draft Tournament
        Tournament::create([
            'name' => 'Autumn Badminton Invitational',
            'description' => "Welcome to the Annual Autumn Badminton Invitational. \n\nAll club members are welcome to join. Double-elimination format.",
            'type' => 'double',
            'status' => 'draft',
            'registration_start' => now()->addDays(5),
            'registration_end' => now()->addDays(15),
            'start_date' => now()->addDays(16),
            'max_participants' => 16,
            'entry_fee' => 15.00,
            'auto_schedule' => true,
        ]);

        // 3. Create a Registration Open Tournament
        $openTournament = Tournament::create([
            'name' => 'Peak Summer Smash Basketball',
            'description' => "Our signature Basketball tournament of the summer. \n\nWinners walk away with a $500 grand prize and trophy.",
            'type' => 'round_robin',
            'status' => 'registration_open',
            'registration_start' => now()->subDays(5),
            'registration_end' => now()->addDays(5),
            'start_date' => now()->addDays(6),
            'max_participants' => 8,
            'entry_fee' => 25.00,
            'auto_schedule' => true,
        ]);

        // Register a couple participants in the open one
        TournamentParticipant::create([
            'tournament_id' => $openTournament->id,
            'user_id' => $player->id,
            'participant_type' => 'individual',
            'display_name' => $player->name,
            'seed' => 1,
            'checked_in' => false,
            'status' => 'active',
        ]);

        TournamentParticipant::create([
            'tournament_id' => $openTournament->id,
            'user_id' => $seededMembers[0]->id,
            'participant_type' => 'individual',
            'display_name' => $seededMembers[0]->name,
            'seed' => 2,
            'checked_in' => false,
            'status' => 'active',
        ]);

        // 4. Create an Active/Live Tournament (Single Elimination)
        $activeTournament = Tournament::create([
            'name' => 'CourtConnect Championship Open (Padel)',
            'description' => "The premier indoor tournament of the season. \n\nLive bracket updates, multi-set matches, and dedicated court scheduling.",
            'type' => 'single',
            'status' => 'active',
            'registration_start' => now()->subDays(10),
            'registration_end' => now()->subDays(2),
            'start_date' => now()->subDay(),
            'max_participants' => 8,
            'entry_fee' => 50.00,
            'auto_schedule' => true,
        ]);

        // Register 8 participants (Alex Rivera + 7 others)
        $allPlayers = array_merge([$player], $seededMembers);
        foreach ($allPlayers as $idx => $user) {
            TournamentParticipant::create([
                'tournament_id' => $activeTournament->id,
                'user_id' => $user->id,
                'participant_type' => 'individual',
                'display_name' => $user->name,
                'seed' => $idx + 1,
                'checked_in' => true,
                'checked_in_at' => now()->subHours(4),
                'status' => 'active',
            ]);
        }

        // Generate Brackets for the Active Tournament
        $this->generator->generate($activeTournament);

        // Auto schedule matches onto courts
        $this->scheduler->schedule($activeTournament);

        // Restore active tournament status from 'scheduled' or 'seeding' to 'active' for proper display
        $activeTournament->update(['status' => 'active']);

        // Score 2 of the Round 1 matches to simulate ongoing live tournament progression
        $r1Matches = $activeTournament->matches()->where('round_number', 1)->orderBy('match_number')->get();
        
        if ($r1Matches->count() >= 2) {
            // Match 1: Player 1 (Alex Rivera) vs Player 8 (Diana Prince)
            $this->scoreService->submitScore($r1Matches[0], [
                ['p1' => 6, 'p2' => 4], // Set 1: P1 wins
                ['p1' => 6, 'p2' => 2], // Set 2: P1 wins
            ]);

            // Match 2: Player 4 (John Doe) vs Player 5 (Jane Smith)
            $this->scoreService->submitScore($r1Matches[1], [
                ['p1' => 2, 'p2' => 6], // Set 1: P2 wins
                ['p1' => 6, 'p2' => 4], // Set 2: P1 wins
                ['p1' => 3, 'p2' => 6], // Set 3: P2 wins
            ]);
        }
    }
}
