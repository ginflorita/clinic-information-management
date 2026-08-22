<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Supplier;
use Illuminate\View\View;

class InventoryController extends Controller
{
    const EXPIRY_WARNING_DAYS = 30;

    public function index(): View
    {
        $products = Product::with(['category', 'unit'])
            ->withSum('batches', 'quantity')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('inventory.index', [
            'products' => $products,
            'lowStockCount' => $products->filter->isLowStock()->count(),
            'expiringBatchCount' => ProductBatch::expiringWithin(self::EXPIRY_WARNING_DAYS)->count(),
            'expiryWarningDays' => self::EXPIRY_WARNING_DAYS,
        ]);
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'unit']);

        return view('inventory.show', [
            'product' => $product,
            'batches' => $product->batches()->orderBy('expiry_date')->orderBy('received_at')->get(),
            'movements' => $product->stockMovements()->with(['batch', 'performedByUser'])->latest('movement_date')->latest('id')->take(50)->get(),
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'expiryWarningDays' => self::EXPIRY_WARNING_DAYS,
        ]);
    }
}
