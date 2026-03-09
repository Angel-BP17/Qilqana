<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalEntity extends Model
{
    use HasFactory;

    protected $table = 'legal_entities';

    protected $fillable = [
        'ruc',
        'razon_social',
        'district',
        'representative_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }
}
