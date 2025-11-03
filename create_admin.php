<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Checking for admin user...\n";

$user = User::where('email', 'it@ghidas.com')->first();

if ($user) {
    echo "✓ Admin user exists:\n";
    echo "  Email: {$user->email}\n";
    echo "  Name: {$user->name}\n";
    echo "  Updating password...\n";
    $user->update(['password' => Hash::make('sdb@123456')]);
    echo "  Password updated to: sdb@123456\n";
} else {
    echo "Creating admin user...\n";
    $user = User::create([
        'name' => 'IT Admin',
        'email' => 'it@ghidas.com',
        'password' => Hash::make('sdb@123456'),
    ]);
    echo "✓ Admin user created:\n";
    echo "  Email: {$user->email}\n";
    echo "  Password: sdb@123456\n";
}

echo "\nAll users in database:\n";
User::all()->each(function($u) {
    echo "  • {$u->name} ({$u->email})\n";
});
