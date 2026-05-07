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
        $mitraId = Auth::id();
        $tanggalPilih = request('tanggal', \Carbon\Carbon::today()->format('Y-m-d'));

        // Ambil lapangan milik Mitra
        $lapangans = \App\Models\Lapangan::where('mitra_id', $mitraId)->get();

        // NAH INI YANG DIGANTI BANG: \App\Models\Pemesanan jadi \App\Models\Booking
        $pemesanans = \App\Models\Booking::whereHas('lapangan', function ($q) use ($mitraId) {
            $q->where('mitra_id', $mitraId);
        })->whereDate('jam_mulai', $tanggalPilih)
            ->get();

        // Generate Slot Jam Operasional (Misal GOR buka jam 08:00 - 22:00)
        $slotJadwal = [];
        foreach ($lapangans as $lapangan) {
            for ($jam = 8; $jam <= 21; $jam++) {
                $jamMulai = sprintf('%02d:00', $jam);
                $jamSelesai = sprintf('%02d:00', $jam + 1);

                // Cek apakah slot jam ini ada di tabel Pemesanan (Booking)
                $booking = $pemesanans->where('lapangan_id', $lapangan->id)
                    ->filter(function ($b) use ($jamMulai) {
                        return \Carbon\Carbon::parse($b->jam_mulai)->format('H:i') == $jamMulai;
                    })->first();

                $slotJadwal[] = (object) [
                    'id_slot' => $lapangan->id . '-' . $jam,
                    'lapangan' => $lapangan,
                    'jam' => $jamMulai . ' - ' . $jamSelesai,
                    'harga' => $lapangan->harga_per_jam,
                    'status' => $booking ? ($booking->status == 'batal' ? 'siap dipakai' : 'dibooking') : 'siap dipakai',
                    'booking_id' => $booking ? $booking->id : null
                ];
            }
        }

        return view('mitra.jadwal.index', compact('slotJadwal', 'tanggalPilih', 'lapangans'));
    }
    public function indexMitra(\Illuminate\Http\Request $request)
    {
        $mitraId = \Illuminate\Support\Facades\Auth::id();

        // Nangkep parameter dari URL
        $status = $request->query('status'); // All, pending, atau completed
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Tarik data booking khusus buat lapangan milik mitra ini
        $query = \App\Models\Booking::with(['user', 'lapangan'])
            ->whereHas('lapangan', function ($q) use ($mitraId) {
                $q->where('mitra_id', $mitraId);
            });

        // 1. Logic Filter Tabs Status
        if ($status == 'pending') {
            $query->where('status', 'pending');
        } elseif ($status == 'completed') {
            $query->whereIn('status', ['sukses', 'lunas']); // Disesuaikan sama status di DB lu
        }

        // 2. Logic Filter Range Tanggal (Bisa cari masa lalu)
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_main', [$startDate, $endDate]);
        }

        // Urutin dari yang paling baru
        $pesanans = $query->latest('created_at')->get();

        // Hapus kata 'mitra.' nya bang
        return view('transaksi.index', compact('pesanans', 'status', 'startDate', 'endDate'));
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
    public function updateJadwal(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'lapangan_id' => 'required',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'status' => 'required'
        ]);

        // Gabungin tanggal dan jam sesuai format database (datetime)
        $tanggalMulai = $request->tanggal . ' ' . $request->jam_mulai . ':00';
        $tanggalSelesai = $request->tanggal . ' ' . $request->jam_selesai . ':00';

        // Cari apakah di slot ini udah ada bookingan
        $booking = \App\Models\Booking::where('lapangan_id', $request->lapangan_id)
            ->where('jam_mulai', $tanggalMulai)
            ->first();

        if ($request->status == 'maintenance') {
            if ($booking) {
                // Kalau slot udah dibooking orang beneran, tolak!
                if ($booking->status == 'sukses' || $booking->status == 'pending') {
                    return back()->with('error', 'Gagal memblokir! Jadwal ini sudah dipesan oleh penyewa.');
                }
                // Kalau sebelumnya udah maintenance, ya biarin aja
                $booking->update(['status' => 'maintenance']);
            } else {
                // Bikin booking 'palsu' khusus buat ngeblokir lapangan
                \App\Models\Booking::create([
                    'lapangan_id' => $request->lapangan_id,
                    'penyewa_id' => \Illuminate\Support\Facades\Auth::id(), // Pake ID Mitra sebagai penanda
                    'jam_mulai' => $tanggalMulai,
                    'jam_selesai' => $tanggalSelesai,
                    'status' => 'maintenance',
                    'total_harga' => 0
                ]);
            }
        } else {
            // Kalau Mitra milih "Siap Dipakai", kita hapus aja blokiran maintenance-nya
            if ($booking && $booking->status == 'maintenance') {
                $booking->delete();
            }
        }

        return back()->with('success', 'Status jadwal berhasil diperbarui!');
    }
}