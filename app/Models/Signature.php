<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;

    protected $fillable = [
        'charge_id',
        'signature_status',
        'signature_root',
        'evidence_root',
        'signed_by',
        'assigned_to',
        'signature_requested_at',
        'signature_completed_at',
        'signature_comment',
        'titularidad',
        'parentesco',
        'carta_poder_path',
    ];

    protected $casts = [
        'signature_requested_at' => 'datetime',
        'signature_completed_at' => 'datetime',
        'titularidad' => 'boolean',
    ];

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
