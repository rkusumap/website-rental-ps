<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuid;

    protected $table = "ms_category";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_category';
    protected $fillable = ['image_category']; // Add this if it's missing

    public function getImageAttribute()
    {
        $image = $this->attributes['image_category'] ?? null; // Get image_category field
        return $image ? asset('file/category/' . $image) : asset('file/category/default.webp');
    }
}
