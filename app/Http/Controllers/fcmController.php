<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmController extends Controller
{
    // -------------------------------------------------------
    // Simpan FCM token dari Flutter ke database
    // POST /api/fcm-token
    // -------------------------------------------------------
    public function saveToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'FCM token berhasil disimpan',
        ]);
    }

    // -------------------------------------------------------
    // Kirim push notification ke device tertentu
    // Dipanggil dari BookingController::callbackAPI()
    // -------------------------------------------------------
    public static function sendBookingNotification(string $fcmToken, array $data): void
    {
        try {
            $factory = (new Factory)->withServiceAccount(
                storage_path('app/firebase-credentials.json')
            );

            $messaging = $factory->createMessaging();

            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification(
                    Notification::create(
                        'Pembayaran Berhasil! 🎉',
                        'Booking ' . $data['nama_lapangan'] . ' pada ' .
                        $data['tanggal_main'] . ' pukul ' .
                        $data['jam_mulai'] . ' - ' . $data['jam_selesai'] .
                        ' telah dikonfirmasi.'
                    )
                )
                ->withData([
                    'booking_id'          => $data['booking_id'],
                    'nama_lapangan'       => $data['nama_lapangan'],
                    'lokasi'              => $data['lokasi'],
                    'tanggal_main'        => $data['tanggal_main'],
                    'jam_mulai'           => $data['jam_mulai'],
                    'jam_selesai'         => $data['jam_selesai'],
                    'total_harga'         => $data['total_harga'],
                    'user_name'           => $data['user_name'],
                    'metode_pembayaran'   => $data['metode_pembayaran'],
                    'transaction_id'      => $data['transaction_id'],
                    'type'                => 'booking_success',
                ]);

            $messaging->send($message);
        } catch (\Throwable $e) {
            \Log::error('FCM Error: ' . $e->getMessage());
        }
    }
}