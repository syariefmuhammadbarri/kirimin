<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShipmentCancelController extends Controller
{
    /**
     * FR-01: Customer membatalkan shipment miliknya sendiri.
     * Syarat: status masih booking_created/waiting_dropoff/pickup_scheduled/payment_pending
     *         DAN payment belum paid.
     */
    public function cancel(Request $request, Shipment $shipment)
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        // Validasi kepemilikan resource (pola abort(403) existing dipertahankan)
        if (!$customer || $shipment->customer_id !== $customer->id) {
            abort(403, 'Akses ditolak.');
        }

        // Validasi eligibilitas pembatalan
        if (!$shipment->isCancellable()) {
            return back()->with('error', 'Paket ini tidak dapat dibatalkan. Hanya paket yang belum dibayar dan belum diproses yang bisa dibatalkan.');
        }

        $request->validate([
            'cancel_reason' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $shipment) {
            $reason = $request->cancel_reason ?: 'Dibatalkan oleh pengirim';

            $shipment->update([
                'status'        => 'cancelled',
                'cancelled_at'  => now(),
                'cancel_reason' => $reason,
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location'    => $shipment->origin_city,
                'description' => 'Booking dibatalkan oleh pengirim. Alasan: ' . $reason,
                'status'      => 'cancelled',
                'tracked_at'  => now(),
            ]);
        });

        return redirect()->route('customer.dashboard')
            ->with('success', 'Booking ' . $shipment->booking_code . ' berhasil dibatalkan.');
    }
}
