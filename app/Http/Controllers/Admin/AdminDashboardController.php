<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rencana;
use App\Models\SystemConfig;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    // -------------------------------------------------------------------------
    // Dashboard index
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $appName = SystemConfig::getValue('app_name', 'DOMPETKU');

        // Global stats (no filter)
        $totalUsers        = User::where('email', '!=', 'admin@dompetku.com')->count();
        $totalTransactions = Transaksi::count();
        $totalPemasukan    = Transaksi::where('tipe', 'pemasukan')->sum('jumlah');
        $totalPengeluaran  = Transaksi::where('tipe', 'pengeluaran')->sum('jumlah');
        $totalSaldo        = $totalPemasukan - $totalPengeluaran;

        // Filtered queries (for transactions & reports tab)
        $query = $this->buildTransaksiQuery($request);

        // Users list with search
        $searchUser = $request->get('search_user', '');
        $users = User::where('email', '!=', 'admin@dompetku.com')
            ->when($searchUser, fn($q) => $q->where('email', 'like', "%{$searchUser}%"))
            ->withCount('transaksi')
            ->orderByDesc('created_at')
            ->get();

        // Transactions (max 50)
        $transactions = (clone $query)
            ->with('user:id,email')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        // User stats for reports
        $userStats = User::where('email', '!=', 'admin@dompetku.com')
            ->withCount(['transaksi as total_transactions'])
            ->withSum(['transaksi as total_pemasukan' => fn($q) => $q->where('tipe', 'pemasukan')], 'jumlah')
            ->withSum(['transaksi as total_pengeluaran' => fn($q) => $q->where('tipe', 'pengeluaran')], 'jumlah')
            ->get();

        // Monthly stats (last 6 months)
        $monthlyStats = Transaksi::selectRaw("
                DATE_FORMAT(tanggal, '%Y-%m') as bulan,
                SUM(CASE WHEN tipe = 'pemasukan'  THEN jumlah ELSE 0 END) as pemasukan,
                SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END) as pengeluaran
            ")
            ->where('tanggal', '>=', now()->subMonths(6))
            ->groupByRaw("DATE_FORMAT(tanggal, '%Y-%m')")
            ->orderBy('bulan')
            ->get();

        // System config
        $config            = SystemConfig::pluck('config_value', 'config_key');
        $maintenanceMode   = $config->get('maintenance_mode', '0');
        $adminEmail        = $config->get('admin_email', auth()->user()->email);
        $systemVersion     = $config->get('system_version', 'v1.0.0');

        // Budget & Goals stats
        $totalBudgets      = Rencana::where('tipe', 'anggaran')->count();
        $totalGoals        = Rencana::where('tipe', 'tabungan')->count();
        $goalsSelesai      = Rencana::where('tipe', 'tabungan')
                                ->whereColumn('terkumpul', '>=', 'target')->count();

        // Budget over limit bulan ini
        $budgetsOverLimit  = Rencana::where('tipe', 'anggaran')
            ->get()
            ->filter(function ($b) {
                $aktual = Transaksi::where('user_id', $b->user_id)
                    ->where('tipe', 'pengeluaran')
                    ->where('kategori', $b->kategori)
                    ->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year)
                    ->sum('jumlah');
                return $aktual > $b->target;
            })->count();

        // All budgets with actual spending (for admin view)
        $allBudgets = Rencana::where('tipe', 'anggaran')
            ->with('user:id,email')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($b) {
                $aktual = Transaksi::where('user_id', $b->user_id)
                    ->where('tipe', 'pengeluaran')
                    ->where('kategori', $b->kategori)
                    ->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year)
                    ->sum('jumlah');
                $b->aktual     = $aktual;
                $b->persen     = $b->target > 0 ? min(round(($aktual / $b->target) * 100), 100) : 0;
                $b->overBudget = $aktual > $b->target;
                return $b;
            });

        // All goals (for admin view)
        $allGoals = Rencana::where('tipe', 'tabungan')
            ->with('user:id,email')
            ->orderByDesc('created_at')
            ->get();

        $activeTab = $request->get('tab', 'users');

        return view('admin.dashboard', compact(
            'appName', 'totalUsers', 'totalTransactions',
            'totalPemasukan', 'totalPengeluaran', 'totalSaldo',
            'users', 'transactions', 'userStats', 'monthlyStats',
            'config', 'maintenanceMode', 'adminEmail', 'systemVersion',
            'activeTab', 'searchUser',
            'totalBudgets', 'totalGoals', 'goalsSelesai', 'budgetsOverLimit',
            'allBudgets', 'allGoals',
        ));
    }

    // -------------------------------------------------------------------------
    // User Management
    // -------------------------------------------------------------------------

    public function addUser(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'     => explode('@', $request->email)[0],
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'users'])
            ->with('toast_success', 'User berhasil ditambahkan!');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'new_password' => 'required|min:6',
        ]);

        User::findOrFail($request->user_id)
            ->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('admin.dashboard', ['tab' => 'users'])
            ->with('toast_success', 'Password berhasil direset!');
    }

    public function deleteUser(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->user_id);

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.dashboard', ['tab' => 'users'])
                ->with('toast_error', 'Tidak dapat menghapus akun sendiri!');
        }

        // Cascade delete transactions
        $user->transaksi()->delete();
        $user->delete();

        return redirect()->route('admin.dashboard', ['tab' => 'users'])
            ->with('toast_success', 'User berhasil dihapus!');
    }

    // -------------------------------------------------------------------------
    // Transaction Management
    // -------------------------------------------------------------------------

    public function deleteTransaction(Request $request): RedirectResponse
    {
        Transaksi::findOrFail($request->transaction_id)->delete();

        return redirect()->route('admin.dashboard', $request->only(['tab', 'periode', 'start_date', 'end_date']))
            ->with('toast_success', 'Transaksi berhasil dihapus!');
    }

    // -------------------------------------------------------------------------
    // System Config
    // -------------------------------------------------------------------------

    public function updateConfig(Request $request): RedirectResponse
    {
        $request->validate([
            'app_name'    => 'required|string|max:100',
            'admin_email' => 'required|email',
        ]);

        $configs = [
            'app_name'         => $request->app_name,
            'admin_email'      => $request->admin_email,
            'maintenance_mode' => $request->boolean('maintenance_mode') ? '1' : '0',
        ];

        foreach ($configs as $key => $value) {
            SystemConfig::updateOrCreate(
                ['config_key' => $key],
                ['config_value' => $value]
            );
        }

        return redirect()->route('admin.dashboard', ['tab' => 'config'])
            ->with('toast_success', 'Konfigurasi sistem berhasil diperbarui!');
    }

    // -------------------------------------------------------------------------
    // AJAX: user transactions
    // -------------------------------------------------------------------------

    public function userTransactions(Request $request)
    {
        $user = User::with(['transaksi' => fn($q) => $q->orderByDesc('tanggal')->limit(20)])
            ->findOrFail($request->user_id);

        return view('admin.partials.user-transactions', compact('user'));
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    private function buildTransaksiQuery(Request $request)
    {
        $periode = $request->get('periode', 'all');
        $query   = Transaksi::query();

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
}
