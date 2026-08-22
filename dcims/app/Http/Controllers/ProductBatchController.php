<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBatchController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'batch_number' => ['nullable', 'string', 'max:255'],
            'lot_number' => ['nullable', 'string', 'max:255'],
            'expiry_date' => ['nullable', 'date'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'received_at' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($product, $data, $request) {
            $batch = $product->batches()->create($data);

            $batch->stockMovements()->create([
                'product_id' => $product->id,
                'movement_type' => 'stock_in',
                'quantity' => $data['quantity'],
                'movement_date' => $data['received_at'],
                'performed_by' => $request->user()->id,
                'notes' => 'Batch received.',
            ]);
        });

        return redirect()->route('inventory.show', $product)->with('status', 'Stock received.');
    }
}
