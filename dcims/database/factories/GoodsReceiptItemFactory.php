<?php

namespace Database\Factories;

use App\Models\GoodsReceipt;
use App\Models\ProductBatch;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoodsReceiptItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'goods_receipt_id' => GoodsReceipt::factory(),
            'purchase_order_item_id' => PurchaseOrderItem::factory(),
            'batch_id' => ProductBatch::factory(),
            'quantity_received' => fake()->randomFloat(2, 1, 50),
        ];
    }
}
