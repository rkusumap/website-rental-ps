<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\TransactionDetail;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\MethodPayment;

class Transaction extends Model
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $fillable = [];
    protected $table = "tb_transaction";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_trx';

    public function transaksi_detail(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'trx_trd', 'id_trx')->orderBy('order_trd', 'asc')->with('product');
    }
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id_customer', 'customer_trx');
    }

    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'id_supplier', 'supplier_trx');
    }

    public function method_payment(): HasOne
    {
        return $this->hasOne(MethodPayment::class, 'id_mp', 'method_payment_trx');
    }

}
