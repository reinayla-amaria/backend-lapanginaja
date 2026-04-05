<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;

class BookingController extends Controller
{
    public function checkoutAPI(Request $request)
    {

        $booking = Booking::create([
            'user_id' => $request->user_id,
            'lapangan_id' => $request->lapangan_id,
            'tanggal_main' => $request->tanggal_main,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'total_harga' => $request->total_harga,
            'status' => 'pending'
        ]);

        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $order_id = 'BOOK-' . $booking->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => $booking->total_harga,
            ],
            'customer_details' => [
                'first_name' => 'Penyewa',
                'email' => 'penyewa@example.com',
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        Payment::create([
            'booking_id' => $booking->id,
            'transaction_id' => $order_id,
            'snap_token' => $snapToken,
            'jumlah_bayar' => $booking->total_harga,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => 'sukses',
            'pesan' => 'Booking berhasil dibuat!',
            'snap_token' => $snapToken
        ]);
    }

    public function callbackAPI(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {

            $payment = Payment::where('transaction_id', $request->order_id)->first();

            if ($payment) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {

                    $payment->update(['status' => 'sukses']);

                    $payment->booking->update(['status' => 'dibayar']);

                } elseif ($request->transaction_status == 'cancel' || $request->transaction_status == 'deny' || $request->transaction_status == 'expire') {

                    $payment->update(['status' => 'gagal']);
                    $payment->booking->update(['status' => 'batal']);

                }
            }
        }

        return response()->json(['message' => 'Laporan diterima!']);
    }
    public function indexMitra()
    {
        $bookings = Booking::with(['lapangan', 'user', 'payment'])
            ->whereHas('lapangan', function ($query) {
                $query->where('mitra_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('transaksi.index', compact('bookings'));
    }
}