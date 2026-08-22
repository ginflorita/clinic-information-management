<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    /**
     * Module keys gate the app's route groups (see routes/web.php) and the
     * sidebar. Keep this list in sync with the `module:<key>` middleware
     * applied to each route group.
     */
    const MODULES = [
        'patients' => 'Patients',
        'appointments' => 'Appointments',
        'queue' => 'Queue',
        'recalls' => 'Recalls',
        'encounters' => 'Encounters',
        'treatment_plans' => 'Treatment Plans',
        'invoices' => 'Billing & Payments',
        'inventory' => 'Inventory',
        'purchase_orders' => 'Purchase Orders',
        'audit_logs' => 'Audit Log',
        'master_data' => 'Master Data',
    ];

    protected $fillable = [
        'name',
        'description',
    ];

    public function permissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasModule(string $module): bool
    {
        return $this->permissions->contains('module', $module);
    }
}
