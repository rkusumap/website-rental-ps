<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Uuid;
use App\Models\Module;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Role extends Model
{
    use HasFactory;
    use Uuid;


    protected $table = "ms_groupmodule";
    protected $primaryKey = 'id_gmd';
    public $incrementing = false;
    protected $fillable = [];

    public function module(): HasOne
    {
        return $this->hasOne(Module::class, 'id_module', 'module_gmd');
    }
}
