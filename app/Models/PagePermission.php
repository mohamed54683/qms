<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_name',
        'page_key',
        'page_route',
        'page_icon',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the role permissions for this page
     */
    public function rolePermissions()
    {
        return $this->hasMany(RolePagePermission::class);
    }

    /**
     * Get roles that have access to this page
     */
    public function roles()
    {
        return $this->belongsToMany(
            \Spatie\Permission\Models\Role::class,
            'role_page_permissions',
            'page_permission_id',
            'role_id'
        )->withPivot([
            'can_view',
            'can_create',
            'can_edit',
            'can_delete',
            'can_approve',
            'show_in_dashboard'
        ])->withTimestamps();
    }
}
