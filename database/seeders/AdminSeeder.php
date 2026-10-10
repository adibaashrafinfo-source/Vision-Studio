<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Create the default Super Admin.
     *
     * Credentials can be overridden via .env (ADMIN_EMAIL / ADMIN_PASSWORD).
     * Change the password immediately after first login in production.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@visionstudio.test')],
            [
                'name' => env('ADMIN_NAME', 'Vision Studio Admin'),
                'password' => env('ADMIN_PASSWORD', 'password'),
                'role' => 'super_admin',
            ]
        );
    }
}
