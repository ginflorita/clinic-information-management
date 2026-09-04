<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\EncounterDiagnosis;
use App\Models\GoodsReceiptItem;
use App\Models\Invoice;
use App\Models\InvoiceAdjustment;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\ProcedureRecord;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\TreatmentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function patients(): View
    {
        return view('reports.patients', [
            'totalPatients' => Patient::count(),
            'statusCounts' => Patient::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'sexCounts' => Patient::selectRaw('sex, count(*) as total')->groupBy('sex')->pluck('total', 'sex'),
            'referralSourceCounts' => Patient::selectRaw('COALESCE(referral_source, ?) as source, count(*) as total', ['Unspecified'])
                ->groupBy('source')->orderByDesc('total')->pluck('total', 'source'),
            'newThisMonth' => Patient::whereBetween('created_at', [now()->startOfMonth(), now()])->count(),
        ]);
    }

    public function appointments(Request $request): View
    {
        [$from, $to] = $this->dateRange($request);

        $appointments = Appointment::whereBetween('scheduled_start', [$from, $to]);

        return view('reports.appointments', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total' => (clone $appointments)->count(),
            'statusCounts' => (clone $appointments)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'byProvider' => (clone $appointments)->with('provider')
                ->selectRaw('provider_id, count(*) as total')
                ->groupBy('provider_id')
                ->orderByDesc('total')
                ->get(),
        ]);
    }

    public function clinical(Request $request): View
    {
        [$from, $to] = $this->dateRange($request);

        $procedureRecords = ProcedureRecord::where('status', 'completed')->whereBetween('performed_at', [$from, $to]);

        return view('reports.clinical', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'procedureCount' => (clone $procedureRecords)->count(),
            'procedureRevenue' => (clone $procedureRecords)->sum('total_amount'),
            'byProcedure' => (clone $procedureRecords)->with('procedure')
                ->selectRaw('procedure_id, count(*) as total, sum(total_amount) as revenue')
                ->groupBy('procedure_id')
                ->orderByDesc('revenue')
                ->get(),
            'treatmentPlanCounts' => TreatmentPlan::whereBetween('created_at', [$from, $to])
                ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'diagnosisCounts' => EncounterDiagnosis::whereBetween('diagnosed_at', [$from, $to])
                ->with('diagnosis')
                ->selectRaw('diagnosis_id, count(*) as total')
                ->groupBy('diagnosis_id')
                ->orderByDesc('total')
                ->get(),
        ]);
    }

    public function financial(Request $request): View
    {
        [$from, $to] = $this->dateRange($request);

        return view('reports.financial', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'totalInvoiced' => Invoice::where('status', 'issued')->whereBetween('invoice_date', [$from, $to])->sum('total_amount'),
            'totalPayments' => Payment::where('status', 'completed')->whereBetween('payment_date', [$from, $to])->sum('amount'),
            'outstandingBalance' => Invoice::where('status', 'issued')->sum('balance'),
            'adjustmentsByType' => InvoiceAdjustment::whereBetween('created_at', [$from, $to])
                ->selectRaw('type, sum(amount) as total')->groupBy('type')->pluck('total', 'type'),
            'revenueByProvider' => ProcedureRecord::where('status', 'completed')
                ->whereBetween('performed_at', [$from, $to])
                ->with('provider')
                ->selectRaw('provider_id, sum(total_amount) as revenue')
                ->groupBy('provider_id')
                ->orderByDesc('revenue')
                ->get(),
            'revenueByProcedure' => ProcedureRecord::where('status', 'completed')
                ->whereBetween('performed_at', [$from, $to])
                ->with('procedure')
                ->selectRaw('procedure_id, sum(total_amount) as revenue')
                ->groupBy('procedure_id')
                ->orderByDesc('revenue')
                ->get(),
        ]);
    }

    public function inventory(Request $request): View
    {
        [$from, $to] = $this->dateRange($request);

        $products = Product::where('is_active', true)->withSum('batches', 'quantity')->get();

        return view('reports.inventory', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'products' => $products,
            'lowStockProducts' => $products->filter->isLowStock()->values(),
            'expiringBatches' => ProductBatch::expiringWithin(InventoryController::EXPIRY_WARNING_DAYS)->with('product')->orderBy('expiry_date')->get(),
            'movementCounts' => StockMovement::whereBetween('movement_date', [$from, $to])
                ->selectRaw('movement_type, count(*) as total, sum(quantity) as quantity')
                ->groupBy('movement_type')
                ->get(),
            'purchasesReceived' => GoodsReceiptItem::whereHas('goodsReceipt', fn ($q) => $q->whereBetween('received_date', [$from, $to]))
                ->with('purchaseOrderItem')
                ->get()
                ->sum(fn (GoodsReceiptItem $item) => $item->quantity_received * $item->purchaseOrderItem->unit_cost),
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function dateRange(Request $request): array
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to = $request->date('to') ?? now();

        return [$from->copy()->startOfDay(), $to->copy()->endOfDay()];
    }
}
