<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialMail;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('it.users.index', compact('users'));
    }

    public function history()
    {
        $logs = \App\Models\ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50); // Show 50 entries per page for history

        return view('it.users.history', compact('logs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:Administrator IT,Operator Pusat Kendali,Teknisi Lapangan,Pejabat Berwenang,Warga',
        ]);

        $randomPassword = Str::random(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($randomPassword),
            'role' => $request->role,
            'must_change_password' => true,
        ]);

        return back()->with('success_password', [
            'name' => $request->name,
            'password' => $randomPassword,
            'role' => $request->role
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:Administrator IT,Operator Pusat Kendali,Teknisi Lapangan,Pejabat Berwenang,Warga',
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role user {$user->name} berhasil diperbarui menjadi {$request->role}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', "Akun user {$user->name} berhasil dihapus dari sistem.");
    }
}
