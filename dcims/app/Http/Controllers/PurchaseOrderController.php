<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use LogicException;

class PurchaseOrderController extends Controller
{
    public function index(): View
    {
        return view('purchase-orders.index', [
            'purchaseOrders' => PurchaseOrder::with('supplier')->orderByDesc('created_at')->get(),
        ]);
    }

    public function create(): View
    {
        return view('purchase-orders.create', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'order_date' => ['required', 'date'],
            'expected_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['created_by'] = $request->user()->id;

        $purchaseOrder = PurchaseOrder::create($data);

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('status', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['supplier', 'items.product', 'goodsReceipts.items.batch.product', 'goodsReceipts.receivedByUser']);

        return view('purchase-orders.show', [
            'purchaseOrder' => $purchaseOrder,
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'availableTransitions' => PurchaseOrder::TRANSITIONS[$purchaseOrder->status] ?? [],
        ]);
    }

    public function transition(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(PurchaseOrder::STATUSES)],
        ]);

        try {
            $purchaseOrder->transitionTo($data['status']);
        } catch (LogicException $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('status', 'Purchase order updated.');
    }
}
