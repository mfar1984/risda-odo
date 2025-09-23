<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Services\RisdaHashService;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Hash password using RISDA custom Argon2 + salt
     *
     * @return Attribute
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                $hashService = app(RisdaHashService::class);
                return $hashService->hashPassword($value, $this->email ?? '');
            }
        );
    }

    /**
     * Verify password using RISDA custom hash service
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        $hashService = app(RisdaHashService::class);
        return $hashService->verifyPassword($password, $this->password, $this->email);
    }

    /**
     * Check if password needs rehashing
     *
     * @return bool
     */
    public function needsPasswordRehash(): bool
    {
        $hashService = app(RisdaHashService::class);
        return $hashService->needsRehash($this->password);
    }

    /**
     * Validate password strength
     *
     * @param string $password
     * @return array
     */
    public static function validatePasswordStrength(string $password): array
    {
        $hashService = app(RisdaHashService::class);
        return $hashService->validatePasswordStrength($password);
    }

    /**
     * Generate secure password
     *
     * @param int $length
     * @return string
     */
    public static function generateSecurePassword(int $length = 16): string
    {
        $hashService = app(RisdaHashService::class);
        return $hashService->generateSecurePassword($length);
    }
}
