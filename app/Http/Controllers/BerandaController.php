<?php

namespace App\Http\Controllers;

use App\Models\Rencana;
use App\Models\SystemConfig;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BerandaController extends Controller
{
    // -------------------------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------------------------

    public function index(Request $request): View|RedirectResponse
    {
        $appName = SystemConfig::getValue('app_name', 'DOMPETKU');
        $userId  = Auth::id();
        $query   = $this->buildQuery($request);

        $riwayat          = (clone $query)->orderByDesc('tanggal')->orderByDesc('id')->get();
        $totalPemasukan   = $riwayat->where('tipe', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = $riwayat->where('tipe', 'pengeluaran')->sum('jumlah');
        $saldo            = $totalPemasukan - $totalPengeluaran;

        // Chart totals
        $chartData        = (clone $query)->selectRaw("
            SUM(CASE WHEN tipe = 'pemasukan'  THEN jumlah ELSE 0 END) as total_pemasukan,
            SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran
        ")->first();
        $chartPemasukan   = (int) ($chartData->total_pemasukan  ?? 0);
        $chartPengeluaran = (int) ($chartData->total_pengeluaran ?? 0);

        // Analisis per kategori (pengeluaran)
        $kategoriStats = (clone $query)
            ->where('tipe', 'pengeluaran')
            ->whereNotNull('kategori')
            ->selectRaw('kategori, SUM(jumlah) as total, COUNT(*) as jumlah_transaksi')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        // ── Notifikasi: budget over + goal tercapai ──
        // Ambil daftar notif yang sudah di-dismiss dari session
        $dismissedNotifs = session('dismissed_notifs', []);
        $notifications   = collect();

        // Budget yang melebihi alokasi (hitung SETELAH budget dibuat)
        $budgets = Rencana::where('user_id', $userId)->where('tipe', 'anggaran')->get();
        foreach ($budgets as $b) {
            $aktual = Transaksi::where('user_id', $userId)
                ->where('tipe', 'pengeluaran')
                ->where('kategori', $b->kategori)
                ->where('created_at', '>', $b->created_at)
                ->whereMonth('tanggal', $b->created_at->month)
                ->whereYear('tanggal', $b->created_at->year)
                ->sum('jumlah');

            if ($aktual >= $b->target) {
                $key = 'budget_' . $b->id . '_' . $b->updated_at->timestamp;
                if (!in_array($key, $dismissedNotifs)) {
                    $notifications->push([
                        'key'     => $key,
                        'type'    => 'budget',
                        'level'   => $aktual > $b->target ? 'danger' : 'warning',
                        'icon'    => 'payments',
                        'message' => $aktual > $b->target
                            ? 'Anggaran "' . $b->nama . '" sudah melebihi batas!'
                            : 'Anggaran "' . $b->nama . '" sudah habis terpakai.',
                        'link'    => route('rencana'),
                    ]);
                }
            }
        }

        // Goal yang tercapai
        $goals = Rencana::where('user_id', $userId)->where('tipe', 'tabungan')->get();
        foreach ($goals as $g) {
            if ($g->is_selesai) {
                $key = 'goal_' . $g->id;
                if (!in_array($key, $dismissedNotifs)) {
                    $notifications->push([
                        'key'     => $key,
                        'type'    => 'goal',
                        'level'   => 'success',
                        'icon'    => 'workspace_premium',
                        'message' => 'Target "' . $g->nama . '" telah tercapai!',
                        'link'    => route('rencana'),
                    ]);
                }
            }
        }

        // Goals aktif untuk dropdown di form transaksi
        $activeGoals = Rencana::where('user_id', $userId)
            ->where('tipe', 'tabungan')
            ->where('terkumpul', '<', \Illuminate\Support\Facades\DB::raw('target'))
            ->orderBy('nama')
            ->get(['id', 'nama', 'target', 'terkumpul']);

        return view('beranda', compact(
            'riwayat', 'totalPemasukan', 'totalPengeluaran', 'saldo',
            'chartPemasukan', 'chartPengeluaran', 'appName', 'kategoriStats',
            'notifications', 'activeGoals',
        ));
    }

    // -------------------------------------------------------------------------
    // Dismiss Notification
    // -------------------------------------------------------------------------

    public function dismissNotif(Request $request): \Illuminate\Http\JsonResponse
    {
        $key      = $request->input('key');
        $all      = $request->boolean('all', false);
        $keys     = $request->input('keys', []);
        $dismissed = session('dismissed_notifs', []);

        if ($all && !empty($keys)) {
            $dismissed = array_unique(array_merge($dismissed, $keys));
        } elseif ($key) {
            $dismissed[] = $key;
            $dismissed   = array_unique($dismissed);
        }

        // Batasi maksimal 200 entry agar session tidak membengkak
        if (count($dismissed) > 200) {
            $dismissed = array_slice($dismissed, -200);
        }

        session(['dismissed_notifs' => $dismissed]);

        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------------------------
    // Store (add)
    // -------------------------------------------------------------------------

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tipe'        => 'required|in:pemasukan,pengeluaran',
            'kategori'    => 'nullable|string|max:50',
            'jumlah'      => 'required|integer|min:1',
            'deskripsi'   => 'nullable|string|max:255',
            'tanggal'     => 'required|date',
            'goal_id'     => 'nullable|integer|exists:rencana,id',
            'is_tabungan' => 'nullable|in:0,1',
        ]);

        $isTabungan = ($request->input('is_tabungan') === '1');

        // Tabungan wajib punya goal_id
        if ($isTabungan && empty($validated['goal_id'])) {
            return back()->withErrors(['goal_id' => 'Pilih target tabungan terlebih dahulu.'])->withInput();
        }

        // Tabungan tidak butuh kategori
        if (!$isTabungan && empty($validated['kategori'])) {
            return back()->withErrors(['kategori' => 'Kategori wajib dipilih.'])->withInput();
        }

        Transaksi::create([
            'user_id'   => Auth::id(),
            'tipe'      => $validated['tipe'],   // pengeluaran untuk tabungan
            'kategori'  => $isTabungan ? null : $validated['kategori'],
            'jumlah'    => $validated['jumlah'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal'   => $validated['tanggal'],
        ]);

        // Tambah dana ke goal jika tabungan
        if ($isTabungan && !empty($validated['goal_id'])) {
            $goal = Rencana::where('id', $validated['goal_id'])
                ->where('user_id', Auth::id())
                ->where('tipe', 'tabungan')
                ->first();

            if ($goal) {
                $goal->increment('terkumpul', $validated['jumlah']);
                $msg = $goal->fresh()->is_selesai
                    ? 'Target "' . $goal->nama . '" telah tercapai!'
                    : 'Dana Rp ' . number_format($validated['jumlah'], 0, ',', '.') . ' berhasil ditambahkan ke "' . $goal->nama . '"!';
                return redirect()->route('beranda', $this->filterParams($request))
                    ->with('success', $msg);
            }
        }

        return redirect()->route('beranda', $this->filterParams($request))
            ->with('success', 'Transaksi berhasil disimpan!');
    }

    // -------------------------------------------------------------------------
    // Update (edit)
    // -------------------------------------------------------------------------

    public function update(Request $request, Transaksi $transaksi): RedirectResponse
    {
        // Ensure the transaction belongs to the authenticated user
        abort_if($transaksi->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'tipe'      => 'required|in:pemasukan,pengeluaran',
            'kategori'  => 'required|string|max:50',
            'jumlah'    => 'required|integer|min:1',
            'deskripsi' => 'nullable|string|max:255',
            'tanggal'   => 'required|date',
        ]);

        $transaksi->update([
            'tipe'      => strtolower($validated['tipe']),
            'kategori'  => $validated['kategori'],
            'jumlah'    => $validated['jumlah'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal'   => $validated['tanggal'],
        ]);

        return redirect()->route('beranda', $this->filterParams($request))
            ->with('success', 'Transaksi berhasil diperbarui!');
    }

    // -------------------------------------------------------------------------
    // Destroy (delete)
    // -------------------------------------------------------------------------

    public function destroy(Request $request, Transaksi $transaksi): RedirectResponse
    {
        abort_if($transaksi->user_id !== Auth::id(), 403);

        $transaksi->delete();

        return redirect()->route('beranda', $this->filterParams($request))
            ->with('success', 'Transaksi berhasil dihapus!');
    }

    // -------------------------------------------------------------------------
    // Export
    // -------------------------------------------------------------------------

    public function export(Request $request, string $type): Response
    {
        abort_if(!in_array($type, ['csv', 'excel']), 404);

        $data = $this->buildQuery($request)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $periode  = $request->get('periode', 'all');
        $filename = 'laporan-keuangan-' . $periode . '-' . now()->format('Y-m-d');

        if ($type === 'csv') {
            return $this->exportCsv($data, $filename);
        }

        return $this->exportExcel($data, $filename);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Build an Eloquent query with the active period filter applied.
     */
    private function buildQuery(Request $request)
    {
        $userId  = Auth::id();
        $periode = $request->get('periode', 'all');

        $query = Transaksi::where('user_id', $userId);

        if ($periode === 'week') {
            $query->whereBetween('tanggal', [now()->subWeek(), now()]);
        } elseif ($periode === 'month') {
            $query->whereBetween('tanggal', [now()->subMonth(), now()]);
        } elseif ($periode === 'custom') {
            $start = $request->get('start_date');
            $end   = $request->get('end_date');
            if ($start && $end) {
                $query->whereBetween('tanggal', [$start, $end]);
            }
        }

        return $query;
    }

    /**
     * Return only the filter-related query parameters for redirects.
     */
    private function filterParams(Request $request): array
    {
        return $request->only(['periode', 'start_date', 'end_date']);
    }

    private function exportCsv($data, string $filename): Response
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Tanggal', 'Tipe', 'Kategori', 'Deskripsi', 'Jumlah']);

        foreach ($data as $row) {
            fputcsv($output, [
                $row->tanggal->format('Y-m-d'),
                $row->tipe === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran',
                \App\Enums\KategoriTransaksi::label($row->kategori ?? ''),
                $row->deskripsi,
                $row->jumlah,
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ]);
    }

    private function exportExcel($data, string $filename): Response
    {
        $html  = "<table border='1'>";
        $html .= "<tr><th>Tanggal</th><th>Tipe</th><th>Kategori</th><th>Deskripsi</th><th>Jumlah</th></tr>";

        foreach ($data as $row) {
            $tipe     = $row->tipe === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran';
            $kategori = \App\Enums\KategoriTransaksi::label($row->kategori ?? '');
            $html .= "<tr>
                <td>{$row->tanggal->format('Y-m-d')}</td>
                <td>{$tipe}</td>
                <td>{$kategori}</td>
                <td>" . e($row->deskripsi) . "</td>
                <td>{$row->jumlah}</td>
            </tr>";
        }

        $html .= "</table>";

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
        ]);
    }
}
