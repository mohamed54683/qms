<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class UnifyRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:unify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unify duplicate restaurant_manager role to rm';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting role unification...');
        
        // Check if restaurant_manager role exists
        $restaurantManagerRole = Role::where('name', 'restaurant_manager')->first();
        $rmRole = Role::where('name', 'rm')->first();
        
        if (!$restaurantManagerRole) {
            $this->info('No restaurant_manager role found. Nothing to do.');
            return 0;
        }
        
        if (!$rmRole) {
            $this->error('rm role does not exist! Please run: php artisan db:seed --class=RoleSeeder');
            return 1;
        }
        
        // Get users with restaurant_manager role
        $users = User::role('restaurant_manager')->get();
        $count = $users->count();
        
        if ($count === 0) {
            $this->info('No users have restaurant_manager role.');
        } else {
            $this->info("Found {$count} user(s) with restaurant_manager role. Migrating to rm...");
            
            foreach ($users as $user) {
                // Remove restaurant_manager role
                $user->removeRole('restaurant_manager');
                // Add rm role if not already assigned
                if (!$user->hasRole('rm')) {
                    $user->assignRole('rm');
                }
                $this->info("  - Migrated user: {$user->name} ({$user->email})");
            }
        }
        
        // Delete the restaurant_manager role
        $this->info('Deleting restaurant_manager role...');
        $restaurantManagerRole->delete();
        
        $this->info('✅ Role unification complete!');
        $this->info('Remaining roles: ' . Role::pluck('name')->implode(', '));
        
        return 0;
    }
}
