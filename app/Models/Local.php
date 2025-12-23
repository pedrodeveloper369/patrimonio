<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    protected $fillable = ['nome', 'id_tipolocal', 'parent_id'];

    public function tipo()
    {
        return $this->belongsTo(TipoLocal::class, 'id_tipolocal');
    }

    public function parent()
    {
        return $this->belongsTo(Local::class, 'parent_id');
    }

   public function children()
    {
        return $this->hasMany(Local::class, 'parent_id');
    }
}
