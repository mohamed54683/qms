<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePagePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'page_permission_id',
        'can_view',
        'can_create',
        'can_edit',
        'can_delete',
        'can_approve',
        'show_in_dashboard',
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
        'can_approve' => 'boolean',
        'show_in_dashboard' => 'boolean',
    ];

    /**
     * Get the role
     */
    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

    /**
     * Get the page permission
     */
    public function pagePermission()
    {
        return $this->belongsTo(PagePermission::class);
    }
}
