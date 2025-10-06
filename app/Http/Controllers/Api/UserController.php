<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get list of users available for assignment/participants
     * Filtered based on current user's permissions
     */
    public function list(Request $request)
    {
        $currentUser = Auth::user();
        
        $query = User::select('id', 'name', 'email', 'jenis_organisasi', 'organisasi_id');
        
        // Filter based on user's role
        if ($currentUser->jenis_organisasi === 'semua') {
            // Administrator can see all users
            $query->orderBy('name');
        } elseif ($currentUser->jenis_organisasi === 'bahagian') {
            // Bahagian staff can only see users in same bahagian
            $query->where(function ($q) use ($currentUser) {
                $q->where('jenis_organisasi', 'bahagian')
                  ->where('organisasi_id', $currentUser->organisasi_id);
            })
            ->orWhere('jenis_organisasi', 'semua') // Can also see admins
            ->orderBy('name');
        } elseif ($currentUser->jenis_organisasi === 'stesen') {
            // Stesen staff can only see users in same stesen
            $query->where(function ($q) use ($currentUser) {
                $q->where('jenis_organisasi', 'stesen')
                  ->where('organisasi_id', $currentUser->organisasi_id);
            })
            ->orWhere('jenis_organisasi', 'semua') // Can also see admins
            ->orderBy('name');
        }
        
        $users = $query->get();
        
        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }
}