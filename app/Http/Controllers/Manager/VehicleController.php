<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('courier.branch')->latest()->get();
        return view('manager.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $couriers = User::role('kurir')->with('branch')->get();
        return view('manager.vehicles.create', compact('couriers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
            'type' => 'required|string|in:motor,truck',
            'courier_id' => 'nullable|exists:users,id',
        ]);

        Vehicle::create($data);

        return redirect()->route('manager.vehicles.index')
            ->with('success', 'Kendaraan baru berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle)
    {
        $couriers = User::role('kurir')->with('branch')->get();
        return view('manager.vehicles.edit', compact('vehicle', 'couriers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'plate_number' => ['required', 'string', 'max:20', Rule::unique('vehicles', 'plate_number')->ignore($vehicle->id)],
            'type' => 'required|string|in:motor,truck',
            'courier_id' => 'nullable|exists:users,id',
        ]);

        $vehicle->update($data);

        return redirect()->route('manager.vehicles.index')
            ->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('manager.vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }
}