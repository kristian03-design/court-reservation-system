<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public static function log(string $action, ?Model $auditable = null, array $metadata = []): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable ? $auditable->getKey() : null,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }
}
