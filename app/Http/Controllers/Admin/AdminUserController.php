<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query();

        if ($request->filled('keyword')) {
            $kw = $request->string('keyword');
            $q->where(function ($w) use ($kw) {
                $w->where('username', 'like', "%{$kw}%")
                  ->orWhere('email', 'like', "%{$kw}%");
            });
        }

        $users = $q->latest()->paginate(30)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function setRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:buyer,admin'],
        ]);

        $actor = $request->user();

        // Tidak boleh ubah role diri sendiri
        if ($actor->id === $user->id) {
            return back()->with('error', 'Tidak boleh mengubah role akun sendiri.');
        }

        // Tidak boleh ubah role admin lain
        if ($user->role === User::ROLE_ADMIN) {
            return back()->with('error', 'Tidak boleh mengubah role admin lain.');
        }

        $user->role = $data['role'];
        $user->save();

        return back()->with('success', 'Role user berhasil diupdate.');
    }

    public function toggleActive(User $user)
    {
        $actor = request()->user();

        // kalau belum punya kolom is_active, jangan dipakai
        if (!array_key_exists('is_active', $user->getAttributes())) {
            return back()->with('error', 'Kolom is_active belum ada di tabel users.');
        }

        // anti-lockout: tidak boleh suspend akun sendiri
        if ($actor->id === $user->id) {
            return back()->with('error', 'Tidak boleh menonaktifkan akun sendiri.');
        }

        // optional: tidak boleh disable admin lain
        if ($user->role === User::ROLE_ADMIN) {
            return back()->with('error', 'Tidak boleh menonaktifkan admin lain.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'Status user diubah.');
    }
}
