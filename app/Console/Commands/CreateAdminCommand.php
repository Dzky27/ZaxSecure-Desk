<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin 
                            {--name= : Name of the administrator}
                            {--email= : Email address of the administrator}
                            {--password= : Password for the administrator account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provision a secure administrator account without hardcoding credentials';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->option('name') ?: env('ADMIN_NAME');
        $email = $this->option('email') ?: env('ADMIN_EMAIL');
        $password = $this->option('password') ?: env('ADMIN_PASSWORD');

        if (!$name) {
            $name = $this->ask('Administrator full name');
        }

        if (!$email) {
            $email = $this->ask('Administrator email address');
        }

        if (!$password) {
            $password = $this->secret('Administrator password (min 8 characters)');
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->info("Administrator '{$user->email}' provisioned successfully (ID: {$user->id}).");

        return self::SUCCESS;
    }
}
