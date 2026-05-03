<?php

namespace App\Http\Controllers;

use App\Enums\KategoriTransaksi;
use App\Models\Rencana;
use App\Models\SystemConfig;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RencanaController extends Controller
{
    public function index(): View
    {
        $appName = SystemConfig::getValue('app_name', 'DOMPETKU');
        $userId  = Auth::id();

        // Budget — anggaran per kategori bulan ini
        $budgets = Rencana::where('user_id', $userId)
            ->where('tipe', 'anggaran')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($b) use ($userId) {
                // Hitung pengeluaran aktual SETELAH budget dibuat
                // Gunakan created_at yang presisi agar transaksi sebelum budget tidak ikut
                $aktual = Transaksi::where('user_id', $userId)
                    ->where('tipe', 'pengeluaran')
                    ->where('kategori', $b->kategori)
                    ->where('created_at', '>', $b->created_at)
                    ->whereMonth('tanggal', $b->created_at->month)
                    ->whereYear('tanggal', $b->created_at->year)
                    ->sum('jumlah');

                $b->aktual     = $aktual;
                $b->persen     = $b->target > 0 ? min(round(($aktual / $b->target) * 100), 100) : 0;
                $b->overBudget = $aktual > $b->target;
                $b->sisa       = max($b->target - $aktual, 0);
                $b->lebih      = max($aktual - $b->target, 0);
                return $b;
            });

        // Goals — target tabungan
        $goals = Rencana::where('user_id', $userId)
            ->where('tipe', 'tabungan')
            ->orderBy('created_at')
            ->get();

        // Stats ringkasan
        $totalBudget   = $budgets->sum('target');
        $totalAktual   = $budgets->sum('aktual');
        $totalGoalTarget = $goals->sum('target');
        $totalGoalTerkumpul = $goals->sum('terkumpul');
        $goalsSelesai  = $goals->filter->is_selesai->count();

        // Smart insight — budget paling boros
        $borosKat = $budgets->sortByDesc('aktual')->first();

        // Goal paling dekat deadline
        $goalDekat = $goals->reject->is_selesai
            ->filter(fn($g) => $g->deadline)
            ->sortBy(fn($g) => $g->deadline)
            ->first();

        return view('rencana', compact(
            'appName', 'budgets', 'goals',
            'totalBudget', 'totalAktual',
            'totalGoalTarget', 'totalGoalTerkumpul',
            'goalsSelesai', 'borosKat', 'goalDekat',
        ));
    }

    public function storeBudget(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'target'   => 'required|integer|min:1000',
            'warna'    => 'nullable|string|max:20',
            'icon'     => 'nullable|string|max:50',
        ]);

        Rencana::create([
            'user_id'  => Auth::id(),
            'tipe'     => 'anggaran',
            'nama'     => $validated['nama'],
            'kategori' => $validated['kategori'],
            'target'   => $validated['target'],
            'terkumpul'=> 0,
            'warna'    => $validated['warna'] ?? '#a78bfa',
            'icon'     => KategoriTransaksi::icon($validated['kategori']),
        ]);

        return redirect()->route('rencana')->with('success', 'Budget berhasil ditambahkan!');
    }

    public function storeGoal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100',
            'target'    => 'required|integer|min:1000',
            'terkumpul' => 'nullable|integer|min:0',
            'deadline'  => 'nullable|date|after_or_equal:today',
            'warna'     => 'nullable|string|max:20',
            'icon'      => 'nullable|string|max:50',
        ]);

        Rencana::create([
            'user_id'   => Auth::id(),
            'tipe'      => 'tabungan',
            'nama'      => $validated['nama'],
            'kategori'  => null,
            'target'    => $validated['target'],
            'terkumpul' => $validated['terkumpul'] ?? 0,
            'deadline'  => $validated['deadline'] ?? null,
            'warna'     => $validated['warna'] ?? '#44e2cd',
            'icon'      => $validated['icon'] ?? 'savings',
        ]);

        return redirect()->route('rencana')->with('success', 'Goal berhasil ditambahkan!');
    }

    public function addDana(Request $request, Rencana $rencana): RedirectResponse
    {
        abort_if($rencana->user_id !== Auth::id(), 403);
        $request->validate(['jumlah' => 'required|integer|min:1']);

        $jumlah = (int) $request->jumlah;

        // Tambah ke terkumpul goal
        $rencana->increment('terkumpul', $jumlah);

        // Catat sebagai transaksi pengeluaran (uang keluar dari kas ke tabungan)
        Transaksi::create([
            'user_id'   => Auth::id(),
            'tipe'      => 'pengeluaran',
            'kategori'  => null,
            'jumlah'    => $jumlah,
            'deskripsi' => 'Tabungan: ' . $rencana->nama,
            'tanggal'   => now()->toDateString(),
        ]);

        $msg = $rencana->fresh()->is_selesai
            ? 'Target "' . $rencana->nama . '" telah tercapai!'
            : 'Dana berhasil ditambahkan!';

        return redirect()->route('rencana')->with('success', $msg);
    }

    public function destroy(Rencana $rencana): RedirectResponse
    {
        abort_if($rencana->user_id !== Auth::id(), 403);
        $rencana->delete();
        return redirect()->route('rencana')->with('success', 'Berhasil dihapus!');
    }

    public function tingkatkan(Request $request, Rencana $rencana): RedirectResponse
    {
        abort_if($rencana->user_id !== Auth::id(), 403);
        abort_if($rencana->tipe !== 'tabungan', 403);

        $request->validate([
            'target' => 'required|integer|min:1000',
        ]);

        $newTarget = (int) $request->target;

        if ($newTarget <= $rencana->terkumpul) {
            return back()->with('error', 'Target baru harus lebih besar dari dana yang sudah terkumpul (Rp ' . number_format($rencana->terkumpul, 0, ',', '.') . ').');
        }

        $rencana->update(['target' => $newTarget]);

        return redirect()->route('rencana')->with('success', 'Target "' . $rencana->nama . '" berhasil ditingkatkan!');
    }
}
