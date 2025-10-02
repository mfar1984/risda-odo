<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    /**
     * Upload or update profile picture
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,jpg,png|max:2048', // Max 2MB
        ], [
            'profile_picture.required' => 'Gambar profil diperlukan',
            'profile_picture.image' => 'Fail mesti dalam format gambar',
            'profile_picture.mimes' => 'Gambar mesti dalam format: jpeg, jpg, png',
            'profile_picture.max' => 'Saiz gambar maksimum 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new profile picture
        $file = $request->file('profile_picture');
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('profile_pictures', $filename, 'public');

        // Update user profile picture path
        $user->profile_picture = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Gambar profil berjaya dikemaskini',
            'data' => [
                'profile_picture' => $path,
                'profile_picture_url' => Storage::url($path),
            ],
        ], 200);
    }

    /**
     * Delete profile picture
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProfilePicture(Request $request)
    {
        $user = $request->user();

        if (!$user->profile_picture) {
            return response()->json([
                'success' => false,
                'message' => 'Tiada gambar profil untuk dipadam',
            ], 404);
        }

        // Delete file from storage
        Storage::disk('public')->delete($user->profile_picture);

        // Update user record
        $user->profile_picture = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Gambar profil berjaya dipadam',
        ], 200);
    }

    /**
     * Change password
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ], [
            'current_password.required' => 'Kata laluan semasa diperlukan',
            'new_password.required' => 'Kata laluan baru diperlukan',
            'new_password.min' => 'Kata laluan baru mesti sekurang-kurangnya 8 aksara',
            'new_password.confirmed' => 'Pengesahan kata laluan tidak sepadan',
            'new_password_confirmation.required' => 'Pengesahan kata laluan diperlukan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Verify current password using RISDA custom hash
        if (!$user->verifyPassword($request->current_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata laluan semasa tidak sah',
                'errors' => [
                    'current_password' => ['Kata laluan semasa tidak sah'],
                ],
            ], 422);
        }

        // Update password (will be hashed automatically via User model mutator)
        $user->password = $request->new_password;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kata laluan berjaya dikemaskini',
        ], 200);
    }
}
