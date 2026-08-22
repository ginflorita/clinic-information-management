<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GoodsReceiptController extends Controller
{
    public function store(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        if (! in_array($purchaseOrder->status, ['ordered', 'partially_received'], true)) {
            throw ValidationException::withMessages([
                'received_date' => 'This purchase order is not open for receiving.',
            ]);
        }

        $data = $request->validate([
            'received_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $quantities = collect($data['items'])
            ->filter(fn ($qty) => $qty !== null && (float) $qty > 0);

        if ($quantities->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Enter a received quantity for at least one item.',
            ]);
        }

        $poItems = $purchaseOrder->items()->with('product')->whereIn('id', $quantities->keys())->get()->keyBy('id');

        if ($poItems->count() !== $quantities->count()) {
            throw ValidationException::withMessages([
                'items' => 'One or more items do not belong to this purchase order.',
            ]);
        }

        foreach ($quantities as $itemId => $qty) {
            $poItem = $poItems[$itemId];

            if ((float) $qty > $poItem->remainingQuantity()) {
                throw ValidationException::withMessages([
                    'items' => "Cannot receive more than {$poItem->remainingQuantity()} remaining for {$poItem->product->name}.",
                ]);
            }
        }

        DB::transaction(function () use ($purchaseOrder, $data, $quantities, $poItems, $request) {
            $receipt = $purchaseOrder->goodsReceipts()->create([
                'received_date' => $data['received_date'],
                'received_by' => $request->user()->id,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($quantities as $itemId => $qty) {
                $poItem = $poItems[$itemId];

                $batch = $poItem->product->batches()->create([
                    'supplier_id' => $purchaseOrder->supplier_id,
                    'quantity' => $qty,
                    'unit_cost' => $poItem->unit_cost,
                    'received_at' => $data['received_date'],
                ]);

                $batch->stockMovements()->create([
                    'product_id' => $poItem->product_id,
                    'movement_type' => 'stock_in',
                    'quantity' => $qty,
                    'reference_type' => 'purchase_order',
                    'reference_id' => $purchaseOrder->id,
                    'movement_date' => $data['received_date'],
                    'performed_by' => $request->user()->id,
                    'notes' => 'Received against '.$purchaseOrder->po_number,
                ]);

                $receipt->items()->create([
                    'purchase_order_item_id' => $poItem->id,
                    'batch_id' => $batch->id,
                    'quantity_received' => $qty,
                ]);

                $poItem->quantity_received = (float) $poItem->quantity_received + (float) $qty;
                $poItem->save();
            }

            $allReceived = $purchaseOrder->items()->get()->every(fn ($item) => $item->remainingQuantity() <= 0);
            $purchaseOrder->update(['status' => $allReceived ? 'received' : 'partially_received']);
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('status', 'Goods received.');
    }
}
