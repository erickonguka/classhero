<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Hash;

class SetupAdminMfa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:setup-mfa {email : Admin email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup MFA for admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->where('role', 'admin')->first();
        
        if (!$user) {
            $this->error('Admin user not found with email: ' . $email);
            return 1;
        }
        
        $google2fa = new Google2FA();
        $secretKey = $google2fa->generateSecretKey();
        
        // Generate recovery codes
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
        }
        
        $user->update([
            'two_factor_secret' => encrypt($secretKey),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'mfa_enabled' => true
        ]);
        
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'ClassHero',
            $user->email,
            $secretKey
        );
        
        $this->info('MFA has been enabled for: ' . $user->name);
        $this->info('Secret Key: ' . $secretKey);
        $this->info('QR Code URL: ' . $qrCodeUrl);
        $this->newLine();
        $this->info('Recovery Codes (save these safely):');
        foreach ($recoveryCodes as $code) {
            $this->info('  ' . $code);
        }
        $this->newLine();
        $this->info('Instructions:');
        $this->info('1. Install Google Authenticator or similar app');
        $this->info('2. Scan the QR code or manually enter the secret key');
        $this->info('3. Use the 6-digit code from your app to login');
        $this->info('4. Save the recovery codes in a secure location');
        
        return 0;
    }
}
