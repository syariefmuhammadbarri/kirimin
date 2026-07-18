<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * FR-11: Halaman profil customer — tampilkan form edit data.
     */
    public function show()
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            $customer = Customer::create([
                'user_id' => $user->id,
                'phone'   => '',
                'address' => '',
                'city'    => '',
            ]);
        }

        return view('customer.profile', compact('customer', 'user'));
    }

    /**
     * Proses update data profil customer.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city'    => 'nullable|string|max:100',
            'photo'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Update nama di tabel users
        $user->update(['name' => $request->name]);

        $updateData = [
            'phone'   => $request->phone,
            'address' => $request->address,
            'city'    => $request->city,
        ];

        // Upload foto profil jika ada
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($customer->photo_path && Storage::disk('public')->exists($customer->photo_path)) {
                Storage::disk('public')->delete($customer->photo_path);
            }
            $path = $request->file('photo')->store('profiles', 'public');
            $updateData['photo_path'] = $path;
        }

        $customer->update($updateData);

        return redirect()->route('customer.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
