<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'guard_name',
    ];

    /**
     * Get the users for the role via model_has_roles table
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id')
                    ->where('model_type', User::class);
    }

    /**
     * Get the role permissions
     */
    public function rolePermissions()
    {
        return $this->hasMany(RolePagePermission::class, 'role_id');
    }
}