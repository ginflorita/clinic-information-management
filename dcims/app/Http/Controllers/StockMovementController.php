<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StockMovementController extends Controller
{
    public function stockOut(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'batch_id' => ['required', Rule::exists('product_batches', 'id')->where('product_id', $product->id)],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string'],
        ]);

        $batch = ProductBatch::findOrFail($data['batch_id']);

        if ($data['quantity'] > $batch->quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Only '.$batch->quantity.' available in this batch.',
            ]);
        }

        DB::transaction(function () use ($product, $batch, $data, $request) {
            $batch->quantity = $batch->quantity - $data['quantity'];
            $batch->save();

            $batch->stockMovements()->create([
                'product_id' => $product->id,
                'movement_type' => 'stock_out',
                'quantity' => -$data['quantity'],
                'movement_date' => now()->toDateString(),
                'performed_by' => $request->user()->id,
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()->route('inventory.show', $product)->with('status', 'Stock recorded as used.');
    }

    public function adjust(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'batch_id' => ['required', Rule::exists('product_batches', 'id')->where('product_id', $product->id)],
            'delta' => ['required', 'numeric', 'not_in:0'],
            'notes' => ['required', 'string'],
        ]);

        $batch = ProductBatch::findOrFail($data['batch_id']);

        if (($batch->quantity + $data['delta']) < 0) {
            throw ValidationException::withMessages([
                'delta' => 'This adjustment would take the batch below zero.',
            ]);
        }

        DB::transaction(function () use ($product, $batch, $data, $request) {
            $batch->quantity = $batch->quantity + $data['delta'];
            $batch->save();

            $batch->stockMovements()->create([
                'product_id' => $product->id,
                'movement_type' => 'adjustment',
                'quantity' => $data['delta'],
                'movement_date' => now()->toDateString(),
                'performed_by' => $request->user()->id,
                'notes' => $data['notes'],
            ]);
        });

        return redirect()->route('inventory.show', $product)->with('status', 'Stock adjusted.');
    }
}
