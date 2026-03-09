<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resolucion extends Model
{
    use HasFactory;
    protected $casts = [
        'fecha' => 'datetime',
    ];

    protected $fillable = [
        'rd',
        'fecha',
        'periodo',
        'nombres_apellidos',
        'dni',
        'asunto',
        'procedencia',
    ];

    protected $dates = [
        'fecha',
        'created_at',
        'updated_at'
    ];

    public function charge()
    {
        return $this->hasOne(Charge::class);
    }
}
