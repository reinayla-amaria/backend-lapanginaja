<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Cek apakah user sudah mengaktifkan MFA
        $mfaEnabled = $user->twoFactorAuth()->exists();
        $qrCode = null;

        // Jika tombol "Aktifkan" diklik, generate QR Code
        if ($request->has('enable_mfa') && !$mfaEnabled) {
            // Membuat secret key baru dan QR Code otomatis
            $qrCode = $user->createTwoFactorAuth()->toQr();
        }

        return view('profile.edit', [
            'user' => $user,
            'mfaEnabled' => $mfaEnabled,
            'qrCode' => $qrCode, // Kirim QR Code ke tampilan web
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Matikan fitur MFA (Optional jika admin mau menonaktifkan)
     */
    public function disableMfa(Request $request): RedirectResponse
    {
        $request->user()->disableTwoFactorAuth();
        return Redirect::route('profile.edit')->with('status', 'mfa-disabled');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}