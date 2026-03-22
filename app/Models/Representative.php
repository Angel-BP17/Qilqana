<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Representative extends Model
{
    use HasFactory;

    protected $fillable = [
        'natural_person_id',
        'cargo',
        'fecha_desde',
    ];

    public function naturalPerson()
    {
        return $this->belongsTo(NaturalPerson::class);
    }

    public function legalEntities()
    {
        return $this->hasMany(LegalEntity::class);
    }
}
