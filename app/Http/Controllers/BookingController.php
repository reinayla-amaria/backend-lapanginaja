<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Booking;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;
use App\Http\Controllers\Api\FcmController;

class BookingController extends Controller
{
    public function checkoutAPI(Request $request)
    {

        $request->validate([
        'lapangan_id' => 'required|integer|exists:lapangans,id',
        'tanggal_main' => 'required|date|after_or_equal:today',
        'jam_mulai' => 'required|date_format:H:i',
        'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        'total_harga' => 'required|numeric|min:0',
    ]);
        // 1. Simpan booking pake user_id (Sesuai database lu)
        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'lapangan_id' => $request->lapangan_id,
            'tanggal_main' => $request->tanggal_main,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'total_harga' => $request->total_harga,
            'status' => 'pending'
        ]);


        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $order_id = 'BOOK-' . $booking->id . '-' . time();

        // 2. Tarik data penyewa dari tabel User berdasarkan user_id
        $penyewa = $request->user();

        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => $booking->total_harga,
            ],
            'customer_details' => [
                // 3. Nama & Email dinamis buat resi Midtrans
                'first_name' => $penyewa ? $penyewa->name : 'Penyewa',
                'email' => $penyewa ? $penyewa->email : 'penyewa@lapanginaja.com',
                'phone' => $penyewa ? $penyewa->no_hp : '08000000000'
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
    'snap_token' => $snapToken,
    'booking_id' => $booking->id, // tambah ini
]);
    }


