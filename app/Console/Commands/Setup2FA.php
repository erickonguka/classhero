<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class Setup2FA extends Command
{
    protected $signature = 'auth:setup-2fa {email}';
    protected $description = 'Setup 2FA for admin user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->where('role', 'admin')->first();
        
        if (!$user) {
            $this->error('Admin user not found');
            return;
        }
        
        $secret = $this->generateSecret();
        
        $user->update([
            'two_factor_secret' => $secret,
            'mfa_enabled' => true
        ]);
        
        $this->info('2FA setup completed for ' . $user->email);
        $this->info('Secret: ' . $secret);
    }
    
    private function generateSecret()
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'), 0, 32);
    }
}