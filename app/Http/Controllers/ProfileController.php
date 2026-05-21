<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProfileController extends Controller
{
public function edit(Request $request): View
{
    $user = $request->user();
    
    // Jika tombol di-klik, buat secret-nya
    if ($request->has('enable_mfa')) {
        $mfa = DB::table('two_factor_authentications')->where('user_id', $user->id)->first();
        if (!$mfa) {
            $secret = bin2hex(random_bytes(10));
            DB::table('two_factor_authentications')->insert([
                'user_id' => $user->id,
                'shared_secret' => $secret,
                'enabled' => true,
            ]);
        }
    }

    // Ambil data terbaru dari DB
    $mfa = DB::table('two_factor_authentications')->where('user_id', $user->id)->first();
    $mfaEnabled = $mfa ? (bool)$mfa->enabled : false;
    $qrCode = null;

    if ($mfaEnabled && $mfa) {
        $otpUrl = "otpauth://totp/LapanginAja:{$user->email}?secret={$mfa->shared_secret}";
        $qrCode = QrCode::size(200)->generate($otpUrl);
    }

    return view('profile.edit', [
        'user' => $user,
        'mfaEnabled' => $mfaEnabled,
        'qrCode' => $qrCode,
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