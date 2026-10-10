<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class MakeAdmin extends Command
{
    protected $signature = 'vision:make-admin
                            {--name= : Admin display name}
                            {--email= : Admin email address}
                            {--password= : Admin password}';

    protected $description = 'Create (or update) a Vision Studio admin account';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Name');
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->option('password') ?: $this->secret('Password');

        $validator = Validator::make(
            compact('name', 'email', 'password'),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $existing = Admin::where('email', $email)->exists();

        $admin = Admin::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => $password, 'role' => 'super_admin']
        );

        $this->info(($existing ? 'Updated' : 'Created')." admin: {$admin->email}");

        return self::SUCCESS;
    }
}
