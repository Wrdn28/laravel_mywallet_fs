<?php

namespace App\Http\Controllers;

use App\Enums\KategoriTransaksi;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AiSuggestionController extends Controller
{
    public function suggest(): JsonResponse
    {
        $userId = Auth::id();

        $recent  = Transaksi::where('user_id', $userId)->where('tanggal', '>=', now()->subDays(30))->get();
        $allTime = Transaksi::where('user_id', $userId)->get();

        $pemasukan30   = $recent->where('tipe', 'pemasukan')->sum('jumlah');
        $pengeluaran30 = $recent->where('tipe', 'pengeluaran')->sum('jumlah');
        $saldo30       = $pemasukan30 - $pengeluaran30;
        $saldoTotal    = $allTime->where('tipe', 'pemasukan')->sum('jumlah')
                       - $allTime->where('tipe', 'pengeluaran')->sum('jumlah');

        $jumlahTransaksi = $recent->count();
        $rasio           = $pemasukan30 > 0 ? round(($pengeluaran30 / $pemasukan30) * 100) : 0;
        $kategoriData    = $this->analyzeKategori($recent->where('tipe', 'pengeluaran'));

        $suggestions = $this->generateSuggestions(
            pemasukan: $pemasukan30,
            pengeluaran: $pengeluaran30,
            saldo: $saldo30,
            saldoTotal: $saldoTotal,
            rasio: $rasio,
            jumlahTransaksi: $jumlahTransaksi,
            kategoriData: $kategoriData,
        );

        return response()->json([
            'suggestions' => $suggestions,
            'stats'       => compact('pemasukan30', 'pengeluaran30', 'saldo30', 'rasio', 'jumlahTransaksi'),
        ]);
    }

    private function generateSuggestions(
        int $pemasukan, int $pengeluaran, int $saldo,
        int $saldoTotal, int $rasio, int $jumlahTransaksi,
        array $kategoriData
    ): array {
        $suggestions = [];

        // Tidak ada data sama sekali
        if ($jumlahTransaksi === 0) {
            return [[
                'type'    => 'info',
                'icon'    => 'lightbulb',
                'title'   => 'Mulai Catat Keuangan Anda',
                'message' => 'Belum ada transaksi dalam 30 hari terakhir. Mulai catat pemasukan dan pengeluaran Anda untuk mendapatkan analisis keuangan yang akurat.',
                'action'  => 'Tambah Transaksi',
            ]];
        }

        // Pengeluaran ada tapi tidak ada kategori
        if ($pengeluaran > 0 && empty($kategoriData)) {
            $suggestions[] = [
                'type'    => 'warning',
                'icon'    => 'label_off',
                'title'   => 'Transaksi Belum Berkategori',
                'message' => 'Pengeluaran Anda belum memiliki kategori. Tambahkan kategori saat mencatat transaksi agar analisis keuangan lebih akurat dan AI dapat memberikan rekomendasi yang lebih personal.',
                'action'  => null,
            ];
        }

        // Saldo negatif
        if ($saldo < 0) {
            $suggestions[] = [
                'type'    => 'danger',
                'icon'    => 'warning',
                'title'   => 'Pengeluaran Melebihi Pemasukan!',
                'message' => 'Dalam 30 hari terakhir, pengeluaran Anda melebihi pemasukan sebesar Rp ' . number_format(abs($saldo), 0, ',', '.') . '. Segera evaluasi pengeluaran yang tidak perlu dan cari sumber pemasukan tambahan.',
                'action'  => null,
            ];
        }

        // Rasio pengeluaran tinggi
        if ($rasio > 80 && $pemasukan > 0) {
            $suggestions[] = [
                'type'    => 'warning',
                'icon'    => 'trending_down',
                'title'   => 'Pengeluaran Terlalu Tinggi',
                'message' => "{$rasio}% dari pemasukan Anda habis untuk pengeluaran bulan ini. Idealnya pengeluaran tidak melebihi 70% dari pemasukan. Coba terapkan aturan 50/30/20: 50% kebutuhan, 30% keinginan, 20% tabungan.",
                'action'  => null,
            ];
        }

        // Rasio sehat
        if ($rasio > 0 && $rasio <= 60) {
            $suggestions[] = [
                'type'    => 'success',
                'icon'    => 'thumb_up',
                'title'   => 'Keuangan Anda Sehat!',
                'message' => "Hanya {$rasio}% pemasukan yang digunakan untuk pengeluaran bulan ini. Pertahankan kebiasaan ini dan pertimbangkan menginvestasikan sisa dana ke reksa dana atau deposito.",
                'action'  => null,
            ];
        }

        // Rekomendasi spesifik per kategori terbesar
        if (!empty($kategoriData)) {
            $topKey   = array_key_first($kategoriData);
            $topTotal = $kategoriData[$topKey];
            $topLabel = KategoriTransaksi::label($topKey);
            $topIcon  = KategoriTransaksi::icon($topKey);
            $topFmt   = 'Rp ' . number_format($topTotal, 0, ',', '.');

            $tipsByKategori = [
                'makanan'      => "Pengeluaran {$topLabel} Anda bulan ini mencapai {$topFmt}. Coba masak di rumah 2–3x seminggu — bisa menghemat hingga 30% biaya makan bulanan.",
                'transportasi' => "Pengeluaran {$topLabel} Anda bulan ini {$topFmt}. Pertimbangkan carpooling, transportasi umum, atau bersepeda untuk rute pendek agar lebih hemat.",
                'belanja'      => "Pengeluaran {$topLabel} Anda bulan ini {$topFmt}. Buat daftar belanja sebelum pergi dan terapkan aturan 24 jam sebelum membeli barang non-esensial.",
                'tagihan'      => "Tagihan Anda bulan ini {$topFmt}. Cek apakah ada paket internet atau listrik yang lebih hemat, dan matikan perangkat yang tidak digunakan.",
                'hiburan'      => "Pengeluaran {$topLabel} Anda bulan ini {$topFmt}. Coba batasi langganan streaming ke 1–2 platform saja dan manfaatkan konten gratis sebagai alternatif.",
                'kesehatan'    => "Pengeluaran {$topLabel} Anda bulan ini {$topFmt}. Pastikan Anda memiliki asuransi kesehatan aktif untuk menekan biaya tak terduga di masa depan.",
                'pendidikan'   => "Investasi {$topLabel} Anda bulan ini {$topFmt}. Ini investasi jangka panjang yang sangat baik — pastikan tetap sesuai dengan anggaran bulanan Anda.",
                'rumah'        => "Pengeluaran {$topLabel} Anda bulan ini {$topFmt}. Pastikan biaya sewa/rumah tidak melebihi 30% dari total pemasukan Anda.",
                'lainnya_out'  => "Pengeluaran kategori lainnya Anda bulan ini {$topFmt}. Coba rinci pengeluaran ini ke kategori yang lebih spesifik agar lebih mudah dianalisis.",
            ];

            $tipMessage = $tipsByKategori[$topKey]
                ?? "Kategori \"{$topLabel}\" mendominasi pengeluaran Anda bulan ini ({$topFmt}). Buat anggaran khusus untuk kategori ini agar lebih terkontrol.";

            $suggestions[] = [
                'type'    => 'info',
                'icon'    => $topIcon,
                'title'   => "Rekomendasi: {$topLabel}",
                'message' => $tipMessage,
                'action'  => null,
            ];

            // Distribusi top 3 kategori
            if (count($kategoriData) >= 3) {
                $keys = array_keys($kategoriData);
                $secondLbl = KategoriTransaksi::label($keys[1]);
                $thirdLbl  = KategoriTransaksi::label($keys[2]);
                $suggestions[] = [
                    'type'    => 'info',
                    'icon'    => 'pie_chart',
                    'title'   => 'Distribusi Pengeluaran',
                    'message' => "3 kategori terbesar Anda: {$topLabel}, {$secondLbl}, dan {$thirdLbl}. Pantau ketiga kategori ini secara rutin untuk menjaga keuangan tetap seimbang.",
                    'action'  => null,
                ];
            }
        }

        // Saran investasi bertingkat
        if ($saldoTotal > 10000000) {
            $suggestions[] = [
                'type'    => 'success',
                'icon'    => 'savings',
                'title'   => 'Saatnya Mulai Investasi',
                'message' => 'Saldo total Anda sudah di atas Rp 10 juta (Rp ' . number_format($saldoTotal, 0, ',', '.') . '). Pertimbangkan mengalokasikan 10–20% ke reksa dana, saham, atau deposito untuk melawan inflasi.',
                'action'  => null,
            ];
        } elseif ($saldoTotal > 5000000) {
            $suggestions[] = [
                'type'    => 'success',
                'icon'    => 'savings',
                'title'   => 'Pertimbangkan Investasi',
                'message' => 'Saldo total Anda sudah cukup besar (Rp ' . number_format($saldoTotal, 0, ',', '.') . '). Mulai dengan reksa dana pasar uang sebagai langkah awal investasi yang aman.',
                'action'  => null,
            ];
        }

        // Transaksi sedikit
        if ($jumlahTransaksi < 5) {
            $suggestions[] = [
                'type'    => 'info',
                'icon'    => 'edit_note',
                'title'   => 'Catat Lebih Rutin',
                'message' => 'Anda hanya mencatat ' . $jumlahTransaksi . ' transaksi dalam 30 hari terakhir. Pencatatan harian membantu Anda memahami pola keuangan dan mendapatkan rekomendasi yang lebih akurat.',
                'action'  => 'Tambah Transaksi',
            ];
        }

        // Tidak ada pemasukan
        if ($pemasukan === 0 && $pengeluaran > 0) {
            $suggestions[] = [
                'type'    => 'warning',
                'icon'    => 'account_balance_wallet',
                'title'   => 'Belum Ada Pemasukan Tercatat',
                'message' => 'Anda memiliki pengeluaran tapi belum mencatat pemasukan bulan ini. Catat semua sumber pemasukan agar analisis keuangan Anda lebih akurat.',
                'action'  => 'Catat Pemasukan',
            ];
        }

        if (empty($suggestions)) {
            $suggestions[] = [
                'type'    => 'info',
                'icon'    => 'auto_awesome',
                'title'   => 'Keuangan Terpantau',
                'message' => 'Keuangan Anda dalam kondisi normal. Terus pertahankan kebiasaan mencatat transaksi secara rutin untuk analisis yang lebih mendalam.',
                'action'  => null,
            ];
        }

        return $suggestions;
    }

    /**
     * Analisis kategori dari field kategori (bukan keyword matching).
     */
    private function analyzeKategori($transactions): array
    {
        if ($transactions->isEmpty()) return [];

        $totals = [];
        foreach ($transactions as $t) {
            if (!empty($t->kategori)) {
                $totals[$t->kategori] = ($totals[$t->kategori] ?? 0) + $t->jumlah;
            }
        }

        arsort($totals);
        return $totals;
    }
}
