<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the roles for the user via model_has_roles table
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
                    ->where('model_type', self::class);
    }

    /**
     * Check if user has a specific role (compatibility with Spatie)
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Assign a role to the user (compatibility with Spatie)
     */
    public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$this->hasRole($roleName)) {
            $this->roles()->attach($role->id, ['model_type' => self::class]);
        }
        return $this;
    }

    /**
     * Remove a role from the user (compatibility with Spatie)
     */
    public function removeRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
        return $this;
    }

    /**
     * Scope to filter users by role (compatibility with Spatie)
     */
    public function scopeRole($query, $roleName)
    {
        return $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * The restaurants that belong to the user.
     */
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class);
    }

    /**
     * The restaurants this user manages as area manager.
     */
    public function managedRestaurants()
    {
        return $this->hasMany(Restaurant::class, 'area_manager_id');
    }

    /**
     * Area managers that are assigned to this user (many-to-many relationship)
     */
    public function assignedAreaManagers()
    {
        return $this->belongsToMany(User::class, 'user_area_managers', 'user_id', 'area_manager_id');
    }

    /**
     * Users that have this user as their assigned area manager (many-to-many relationship)
     */
    public function managedUsers()
    {
        return $this->belongsToMany(User::class, 'user_area_managers', 'area_manager_id', 'user_id');
    }

    /**
     * Check if user can perform action on a page
     */
    public function canAccessPage($pageKey, $action = 'view')
    {
        $role = $this->roles->first();
        if (!$role) {
            return false;
        }

        $page = \App\Models\PagePermission::where('page_key', $pageKey)->first();
        if (!$page) {
            return false;
        }

        $permission = \App\Models\RolePagePermission::where('role_id', $role->id)
            ->where('page_permission_id', $page->id)
            ->first();

        if (!$permission) {
            return false;
        }

        switch ($action) {
            case 'view':
                return $permission->can_view;
            case 'create':
                return $permission->can_create;
            case 'edit':
                return $permission->can_edit;
            case 'delete':
                return $permission->can_delete;
            case 'approve':
                return $permission->can_approve;
            default:
                return false;
        }
    }

    /**
     * Check if page should show in dashboard for this user
     */
    public function showInDashboard($pageKey)
    {
        $role = $this->roles->first();
        if (!$role) {
            return false;
        }

        $page = \App\Models\PagePermission::where('page_key', $pageKey)->first();
        if (!$page) {
            return false;
        }

        $permission = \App\Models\RolePagePermission::where('role_id', $role->id)
            ->where('page_permission_id', $page->id)
            ->first();

        return $permission ? $permission->show_in_dashboard : false;
    }

    /**
     * Get all accessible pages for this user
     */
    public function getAccessiblePages()
    {
        $role = $this->roles->first();
        if (!$role) {
            return collect();
        }

        return \App\Models\PagePermission::whereHas('rolePermissions', function ($query) use ($role) {
            $query->where('role_id', $role->id)
                  ->where('can_view', true);
        })->orderBy('display_order')->get();
    }
}
