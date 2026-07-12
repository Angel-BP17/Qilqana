<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LevelModality extends Model
{
    use HasFactory;

    protected $table = 'level_modalities';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Obtiene las resoluciones asociadas a esta modalidad/nivel.
     */
    public function resolucions(): HasMany
    {
        return $this->hasMany(Resolucion::class, 'level_modality_id');
    }
}
