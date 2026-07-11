<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Branch;
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
        
        $shipments = Shipment::with(['customer', 'payment'])
            ->where('courier_id', $courier->id)
            ->latest()
            ->get();

        $stats = [
            'total' => $shipments->count(),
            'assigned' => $shipments->where('status', 'assigned_to_courier')->count(),
            'transit' => $shipments->where('status', 'out_for_delivery')->count(),
            'delivered' => $shipments->where('status', 'delivered')->count(),
            'failed' => $shipments->where('status', 'gagal_kirim')->count(),
        ];

        return view('courier.dashboard', compact('shipments', 'stats'));
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
}
