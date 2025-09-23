<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class RisdaHashService
{
    /**
     * Generate a secure salt for password hashing
     *
     * @param string $identifier User identifier (email/username)
     * @return string
     */
    public function generateSalt(string $identifier): string
    {
        $pepper = Config::get('hashing.risda_salt.pepper', 'RISDA_DEFAULT_PEPPER');
        $rounds = Config::get('hashing.risda_salt.rounds', 3);
        
        // Create base salt from identifier and pepper
        $baseSalt = hash('sha256', $identifier . $pepper);
        
        // Apply multiple rounds of hashing for additional security
        $salt = $baseSalt;
        for ($i = 0; $i < $rounds; $i++) {
            $salt = hash('sha256', $salt . $pepper . $i);
        }
        
        return $salt;
    }

    /**
     * Hash password using Argon2ID with custom salt
     *
     * @param string $password Plain text password
     * @param string $identifier User identifier for salt generation
     * @return string
     */
    public function hashPassword(string $password, string $identifier): string
    {
        if (!Config::get('hashing.risda_salt.enabled', true)) {
            return Hash::make($password);
        }

        $salt = $this->generateSalt($identifier);
        $saltedPassword = $salt . $password . $salt;
        
        return Hash::make($saltedPassword);
    }

    /**
     * Verify password against hash using custom salt
     *
     * @param string $password Plain text password
     * @param string $hash Stored hash
     * @param string $identifier User identifier for salt generation
     * @return bool
     */
    public function verifyPassword(string $password, string $hash, string $identifier): bool
    {
        if (!Config::get('hashing.risda_salt.enabled', true)) {
            return Hash::check($password, $hash);
        }

        $salt = $this->generateSalt($identifier);
        $saltedPassword = $salt . $password . $salt;
        
        return Hash::check($saltedPassword, $hash);
    }

    /**
     * Check if password needs rehashing
     *
     * @param string $hash Stored hash
     * @return bool
     */
    public function needsRehash(string $hash): bool
    {
        return Hash::needsRehash($hash);
    }

    /**
     * Generate secure random password
     *
     * @param int $length Password length
     * @return string
     */
    public function generateSecurePassword(int $length = 16): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $password;
    }

    /**
     * Validate password strength
     *
     * @param string $password
     * @return array
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'strength' => $this->calculatePasswordStrength($password)
        ];
    }

    /**
     * Calculate password strength score
     *
     * @param string $password
     * @return int Score from 0-100
     */
    private function calculatePasswordStrength(string $password): int
    {
        $score = 0;
        
        // Length bonus
        $score += min(strlen($password) * 4, 40);
        
        // Character variety bonus
        if (preg_match('/[a-z]/', $password)) $score += 10;
        if (preg_match('/[A-Z]/', $password)) $score += 10;
        if (preg_match('/[0-9]/', $password)) $score += 10;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score += 15;
        
        // Complexity bonus
        if (preg_match('/[a-z].*[A-Z]|[A-Z].*[a-z]/', $password)) $score += 5;
        if (preg_match('/[a-zA-Z].*[0-9]|[0-9].*[a-zA-Z]/', $password)) $score += 5;
        if (preg_match('/[a-zA-Z0-9].*[^a-zA-Z0-9]|[^a-zA-Z0-9].*[a-zA-Z0-9]/', $password)) $score += 5;
        
        return min($score, 100);
    }

    /**
     * Get hashing algorithm info
     *
     * @return array
     */
    public function getHashingInfo(): array
    {
        return [
            'driver' => Config::get('hashing.driver'),
            'argon2id_memory' => Config::get('hashing.argon2id.memory'),
            'argon2id_threads' => Config::get('hashing.argon2id.threads'),
            'argon2id_time' => Config::get('hashing.argon2id.time'),
            'salt_enabled' => Config::get('hashing.risda_salt.enabled'),
            'salt_rounds' => Config::get('hashing.risda_salt.rounds'),
        ];
    }
}
