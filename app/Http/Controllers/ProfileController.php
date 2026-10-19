<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $user->load('roles');

        // statistik user sendiri

        $stats = [
            'total_transactions' => $user->transactions()->count(),
            'total_revenue' => $user->transactions()->sum('total'),
            'total_stock_moves' => $user->stockMovements()->count(),
        ];

        return view('profile.edit', compact('user','stats'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email',Rule::unique('users','email')->ignore($user->id)],
        ]);

        $user->update($data);

        return back()->with('success','Profile berhasil diperbarui');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.min'              => 'Password baru minimal 6 karakter.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        $user = auth()->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
