<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Cek apakah data MFA ada di database
        $mfa = DB::table('two_factor_authentications')->where('user_id', $user->id)->first();
        $mfaEnabled = $mfa ? (bool)$mfa->enabled : false;
        $qrCode = null;

        // Logika generate QR (menggunakan text sementara untuk simulasi)
        if ($request->has('enable_mfa') && !$mfa) {
            $secret = bin2hex(random_bytes(10)); // Generate secret unik
            DB::table('two_factor_authentications')->insert([
                'user_id' => $user->id,
                'shared_secret' => $secret,
                'enabled' => true, // Langsung aktifkan
            ]);
            return Redirect::route('profile.edit')->with('status', 'mfa-enabled');
        }

        return view('profile.edit', [
            'user' => $user,
            'mfaEnabled' => $mfaEnabled,
            'qrCode' => $qrCode, // Bisa diisi library QR generator jika sudah ada
        ]);
    }

    public function disableMfa(Request $request): RedirectResponse
    {
        // Hapus data MFA dari database
        DB::table('two_factor_authentications')->where('user_id', $request->user()->id)->delete();
        
        return Redirect::route('profile.edit')->with('status', 'mfa-disabled');
    }
    
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