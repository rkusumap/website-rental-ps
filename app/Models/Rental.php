<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\RentalDetail;
use App\Models\User;

class Rental extends Model
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $fillable = [];
    protected $table = "tb_rental";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_rental';

    public function rental_detail(): HasMany
    {
        return $this->hasMany(RentalDetail::class, 'rental_rtd', 'id_rental')->orderBy('date_rtd', 'asc')->with('product');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_rental');
    }

    public function rental_detail_one()
    {
        return $this->hasOne(RentalDetail::class, 'rental_rtd', 'id_rental')->with('product');
    }
}
