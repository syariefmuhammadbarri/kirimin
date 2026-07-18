<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
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

        // Apply date range filters if present
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

        // 1. Core financial metrics (Lunas only)
        $totalRevenue = (clone $paymentQuery)->where('payment_status', 'paid')->sum('amount');
        $totalShipments = (clone $shipmentQuery)->count();
        $deliveredCount = (clone $shipmentQuery)->where('status', 'delivered')->count();
        $successRate = $totalShipments > 0 ? ($deliveredCount / $totalShipments) * 100 : 100.0;

        // 2. Ranking of branches by volume and revenue - OPTIMIZED to prevent N+1
        $branchRevenues = Shipment::join('payments', 'shipments.payment_id', '=', 'payments.id')
            ->where('payments.payment_status', 'paid')
            ->when($startDate, function ($q) use ($startDate) {
                return $q->whereDate('payments.created_at', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                return $q->whereDate('payments.created_at', '<=', $endDate);
            })
            ->groupBy('shipments.branch_id')
            ->select('shipments.branch_id', DB::raw('SUM(payments.amount) as revenue'))
            ->pluck('revenue', 'branch_id');

        $branchesRanking = Branch::select('branches.id', 'branches.name', 'branches.city')
            ->withCount(['shipments' => function ($q) use ($startDate, $endDate) {
                $q->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
                  ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate));
            }])
            ->get()
            ->map(function ($branch) use ($branchRevenues) {
                $branch->revenue = $branchRevenues[$branch->id] ?? 0;
                return $branch;
            })
            ->sortByDesc('revenue')
            ->take(5);

        // 3. Top customers leaderboard - OPTIMIZED to prevent N+1
        $customerRevenues = Shipment::join('payments', 'shipments.payment_id', '=', 'payments.id')
            ->where('payments.payment_status', 'paid')
            ->when($startDate, function ($q) use ($startDate) {
                return $q->whereDate('payments.created_at', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                return $q->whereDate('payments.created_at', '<=', $endDate);
            })
            ->groupBy('shipments.customer_id')
            ->select('shipments.customer_id', DB::raw('SUM(payments.amount) as total_spent'))
            ->pluck('total_spent', 'customer_id');

        $topCustomers = Customer::query()
            ->join('users', 'users.id', '=', 'customers.user_id')
            ->select('customers.id', 'users.name', 'users.email')
            ->withCount(['shipments' => function ($q) use ($startDate, $endDate) {
                $q->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
                  ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate));
            }])
            ->get()
            ->map(function ($cust) use ($customerRevenues) {
                $cust->total_spent = $customerRevenues[$cust->id] ?? 0;
                return $cust;
            })
            ->sortByDesc('total_spent')
            ->take(5);

        // 4. Best performing couriers - OPTIMIZED to prevent N+1
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

        $topCouriers = User::role('kurir')
            ->with('branch')
            ->get()
            ->map(function ($cour) use ($courierJobs) {
                $jobsInfo = $courierJobs->get($cour->id);
                $totalJobs = $jobsInfo ? $jobsInfo->total_jobs : 0;
                $deliveredJobs = $jobsInfo ? $jobsInfo->delivered_jobs : 0;
                $cour->total_jobs = $totalJobs;
                $cour->delivered_jobs = $deliveredJobs;
                $cour->success_rate = $totalJobs > 0 ? ($deliveredJobs / $totalJobs) * 100 : 100.0;
                return $cour;
            })
            ->sortByDesc('success_rate')
            ->take(5);

        // 5. Volume status distribution
        $statusDistribution = (clone $shipmentQuery)->groupBy('status')
            ->select('status', DB::raw('count(*) as total'))
            ->pluck('total', 'status')
            ->toArray();

        // 6. Monthly earnings trend
        $monthlyRevenue = (clone $paymentQuery)->select(
            DB::raw('SUM(amount) as total'),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
        )
        ->where('payment_status', 'paid')
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->take(12)
        ->get();

        return view('owner.dashboard', compact(
            'totalRevenue', 
            'totalShipments', 
            'successRate',
            'branchesRanking',
            'topCustomers',
            'topCouriers',
            'statusDistribution',
            'monthlyRevenue'
        ));
    }

    public function exportReport(Request $request)
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

        // Top Branches - OPTIMIZED
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
        usort($metrics['branches'], function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        // Top Couriers - OPTIMIZED
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
        usort($metrics['couriers'], function ($a, $b) {
            return $b['success_rate'] <=> $a['success_rate'];
        });

        $pdf = $this->reportService->generateStrategicReport($metrics);
        return $pdf->download('laporan-strategis-owner.pdf');
    }
}
