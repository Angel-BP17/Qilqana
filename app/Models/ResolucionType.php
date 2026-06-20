<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResolucionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abreviacion',
        'description',
    ];

    public function resolucions()
    {
        return $this->hasMany(Resolucion::class);
    }

    public function asuntoTypes()
    {
        return $this->belongsToMany(AsuntoType::class, 'asunto_type_resolucion_type');
    }
}
