<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RisdaHashService;
use App\Models\User;

class TestRisdaPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'risda:test-password {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test RISDA Argon2 + Salt password hashing system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hashService = app(RisdaHashService::class);
        
        $this->info('=== RISDA Password Security Test ===');
        $this->newLine();
        
        // Display hashing configuration
        $this->info('Current Hashing Configuration:');
        $config = $hashService->getHashingInfo();
        foreach ($config as $key => $value) {
            $this->line("  {$key}: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value));
        }
        $this->newLine();
        
        // Test password strength validation
        $this->info('Testing Password Strength Validation:');
        $testPasswords = [
            'weak' => 'password',
            'medium' => 'Password123',
            'strong' => 'MyStr0ng!P@ssw0rd2024'
        ];
        
        foreach ($testPasswords as $label => $password) {
            $validation = $hashService->validatePasswordStrength($password);
            $this->line("  {$label} ('{$password}'):");
            $this->line("    Valid: " . ($validation['valid'] ? 'Yes' : 'No'));
            $this->line("    Strength: {$validation['strength']}/100");
            if (!empty($validation['errors'])) {
                $this->line("    Errors: " . implode(', ', $validation['errors']));
            }
        }
        $this->newLine();
        
        // Test actual password hashing and verification
        $email = $this->argument('email') ?? 'test@risda.gov.my';
        $password = $this->argument('password') ?? 'TestPassword123!';
        
        $this->info("Testing Password Hashing for: {$email}");
        
        // Generate salt
        $salt = $hashService->generateSalt($email);
        $this->line("Generated Salt: " . substr($salt, 0, 20) . '...');
        
        // Hash password
        $hashedPassword = $hashService->hashPassword($password, $email);
        $this->line("Hashed Password: " . substr($hashedPassword, 0, 50) . '...');
        
        // Verify password
        $isValid = $hashService->verifyPassword($password, $hashedPassword, $email);
        $this->line("Password Verification: " . ($isValid ? 'PASSED' : 'FAILED'));
        
        // Test with wrong password
        $wrongPassword = $password . 'wrong';
        $isWrongValid = $hashService->verifyPassword($wrongPassword, $hashedPassword, $email);
        $this->line("Wrong Password Test: " . ($isWrongValid ? 'FAILED (Should be false)' : 'PASSED'));
        
        // Test with different email (different salt)
        $differentEmail = 'different@risda.gov.my';
        $isDifferentValid = $hashService->verifyPassword($password, $hashedPassword, $differentEmail);
        $this->line("Different Email Test: " . ($isDifferentValid ? 'FAILED (Should be false)' : 'PASSED'));
        
        $this->newLine();
        
        // Generate secure password
        $securePassword = $hashService->generateSecurePassword(16);
        $this->info("Generated Secure Password: {$securePassword}");
        
        $secureValidation = $hashService->validatePasswordStrength($securePassword);
        $this->line("Generated Password Strength: {$secureValidation['strength']}/100");
        
        $this->newLine();
        $this->info('=== Test Completed ===');
        
        if ($isValid && !$isWrongValid && !$isDifferentValid) {
            $this->info('✅ All password security tests PASSED!');
            return Command::SUCCESS;
        } else {
            $this->error('❌ Some password security tests FAILED!');
            return Command::FAILURE;
        }
    }
}
