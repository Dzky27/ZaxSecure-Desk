<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\SecurityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Record a user/admin activity log safely (never throws if DB logging fails).
     */
    public static function logActivity(
        string $action,
        ?string $subjectType = null,
        ?int $subjectId = null,
        array $metadata = [],
        ?Request $request = null,
        ?int $userId = null
    ): void {
        try {
            $req = $request ?? request();
            $uid = $userId ?? ($req?->user()?->id);

            ActivityLog::create([
                'user_id' => $uid,
                'action' => $action,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'ip_address' => $req?->ip(),
                'user_agent' => $req ? substr((string) $req->userAgent(), 0, 500) : null,
                'metadata' => empty($metadata) ? null : $metadata,
            ]);
        } catch (\Throwable $e) {
            Log::warning('AuditLogger::logActivity failed', ['action' => $action, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Record a security event safely.
     */
    public static function logSecurity(
        string $eventType,
        string $severity = 'medium',
        ?string $description = null,
        array $metadata = [],
        ?Request $request = null,
        ?int $userId = null
    ): void {
        try {
            $req = $request ?? request();
            $uid = $userId ?? ($req?->user()?->id);
            $validSeverity = in_array($severity, ['low', 'medium', 'high', 'critical'], true) ? $severity : 'medium';

            SecurityEvent::create([
                'user_id' => $uid,
                'event_type' => $eventType,
                'severity' => $validSeverity,
                'ip_address' => $req?->ip(),
                'user_agent' => $req ? substr((string) $req->userAgent(), 0, 500) : null,
                'description' => $description,
                'metadata' => empty($metadata) ? null : $metadata,
            ]);
        } catch (\Throwable $e) {
            Log::warning('AuditLogger::logSecurity failed', ['event_type' => $eventType, 'error' => $e->getMessage()]);
        }
    }
}
