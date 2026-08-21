<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Support\Arr;
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
            $model->recordAuditLog('create', null, $model->auditableAttributes());
        });

        static::updated(function ($model) {
            $changes = Arr::except($model->getChanges(), array_merge($model->getHidden(), ['updated_at']));

            if (empty($changes)) {
                return;
            }

            $model->recordAuditLog('update', array_intersect_key($model->getOriginal(), $changes), $changes);
        });

        static::deleted(function ($model) {
            $model->recordAuditLog('delete', $model->auditableAttributes(), null);
        });
    }

    /**
     * Attributes are excluded via $hidden rather than logged in full —
     * hidden fields (e.g. User's password/remember_token) never belong in
     * an audit trail even hashed.
     */
    protected function auditableAttributes(): array
    {
        return Arr::except($this->getAttributes(), $this->getHidden());
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