public function callbackAPI(Request $request)
{
    $serverKey = config('midtrans.server_key');
    $hashed = hash(
        "sha512",
        $request->order_id . $request->status_code . $request->gross_amount . $serverKey
    );

    if ($hashed == $request->signature_key) {

        $payment = Payment::where('transaction_id', $request->order_id)->first();

        if ($payment) {
            if (
                $request->transaction_status == 'capture' ||
                $request->transaction_status == 'settlement'
            ) {
                $payment->update([
                    'status'             => 'sukses',
                    'metode_pembayaran'  => $request->payment_type ?? 'Midtrans',
                ]);
                $payment->booking->update(['status' => 'dibayar']);

                // --- KIRIM FCM NOTIFICATION ---
                $booking  = $payment->booking->load(['lapangan', 'user']);
                $fcmToken = $booking->user->fcm_token ?? null;

                if ($fcmToken) {
                    FcmController::sendBookingNotification($fcmToken, [
                        'booking_id'        => (string) $booking->id,
                        'nama_lapangan'     => ($booking->lapangan->mitra->name ?? '') .
                                              ' - ' . ($booking->lapangan->nama_lapangan ?? ''),
                        'lokasi'            => $booking->lapangan->lokasi ?? '-',
                        'tanggal_main'      => $booking->tanggal_main,
                        'jam_mulai'         => \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i'),
                        'jam_selesai'       => \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i'),
                        'total_harga'       => (string) $booking->total_harga,
                        'user_name' => $booking->user->name ?? 'Penyewa',
                        'metode_pembayaran' => $request->payment_type ?? 'Midtrans',
                        'transaction_id'    => $request->order_id ?? '-',
                    ]);
                }
                // ------------------------------

            } elseif (
                $request->transaction_status == 'cancel' ||
                $request->transaction_status == 'deny' ||
                $request->transaction_status == 'expire'
            ) {
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

        $pemesanans = Booking::whereHas('lapangan', function ($q) use ($mitraId) {
            $q->where('mitra_id', $mitraId);
        })->whereDate('jam_mulai', $tanggalPilih)
            ->get();

        // Generate Slot Jam Operasional (Misal GOR buka jam 08:00 - 22:00)
        $slotJadwal = [];
        foreach ($lapangans as $lapangan) {
            for ($jam = 8; $jam <= 21; $jam++) {
                $jamMulai = sprintf('%02d:00', $jam);
                $jamSelesai = sprintf('%02d:00', $jam + 1);

                // Cek apakah slot jam ini ada di tabel Booking
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

    public function indexMitra(Request $request)
    {
        $mitraId = Auth::id();

        // Nangkep parameter dari URL
        $status = $request->query('status'); // All, pending, atau completed
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Tarik data booking khusus buat lapangan milik mitra ini
        $query = Booking::with(['user', 'lapangan'])
            ->whereHas('lapangan', function ($q) use ($mitraId) {
                $q->where('mitra_id', $mitraId);
            });

        // 1. Logic Filter Tabs Status
        if ($status == 'pending') {
            $query->where('status', 'pending');
        } elseif ($status == 'completed') {
            $query->whereIn('status', ['sukses', 'lunas', 'dibayar']);
        }

        // 2. Logic Filter Range Tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_main', [$startDate, $endDate]);
        }

        // Urutin dari yang paling baru
        $pesanans = $query->latest('created_at')->get();

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
                    $booking->user->no_hp ?? '-',
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

    public function updateJadwal(Request $request)
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
        $booking = Booking::where('lapangan_id', $request->lapangan_id)
            ->where('jam_mulai', $tanggalMulai)
            ->first();

        if ($request->status == 'maintenance') {
            if ($booking) {
                // Kalau slot udah dibooking orang beneran, tolak!
                if (in_array($booking->status, ['sukses', 'pending', 'dibayar'])) {
                    return back()->with('error', 'Gagal memblokir! Jadwal ini sudah dipesan oleh penyewa.');
                }
                // Kalau sebelumnya udah maintenance, ya biarin aja
                $booking->update(['status' => 'maintenance']);
            } else {
                // Bikin booking 'palsu' khusus buat ngeblokir lapangan pake user_id
               Booking::create([
                'lapangan_id' => $request->lapangan_id,
                'user_id' => Auth::id(),
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
   public function myBookings(Request $request)
{
    $bookings = Booking::with('lapangan.mitra')
        ->where('user_id', $request->user()->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($booking) {
            $namaVenue = $booking->lapangan->mitra->name ?? '';
            $namaLapangan = $booking->lapangan->nama_lapangan ?? 'Lapangan';
            
            return [
            'id' => $booking->id,
            'lapangan_id' => $booking->lapangan_id,
            'mitra_id' => $booking->lapangan->mitra_id ?? '',
            'mitra_name' => $booking->lapangan->mitra->name ?? '',
            'nama_lapangan' => $namaVenue . ' - ' . $namaLapangan,
            'tanggal_main' => $booking->tanggal_main,
            'jam_mulai' => $booking->jam_mulai,
            'jam_selesai' => $booking->jam_selesai,
            'total_harga' => $booking->total_harga,
            'status' => $booking->status,
];
        });

    return response()->json([
        'status' => 'success',
        'data' => $bookings,
    ]);
    
}
public function bookingDetail(Request $request, $id)
{
    $booking = Booking::with(['user', 'lapangan', 'lapangan.mitra'])
        ->find($id);

    if (!$booking || $booking->user_id !== $request->user()->id) {
        return response()->json([
            'status' => 'error',
            'message' => 'Tidak ditemukan'
        ], 404);
    }

    $payment = Payment::where('booking_id', $id)->first();

    return response()->json([
        'status' => 'success',
        'data' => [
            'booking' => $booking,
            'lapangan' => $booking->lapangan,
            'user' => $booking->user,   // 🔥 INI FIX UTAMA
            'payment' => $payment,
        ]
    ]);
}
public function index()
{
    return response()->json([
        'message' => 'Halaman transaksi admin'
    ]);
}
public function checkAvailability(Request $request, $id)
{
    $tanggal = $request->query('tanggal');
    
    $bookings = Booking::where('lapangan_id', $id)
        ->where('tanggal_main', $tanggal)
        ->whereIn('status', ['pending', 'dibayar'])
        ->get(['jam_mulai', 'jam_selesai', 'status']);

    return response()->json([
        'status' => 'success',
        'data' => $bookings
    ]);
}
}