<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RisdaHashService;
use App\Models\User;

class TestRisdaHashCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'risda:test-hash {--create-user} {--test-login}';

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
        
        $this->info('🔐 RISDA Argon2 + Salt Password Testing');
        $this->info('=====================================');
        
        // Display hashing configuration
        $this->displayHashingInfo($hashService);
        
        if ($this->option('create-user')) {
            $this->createTestUser($hashService);
        }
        
        if ($this->option('test-login')) {
            $this->testLogin($hashService);
        }
        
        // Basic hash testing
        $this->basicHashTest($hashService);
        
        // Password strength testing
        $this->passwordStrengthTest($hashService);
        
        $this->info('✅ All tests completed successfully!');
    }
    
    private function displayHashingInfo(RisdaHashService $hashService)
    {
        $info = $hashService->getHashingInfo();
        
        $this->info('📊 Current Hashing Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Driver', $info['driver']],
                ['Argon2ID Memory', number_format($info['argon2id_memory']) . ' KB'],
                ['Argon2ID Threads', $info['argon2id_threads']],
                ['Argon2ID Time', $info['argon2id_time']],
                ['Salt Enabled', $info['salt_enabled'] ? 'Yes' : 'No'],
                ['Salt Rounds', $info['salt_rounds']],
            ]
        );
        $this->newLine();
    }
    
    private function createTestUser(RisdaHashService $hashService)
    {
        $this->info('👤 Creating Test User...');
        
        $email = 'test@risda.gov.my';
        $password = 'RisdaSecure123!';
        
        // Delete existing test user
        User::where('email', $email)->delete();
        
        $user = User::create([
            'name' => 'Test RISDA User',
            'email' => $email,
            'password' => $password,
        ]);
        
        $this->info("✅ Test user created: {$email}");
        $this->info("🔑 Password: {$password}");
        $this->newLine();
    }
    
    private function testLogin(RisdaHashService $hashService)
    {
        $this->info('🔓 Testing Login Authentication...');
        
        $email = 'test@risda.gov.my';
        $password = 'RisdaSecure123!';
        $wrongPassword = 'WrongPassword123!';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error('❌ Test user not found. Run with --create-user first.');
            return;
        }
        
        // Test correct password
        $isValid = $user->verifyPassword($password);
        $this->info($isValid ? '✅ Correct password verification: PASSED' : '❌ Correct password verification: FAILED');
        
        // Test wrong password
        $isInvalid = $user->verifyPassword($wrongPassword);
        $this->info(!$isInvalid ? '✅ Wrong password rejection: PASSED' : '❌ Wrong password rejection: FAILED');
        
        $this->newLine();
    }
    
    private function basicHashTest(RisdaHashService $hashService)
    {
        $this->info('🧪 Basic Hash Testing...');
        
        $testCases = [
            ['email' => 'user1@risda.gov.my', 'password' => 'TestPassword123!'],
            ['email' => 'user2@risda.gov.my', 'password' => 'AnotherSecure456@'],
            ['email' => 'admin@risda.gov.my', 'password' => 'AdminPassword789#'],
        ];
        
        foreach ($testCases as $case) {
            $hash1 = $hashService->hashPassword($case['password'], $case['email']);
            $hash2 = $hashService->hashPassword($case['password'], $case['email']);
            
            // Hashes should be different (due to Argon2 salt)
            $differentHashes = $hash1 !== $hash2;
            
            // But verification should work for both
            $verify1 = $hashService->verifyPassword($case['password'], $hash1, $case['email']);
            $verify2 = $hashService->verifyPassword($case['password'], $hash2, $case['email']);
            
            // Wrong email should fail
            $wrongEmailVerify = $hashService->verifyPassword($case['password'], $hash1, 'wrong@email.com');
            
            $this->info("📧 {$case['email']}:");
            $this->info($differentHashes ? '  ✅ Different hashes generated' : '  ❌ Same hashes generated');
            $this->info($verify1 ? '  ✅ Hash 1 verification passed' : '  ❌ Hash 1 verification failed');
            $this->info($verify2 ? '  ✅ Hash 2 verification passed' : '  ❌ Hash 2 verification failed');
            $this->info(!$wrongEmailVerify ? '  ✅ Wrong email rejected' : '  ❌ Wrong email accepted');
        }
        
        $this->newLine();
    }
    
    private function passwordStrengthTest(RisdaHashService $hashService)
    {
        $this->info('💪 Password Strength Testing...');
        
        $testPasswords = [
            'weak' => 'password',
            'medium' => 'Password123',
            'strong' => 'StrongPassword123!',
            'very_strong' => 'VeryStr0ng&Secure#Password2024!',
        ];
        
        $results = [];
        
        foreach ($testPasswords as $label => $password) {
            $validation = $hashService->validatePasswordStrength($password);
            $results[] = [
                'Password Type',
                $label,
                $validation['strength'] . '/100',
                $validation['valid'] ? 'Valid' : 'Invalid',
                count($validation['errors']) . ' errors'
            ];
        }
        
        $this->table(
            ['Type', 'Label', 'Strength', 'Status', 'Errors'],
            $results
        );
        
        $this->newLine();
    }
}
