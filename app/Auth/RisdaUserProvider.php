<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Services\RisdaHashService;

class RisdaUserProvider extends EloquentUserProvider
{
    /**
     * The RISDA hash service instance.
     *
     * @var RisdaHashService
     */
    protected $hashService;

    /**
     * Create a new RISDA user provider.
     *
     * @param RisdaHashService $hashService
     * @param string $model
     * @return void
     */
    public function __construct(RisdaHashService $hashService, $model)
    {
        $this->hashService = $hashService;
        parent::__construct(app('hash'), $model);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];
        
        // Use RISDA custom hash service for password verification
        return $this->hashService->verifyPassword($plain, $user->getAuthPassword(), $user->email);
    }

    /**
     * Rehash the user's password if required and save the user.
     *
     * @param Authenticatable $user
     * @param array $credentials
     * @param bool $force
     * @return void
     */
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        if (! $this->hashService->needsRehash($user->getAuthPassword()) && ! $force) {
            return;
        }

        $user->forceFill([
            'password' => $credentials['password'],
        ])->save();
    }
}
