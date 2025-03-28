<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Product;

class LogStockCashier extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuid;

    protected $table = "tb_log_stock_by_cashier";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_lsbc';
    protected $fillable = [];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id_product', 'product_lsbc');
    }
}
