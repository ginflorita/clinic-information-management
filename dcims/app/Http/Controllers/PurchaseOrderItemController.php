<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PurchaseOrderItemController extends Controller
{
    public function store(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if ($purchaseOrder->status !== 'draft') {
            throw ValidationException::withMessages([
                'product_id' => 'Items can only be added while the purchase order is still a draft.',
            ]);
        }

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity_ordered' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $purchaseOrder->items()->create($data);

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('status', 'Item added.');
    }
}
