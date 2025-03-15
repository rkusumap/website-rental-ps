<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Unit;
use App\Models\Category;
use App\Models\Brand;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuid;

    protected $table = "ms_product";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_product';
    protected $fillable = [];

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id_unit', 'unit_product');
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id_category', 'category_product');
    }

    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'id_brand', 'brand_product');
    }

    public function getImageProductAttribute($value)
    {
        return asset('file/product/' . $value); // Assuming $value is the filename of the image
    }
}
