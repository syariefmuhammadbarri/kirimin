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

    public function dashboard()
    {
        // 1. Core financial metrics (Lunas only)
        $totalRevenue = Payment::where('payment_status', 'paid')->sum('amount');
        $totalShipments = Shipment::count();
        $deliveredCount = Shipment::where('status', 'delivered')->count();
        $successRate = $totalShipments > 0 ? ($deliveredCount / $totalShipments) * 100 : 100.0;

        // 2. Ranking of branches by volume and revenue
        $branchesRanking = Branch::select('branches.id', 'branches.name', 'branches.city')
            ->withCount('shipments')
            ->get()
            ->map(function ($branch) {
                $revenue = Shipment::where('branch_id', $branch->id)
                    ->join('payments', 'shipments.payment_id', '=', 'payments.id')
                    ->where('payments.payment_status', 'paid')
                    ->sum('payments.amount');
                $branch->revenue = $revenue;
                return $branch;
            })
            ->sortByDesc('revenue')
            ->take(5);

        // 3. Top customers leaderboard
        $topCustomers = Customer::select('customers.id', 'customers.name', 'customers.email')
            ->withCount('shipments')
            ->get()
            ->map(function ($cust) {
                $revenue = Shipment::where('customer_id', $cust->id)
                    ->join('payments', 'shipments.payment_id', '=', 'payments.id')
                    ->where('payments.payment_status', 'paid')
                    ->sum('payments.amount');
                $cust->total_spent = $revenue;
                return $cust;
            })
            ->sortByDesc('total_spent')
            ->take(5);

        // 4. Best performing couriers
        $topCouriers = User::role('kurir')
            ->with('branch')
            ->get()
            ->map(function ($cour) {
                $totalJobs = Shipment::where('courier_id', $cour->id)->count();
                $deliveredJobs = Shipment::where('courier_id', $cour->id)->where('status', 'delivered')->count();
                $cour->total_jobs = $totalJobs;
                $cour->delivered_jobs = $deliveredJobs;
                $cour->success_rate = $totalJobs > 0 ? ($deliveredJobs / $totalJobs) * 100 : 100.0;
                return $cour;
            })
            ->sortByDesc('success_rate')
            ->take(5);

        // 5. Volume status distribution
        $statusDistribution = Shipment::groupBy('status')
            ->select('status', DB::raw('count(*) as total'))
            ->pluck('total', 'status')
            ->toArray();

        // 6. Monthly earnings trend
        $monthlyRevenue = Payment::select(
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

    public function exportReport()
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

        // Top Branches
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
        usort($metrics['branches'], function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        // Top Couriers
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
        usort($metrics['couriers'], function ($a, $b) {
            return $b['success_rate'] <=> $a['success_rate'];
        });

        $pdf = $this->reportService->generateStrategicReport($metrics);
        return $pdf->download('laporan-strategis-owner.pdf');
    }
}
