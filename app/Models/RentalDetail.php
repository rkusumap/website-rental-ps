<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Product;
use App\Models\Rental;

class RentalDetail extends Model
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $fillable = [];
    protected $table = "tb_rental_detail";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_rtd';

    public function product()
    {
        return $this->hasOne(Product::class, 'id_product', 'product_rtd'); // Replace 'product_trd' with the correct foreign key in `TransactionDetail`
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_rtd', 'id_rental');
    }
}
