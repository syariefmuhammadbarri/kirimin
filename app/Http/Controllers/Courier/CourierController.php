<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CourierAssignment;
use App\Models\DeliveryProof;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CourierController extends Controller
{
    public function dashboard()
    {
        $courier = Auth::user();
        
        $query = Shipment::with(['customer', 'payment'])
            ->where('courier_id', $courier->id);

        $allShipments = (clone $query)->get();

        $stats = [
            'total'     => $allShipments->count(),
            'pickup'    => $allShipments->whereIn('status', ['pickup_assigned', 'picked_up_from_customer'])->count(),
            'assigned'  => $allShipments->whereIn('status', ['assigned_to_courier', 'picked_up'])->count(),
            'transit'   => $allShipments->where('status', 'out_for_delivery')->count(),
            'delivered' => $allShipments->where('status', 'delivered')->count(),
            'failed'    => $allShipments->where('status', 'gagal_kirim')->count(),
        ];

        $shipments = $query->latest()->paginate(10);

        return view('courier.dashboard', compact('shipments', 'stats'));
    }

    public function pickUp(Shipment $shipment)
    {
        $courier = Auth::user();
        if ($shipment->courier_id !== $courier->id) {
            abort(403);
        }

        DB::transaction(function () use ($shipment, $courier) {
            $branch = Branch::find($courier->branch_id);
            $location = $branch ? $branch->city : $shipment->origin_city;

            $shipment->update([
                'status' => 'picked_up'
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $location,
                'description' => "Paket telah diambil (pickup) dari cabang " . ($branch ? $branch->name : 'asal') . " oleh Kurir {$courier->name}.",
                'status' => 'picked_up',
                'tracked_at' => now(),
            ]);
        });

        return back()->with('success', 'Paket berhasil diambil (Picked Up).');
    }

    public function outForDelivery(Shipment $shipment)
    {
        $courier = Auth::user();
        if ($shipment->courier_id !== $courier->id) {
            abort(403);
        }

        DB::transaction(function () use ($shipment, $courier) {
            $branch = Branch::find($courier->branch_id);
            $location = $branch ? $branch->city : $shipment->origin_city;

            $shipment->update([
                'status' => 'out_for_delivery'
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $location,
                'description' => "Paket dibawa oleh Kurir {$courier->name} sedang dalam perjalanan menuju alamat penerima.",
                'status' => 'out_for_delivery',
                'tracked_at' => now(),
            ]);
        });

        return back()->with('success', 'Status pengiriman diubah menjadi sedang diantar.');
    }

    public function deliver(Request $request, Shipment $shipment)
    {
        $courier = Auth::user();
        if ($shipment->courier_id !== $courier->id) {
            abort(403);
        }

        $request->validate([
            'recipient_name' => 'required|string|max:100',
            'recipient_signature' => 'required|string', // Base64 image data
            'photos' => 'required|array|min:1|max:3',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048', // min 1 max 3, max 2MB per file
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $shipment, $courier) {
            // Upload proof photos
            $uploadedPaths = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    $path = $file->store('proofs', 'public');
                    $uploadedPaths[] = $path;
                }
            }

            // Create Delivery Proof
            DeliveryProof::create([
                'shipment_id' => $shipment->id,
                'courier_id' => $courier->id,
                'photos' => $uploadedPaths,
                'notes' => $request->notes,
                'recipient_name' => $request->recipient_name,
                'recipient_signature' => $request->recipient_signature,
            ]);

            // Update shipment
            $shipment->update([
                'status' => 'delivered'
            ]);

            // Create tracking timeline checkpoint
            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $shipment->destination_city,
                'description' => "Paket berhasil diterima oleh {$request->recipient_name}. Penerima menandatangani bukti penyerahan.",
                'status' => 'delivered',
                'tracked_at' => now(),
            ]);
        });

        return redirect()->route('courier.dashboard')->with('success', 'Paket berhasil ditandai sebagai Terkirim.');
    }

    public function failDelivery(Request $request, Shipment $shipment)
    {
        $courier = Auth::user();
        if ($shipment->courier_id !== $courier->id) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $shipment, $courier) {
            $shipment->update([
                'status' => 'gagal_kirim'
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $shipment->destination_city,
                'description' => "Pengiriman gagal. Alasan: " . $request->reason,
                'status' => 'gagal_kirim',
                'tracked_at' => now(),
            ]);
        });

        return redirect()->route('courier.dashboard')->with('success', 'Paket ditandai sebagai Gagal Kirim.');
    }

    public function collectFromCustomer(Shipment $shipment)
    {
        $courier = Auth::user();
        if ($shipment->courier_id !== $courier->id) {
            abort(403);
        }
        if ($shipment->status !== 'pickup_assigned') {
            return back()->with('error', 'Status paket tidak valid untuk konfirmasi penjemputan.');
        }

        DB::transaction(function () use ($shipment, $courier) {
            $shipment->update(['status' => 'picked_up_from_customer']);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $shipment->origin_city,
                'description' => "Paket berhasil dijemput oleh Kurir {$courier->name} dari alamat pengirim.",
                'status' => 'picked_up_from_customer',
                'tracked_at' => now(),
            ]);
        });

        return back()->with('success', 'Paket berhasil dikonfirmasi dijemput dari customer.');
    }

    public function dropAtBranch(Shipment $shipment)
    {
        $courier = Auth::user();
        if ($shipment->courier_id !== $courier->id) {
            abort(403);
        }
        if ($shipment->status !== 'picked_up_from_customer') {
            return back()->with('error', 'Status paket tidak valid.');
        }

        DB::transaction(function () use ($shipment, $courier) {
            $branch = Branch::find($courier->branch_id);

            $shipment->update([
                'status' => 'received_at_branch',
                'courier_id' => null, // lepas assignment pickup, siap di-assign ulang untuk delivery/transit
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'location' => $branch?->city ?? $shipment->origin_city,
                'description' => "Paket hasil penjemputan telah diserahkan Kurir {$courier->name} ke Cabang {$branch?->name}.",
                'status' => 'received_at_branch',
                'tracked_at' => now(),
            ]);

            // Tandai assignment pickup sebagai selesai
            CourierAssignment::where('shipment_id', $shipment->id)
                ->where('type', 'pickup')
                ->whereIn('status', ['pending', 'assigned'])
                ->update(['status' => 'completed']);
        });

        return redirect()->route('courier.dashboard')->with('success', 'Paket berhasil diserahkan ke cabang.');
    }
}
