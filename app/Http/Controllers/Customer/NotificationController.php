<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Halaman inbox notifikasi customer — tampilkan semua notifikasi (read/unread).
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(20);

        // Tandai semua sebagai sudah dilihat (bukan dibaca) saat membuka inbox
        // "dibaca" hanya saat klik individual
        return view('customer.notifications', compact('notifications'));
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function markRead(Request $request, string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Redirect ke detail shipment jika ada
        $data = $notification->data;
        if (!empty($data['shipment_id'])) {
            return redirect()->route('customer.dashboard')
                ->with('info', 'Notifikasi ditandai sudah dibaca.');
        }

        return back()->with('info', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca.
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi sudah ditandai dibaca.');
    }
}
