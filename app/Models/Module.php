<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Role;

class Module extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuid;

    protected $table = "ms_module";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_module';
    protected $fillable = [];

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'induk_module', 'id_module')->orderBy('order_module', 'asc');
    }

    public function role($idLevel): HasOne
    {
        return $this->hasOne(Role::class, 'module_gmd', 'id_module')->where('level_gmd', $idLevel);
    }
}
