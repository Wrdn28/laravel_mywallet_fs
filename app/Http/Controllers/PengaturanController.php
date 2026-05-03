<?php

namespace App\Http\Controllers;

use App\Models\SystemConfig;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
class PengaturanController extends Controller
{
    public function index(): View
    {
        $appName  = SystemConfig::getValue('app_name', 'DOMPETKU');
        $userData = Auth::user();

        return view('pengaturan', compact('appName', 'userData'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        Auth::user()->update(['email' => $request->email]);

        return redirect()->route('pengaturan')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini salah!');
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('pengaturan')
            ->with('success', 'Password berhasil diubah!');
    }

    public function deleteAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm_text' => 'required|in:HAPUS',
        ]);

        $user = Auth::user();

        Auth::logout();

        // Delete all transactions then the user
        Transaksi::where('user_id', $user->id)->delete();
        User::destroy($user->id);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Akun berhasil dihapus.');
    }
}
