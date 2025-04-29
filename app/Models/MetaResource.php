<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaResource extends Model
{
    protected $guarded = ['id'];
    public function translations()
    {
        return $this->hasMany(MetaResourceTranslation::class);
    }
}
