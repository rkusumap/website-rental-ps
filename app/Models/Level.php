<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class Level extends Model
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = "ms_level";
    protected $primaryKey = 'id_level';
    public $incrementing = false;
    protected $fillable = [];
}
