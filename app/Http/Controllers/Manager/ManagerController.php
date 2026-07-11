<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class ManagerController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function dashboard()
    {
        // System-wide statistics
        $stats = [
            'total_branches' => Branch::count(),
            'total_employees' => User::role(['admin_cabang', 'kurir'])->count(),
            'total_shipments' => Shipment::count(),
            'total_revenue' => Payment::where('payment_status', 'paid')->sum('amount'),
            
            // Status counts
            'booking_created' => Shipment::where('status', 'booking_created')->count(),
            'waiting_dropoff' => Shipment::where('status', 'waiting_dropoff')->count(),
            'weighed' => Shipment::where('status', 'weighed')->count(),
            'assigned' => Shipment::where('status', 'assigned_to_courier')->count(),
            'transit' => Shipment::where('status', 'out_for_delivery')->count(),
            'delivered' => Shipment::where('status', 'delivered')->count(),
            'failed' => Shipment::where('status', 'gagal_kirim')->count(),
        ];

        // Fetch monthly revenue trend data for charts
        $monthlyRevenue = Payment::select(
            DB::raw('SUM(amount) as total'),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
        )
        ->where('payment_status', 'paid')
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->take(12)
        ->get();

        return view('manager.dashboard', compact('stats', 'monthlyRevenue'));
    }

    // --- BRANCHES CRUD ---

    public function listBranches()
    {
        $branches = Branch::withCount('users')->get();
        return view('manager.branches.index', compact('branches'));
    }

    public function createBranch()
    {
        return view('manager.branches.create');
    }

    public function storeBranch(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
        ]);

        Branch::create($data);

        return redirect()->route('manager.branches.index')->with('success', 'Cabang baru berhasil dibuat.');
    }

    public function editBranch(Branch $branch)
    {
        return view('manager.branches.edit', compact('branch'));
    }

    public function updateBranch(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
        ]);

        $branch->update($data);

        return redirect()->route('manager.branches.index')->with('success', 'Data cabang berhasil diperbarui.');
    }

    public function deleteBranch(Branch $branch)
    {
        if ($branch->users()->count() > 0 || $branch->shipments()->count() > 0) {
            return back()->with('error', 'Cabang tidak dapat dihapus karena memiliki relasi karyawan atau paket.');
        }

        $branch->delete();
        return redirect()->route('manager.branches.index')->with('success', 'Cabang berhasil dihapus.');
    }

    // --- USERS CRUD ---

    public function listUsers()
    {
        $users = User::role(['admin_cabang', 'kurir'])->with('branch')->get();
        return view('manager.users.index', compact('users'));
    }

    public function createUser()
    {
        $branches = Branch::all();
        $roles = Role::whereIn('name', ['admin_cabang', 'kurir'])->get();
        return view('manager.users.create', compact('branches', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'branch_id' => 'required|exists:branches,id',
            'role' => 'required|string|in:admin_cabang,kurir',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'branch_id' => $request->branch_id,
            'email_verified_at' => now(), // Auto-verify employees
        ]);

        $user->assignRole($request->role);

        return redirect()->route('manager.users.index')->with('success', 'Pengguna karyawan berhasil ditambahkan.');
    }

    public function editUser(User $user)
    {
        $branches = Branch::all();
        $roles = Role::whereIn('name', ['admin_cabang', 'kurir'])->get();
        return view('manager.users.edit', compact('user', 'branches', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'branch_id' => 'required|exists:branches,id',
            'role' => 'required|string|in:admin_cabang,kurir',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'branch_id' => $request->branch_id,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Sync Spatie role
        $user->syncRoles([$request->role]);

        return redirect()->route('manager.users.index')->with('success', 'Profil karyawan berhasil diperbarui.');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->route('manager.users.index')->with('success', 'Karyawan berhasil dinonaktifkan.');
    }

    // --- REPORTS ---

    public function downloadReport()
    {
        $shipments = Shipment::with(['payment'])->get();

        $metrics = [
            'total_revenue' => Payment::where('payment_status', 'paid')->sum('amount'),
            'total_shipments' => $shipments->count(),
            'delivery_success_rate' => $shipments->count() > 0 
                ? ($shipments->where('status', 'delivered')->count() / $shipments->count()) * 100 
                : 100.0,
            'branches' => [],
            'couriers' => [],
            'status_distribution' => $shipments->groupBy('status')->map->count()->toArray()
        ];

        // Branch breakdown
        $branches = Branch::all();
        foreach ($branches as $branch) {
            $branchShipments = Shipment::where('branch_id', $branch->id)->get();
            $metrics['branches'][] = [
                'name' => $branch->name,
                'shipments_count' => $branchShipments->count(),
                'revenue' => Shipment::where('branch_id', $branch->id)
                    ->join('payments', 'shipments.payment_id', '=', 'payments.id')
                    ->where('payments.payment_status', 'paid')
                    ->sum('payments.amount'),
            ];
        }

        // Courier breakdown
        $couriers = User::role('kurir')->with('branch')->get();
        foreach ($couriers as $courier) {
            $totalJobs = Shipment::where('courier_id', $courier->id)->count();
            $deliveredJobs = Shipment::where('courier_id', $courier->id)->where('status', 'delivered')->count();
            $metrics['couriers'][] = [
                'name' => $courier->name,
                'branch_name' => $courier->branch->name ?? 'Gudang Utama',
                'total_jobs' => $totalJobs,
                'delivered_jobs' => $deliveredJobs,
                'success_rate' => $totalJobs > 0 ? ($deliveredJobs / $totalJobs) * 100 : 100.0
            ];
        }

        $pdf = $this->reportService->generateStrategicReport($metrics);
        return $pdf->download('laporan-operasional-perusahaan.pdf');
    }
}
