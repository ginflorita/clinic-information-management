<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

/**
 * Writes an audit_logs entry for every create/update/delete on models that
 * use this trait (architecture.md §112 — every sensitive operation on
 * clinical or financial data should be traceable to an actor and a
 * before/after value). Applied selectively to patient PHI and clinical/
 * billing transaction models, not to lookup/master-data tables.
 */
trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->recordAuditLog('create', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            $model->recordAuditLog('update', array_intersect_key($model->getOriginal(), $changes), $changes);
        });

        static::deleted(function ($model) {
            $model->recordAuditLog('delete', $model->getAttributes(), null);
        });
    }

    protected function recordAuditLog(string $action, ?array $oldValues, ?array $newValues): void
    {
        AuditLog::create([
            'actor_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $this->getTable(),
            'entity_id' => $this->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
