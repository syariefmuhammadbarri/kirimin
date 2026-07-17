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

    public function dashboard(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Apply filters
        $paymentQuery = Payment::query();
        $shipmentQuery = Shipment::query();

        if ($startDate) {
            $paymentQuery->whereDate('created_at', '>=', $startDate);
            $shipmentQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $paymentQuery->whereDate('created_at', '<=', $endDate);
            $shipmentQuery->whereDate('created_at', '<=', $endDate);
        }

        // Optimize status counts in 1 query
        $statusCounts = (clone $shipmentQuery)->groupBy('status')
            ->select('status', DB::raw('count(*) as count'))
            ->pluck('count', 'status')
            ->toArray();

        // System-wide statistics
        $stats = [
            'total_branches' => Branch::count(),
            'total_employees' => User::role(['admin_cabang', 'kurir'])->count(),
            'total_shipments' => (clone $shipmentQuery)->count(),
            'total_revenue' => (clone $paymentQuery)->where('payment_status', 'paid')->sum('amount'),
            
            // Status counts
            'booking_created' => $statusCounts['booking_created'] ?? 0,
            'waiting_dropoff' => $statusCounts['waiting_dropoff'] ?? 0,
            'weighed' => $statusCounts['weighed'] ?? 0,
            'assigned' => $statusCounts['assigned_to_courier'] ?? 0,
            'transit' => $statusCounts['out_for_delivery'] ?? 0,
            'delivered' => $statusCounts['delivered'] ?? 0,
            'failed' => $statusCounts['gagal_kirim'] ?? 0,
        ];

        // Fetch monthly revenue trend data for charts
        $monthlyRevenue = (clone $paymentQuery)->select(
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

    public function downloadReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $paymentQuery = Payment::query();
        $shipmentQuery = Shipment::query();

        if ($startDate) {
            $paymentQuery->whereDate('created_at', '>=', $startDate);
            $shipmentQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $paymentQuery->whereDate('created_at', '<=', $endDate);
            $shipmentQuery->whereDate('created_at', '<=', $endDate);
        }

        $shipments = (clone $shipmentQuery)->with(['payment'])->get();

        $metrics = [
            'total_revenue' => (clone $paymentQuery)->where('payment_status', 'paid')->sum('amount'),
            'total_shipments' => $shipments->count(),
            'delivery_success_rate' => $shipments->count() > 0 
                ? ($shipments->where('status', 'delivered')->count() / $shipments->count()) * 100 
                : 100.0,
            'branches' => [],
            'couriers' => [],
            'status_distribution' => $shipments->groupBy('status')->map->count()->toArray()
        ];

        // Branch breakdown - OPTIMIZED
        $branchRevenues = Shipment::join('payments', 'shipments.payment_id', '=', 'payments.id')
            ->where('payments.payment_status', 'paid')
            ->when($startDate, fn($q) => $q->whereDate('payments.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('payments.created_at', '<=', $endDate))
            ->groupBy('shipments.branch_id')
            ->select('shipments.branch_id', DB::raw('SUM(payments.amount) as revenue'))
            ->pluck('revenue', 'branch_id');

        $branches = Branch::all();
        foreach ($branches as $branch) {
            $branchShipmentsCount = (clone $shipmentQuery)->where('branch_id', $branch->id)->count();
            $metrics['branches'][] = [
                'name' => $branch->name,
                'shipments_count' => $branchShipmentsCount,
                'revenue' => $branchRevenues[$branch->id] ?? 0,
            ];
        }

        // Courier breakdown - OPTIMIZED
        $courierJobs = Shipment::groupBy('courier_id')
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->select(
                'courier_id',
                DB::raw('count(*) as total_jobs'),
                DB::raw('SUM(case when status = "delivered" then 1 else 0 end) as delivered_jobs')
            )
            ->get()
            ->keyBy('courier_id');

        $couriers = User::role('kurir')->with('branch')->get();
        foreach ($couriers as $courier) {
            $jobsInfo = $courierJobs->get($courier->id);
            $totalJobs = $jobsInfo ? $jobsInfo->total_jobs : 0;
            $deliveredJobs = $jobsInfo ? $jobsInfo->delivered_jobs : 0;
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
