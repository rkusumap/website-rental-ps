<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuid;

    protected $table = "ms_brand";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_brand';
    protected $fillable = [];
}
