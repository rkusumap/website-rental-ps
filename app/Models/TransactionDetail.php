<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use App\Models\Product;
use App\Models\Transaction;

class TransactionDetail extends Model
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $fillable = [];
    protected $table = "tb_transaction_detail";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_trd';

    public function product()
    {
        return $this->hasOne(Product::class, 'id_product', 'product_trd'); // Replace 'product_trd' with the correct foreign key in `TransactionDetail`
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'trx_trd', 'id_trx');
    }
}
