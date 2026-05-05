<?php

namespace App\Http\Controllers;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
    public function jadwal()
    {
        $mitraId = auth()->id();
        // Tarik lapangan + bookingan hari ini
        $lapangans = \App\Models\Lapangan::where('mitra_id', $mitraId)
            ->with([
                'bookings' => function ($q) {
                    $q->whereDate('tanggal_main', now())
                        ->where('status', '!=', 'batal');
                },
                'bookings.user'
            ])
            ->get();

        return view('mitra.jadwal.index', compact('lapangans'));
    }
    public function indexMitra()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $bookings = Booking::with(['user', 'lapangan.mitra'])->latest()->get();
        } else if ($user->role === 'mitra') {
            $bookings = Booking::with(['user', 'lapangan'])
                ->whereHas('lapangan', function ($query) use ($user) {
                    $query->where('mitra_id', $user->id);
                })
                ->latest()
                ->get();
        } else {
            abort(403, 'Akses Ditolak');
        }

        return view('transaksi.index', compact('bookings'));
    }
    public function exportCSV()
    {
        $bookings = Booking::with(['user', 'lapangan.mitra'])->latest()->get();

        $response = new StreamedResponse(function () use ($bookings) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID Pesanan',
                'Nama Penyewa',
                'No HP',
                'Nama GOR',
                'Nama Lapangan',
                'Tanggal Main',
                'Jam Main',
                'Total Bayar (Rp)',
                'Status'
            ]);

            foreach ($bookings as $booking) {
                fputcsv($handle, [
                    '#' . $booking->id,
                    $booking->user->name ?? 'Penyewa',
                    $booking->user->phone ?? '-',
                    $booking->lapangan->mitra->name ?? 'GOR Unknown',
                    $booking->lapangan->nama_lapangan ?? 'Arena',
                    $booking->tanggal_main,
                    $booking->jam_mulai . ' - ' . $booking->jam_selesai,
                    $booking->total_harga ?? 0,
                    strtoupper($booking->status)
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="laporan_transaksi_lapanginaja.csv"');

        return $response;
    }

}