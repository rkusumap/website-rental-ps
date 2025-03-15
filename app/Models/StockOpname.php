<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Product;

class StockOpname extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuid;

    protected $table = "tb_stock_opname";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_so';
    protected $fillable = [];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id_product', 'product_so');
    }
}
