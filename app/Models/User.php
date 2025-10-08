<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Services\RisdaHashService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'kumpulan_id',
        'staf_id',
        'jenis_organisasi',
        'organisasi_id',
        'stesen_akses_ids',
        'status',
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
            'password' => 'hashed',
            'stesen_akses_ids' => 'array',
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

    /**
     * Get the user group that owns the user.
     */
    public function kumpulan()
    {
        return $this->belongsTo(UserGroup::class, 'kumpulan_id');
    }
    
    public function risdaStaf()
    {
        return $this->belongsTo(RisdaStaf::class, 'staf_id');
    }

    /**
     * Get organisation based on type.
     */
    public function organisasi()
    {
        switch($this->jenis_organisasi) {
            case 'bahagian':
                return $this->belongsTo(RisdaBahagian::class, 'organisasi_id');
            case 'stesen':
                return $this->belongsTo(RisdaStesen::class, 'organisasi_id');
            default:
                return null;
        }
    }

    public function bahagian(): BelongsTo
    {
        return $this->belongsTo(RisdaBahagian::class, 'bahagian_id');
    }

    public function stesen(): BelongsTo
    {
        return $this->belongsTo(RisdaStesen::class, 'organisasi_id');
    }

    public function staf(): BelongsTo
    {
        return $this->belongsTo(RisdaStaf::class, 'staf_id');
    }

    public function programsSebagaiPemandu(): HasMany
    {
        return $this->hasMany(Program::class, 'pemandu_id');
    }

    public function logPemandu(): HasMany
    {
        return $this->hasMany(LogPemandu::class, 'pemandu_id');
    }

    /**
     * Get multiple stesen akses (accessor method).
     */
    public function getStesenAksesAttribute()
    {
        if (!$this->stesen_akses_ids) {
            return collect();
        }

        return RisdaStesen::whereIn('id', $this->stesen_akses_ids)->get();
    }

    /**
     * Get stesen akses names as string.
     */
    public function getStesenAksesNamesAttribute()
    {
        $stesens = $this->stesen_akses; // Use accessor
        if ($stesens->isEmpty()) {
            return 'Semua Stesen';
        }

        return $stesens->pluck('nama_stesen')->join(', ');
    }

    /**
     * Check if user has specific permission.
     */
    public function adaKebenaran($modul, $aksi)
    {
        // Administrator bypass - users with jenis_organisasi = 'semua' have all permissions
        if ($this->jenis_organisasi === 'semua') {
            return true;
        }

        if (!$this->kumpulan) {
            return false;
        }

        return $this->kumpulan->adaKebenaran($modul, $aksi);
    }
}
