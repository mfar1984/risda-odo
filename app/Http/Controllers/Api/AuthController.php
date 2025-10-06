<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'string|nullable',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email tidak dijumpai dalam sistem.'],
            ]);
        }

        // Verify password using RISDA custom hash service
        if (!$user->verifyPassword($request->password)) {
            throw ValidationException::withMessages([
                'password' => ['Kata laluan tidak sah.'],
            ]);
        }

        // Check if user is active
        if ($user->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akaun anda tidak aktif. Sila hubungi pentadbir sistem.',
                'status' => $user->status,
            ], 403);
        }

        // Create token for user
        $deviceName = $request->device_name ?? 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        // Load relationships for user data
        $user->load(['kumpulan', 'bahagian', 'stesen', 'staf']);

        return response()->json([
            'success' => true,
            'message' => 'Login berjaya',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_picture_url' => $user->profile_picture, // Return relative path only
                    'no_telefon' => $user->staf?->no_telefon ?? $user->no_telefon,
                    'jenis_organisasi' => $user->jenis_organisasi,
                    'organisasi_id' => $user->organisasi_id,
                    'kumpulan_id' => $user->kumpulan_id,
                    'status' => $user->status,
                    'bahagian' => $user->bahagian ? [
                        'id' => $user->bahagian->id,
                        'nama' => $user->bahagian->nama_bahagian,
                        'kod' => $user->bahagian->kod_bahagian,
                    ] : null,
                    'stesen' => $user->stesen ? [
                        'id' => $user->stesen->id,
                        'nama' => $user->stesen->nama_stesen,
                        'kod' => $user->stesen->kod_stesen,
                    ] : null,
                    'kumpulan' => $user->kumpulan ? [
                        'id' => $user->kumpulan->id,
                        'nama' => $user->kumpulan->nama_kumpulan,
                        'kebenaran_matrix' => $user->kumpulan->kebenaran_matrix,
                    ] : null,
                    'staf' => $user->staf ? [
                        'id' => $user->staf->id,
                        'no_pekerja' => $user->staf->no_pekerja,
                        'nama_penuh' => $user->staf->nama_penuh,
                        'no_kad_pengenalan' => $user->staf->no_kad_pengenalan,
                        'no_telefon' => $user->staf->no_telefon,
                        'email' => $user->staf->email,
                        'jawatan' => $user->staf->jawatan,
                        'jantina' => $user->staf->jantina,
                    ] : null,
                ],
            ],
        ], 200);
    }

    /**
     * Get authenticated user details
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $user = $request->user();

        // Load relationships
        $user->load(['kumpulan', 'bahagian', 'stesen', 'staf']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture_url' => $user->profile_picture, // Return relative path only
                'no_telefon' => $user->staf?->no_telefon ?? $user->no_telefon,
                'jenis_organisasi' => $user->jenis_organisasi,
                'organisasi_id' => $user->organisasi_id,
                'kumpulan_id' => $user->kumpulan_id,
                'status' => $user->status,
                'bahagian' => $user->bahagian ? [
                    'id' => $user->bahagian->id,
                    'nama' => $user->bahagian->nama_bahagian,
                    'kod' => $user->bahagian->kod_bahagian,
                ] : null,
                'stesen' => $user->stesen ? [
                    'id' => $user->stesen->id,
                    'nama' => $user->stesen->nama_stesen,
                    'kod' => $user->stesen->kod_stesen,
                ] : null,
                'kumpulan' => $user->kumpulan ? [
                    'id' => $user->kumpulan->id,
                    'nama' => $user->kumpulan->nama_kumpulan,
                    'kebenaran_matrix' => $user->kumpulan->kebenaran_matrix,
                ] : null,
                'staf' => $user->staf ? [
                    'id' => $user->staf->id,
                    'no_pekerja' => $user->staf->no_pekerja,
                    'nama_penuh' => $user->staf->nama_penuh,
                    'no_kad_pengenalan' => $user->staf->no_kad_pengenalan,
                    'no_telefon' => $user->staf->no_telefon,
                    'email' => $user->staf->email,
                    'jawatan' => $user->staf->jawatan,
                    'jantina' => $user->staf->jantina,
                ] : null,
            ],
        ], 200);
    }

    /**
     * Logout user (Revoke current token)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berjaya',
        ], 200);
    }

    /**
     * Logout from all devices (Revoke all tokens)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutAll(Request $request)
    {
        // Revoke all tokens for the user
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout dari semua peranti berjaya',
        ], 200);
    }
}
