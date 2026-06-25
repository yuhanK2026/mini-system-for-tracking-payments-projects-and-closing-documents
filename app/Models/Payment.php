<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'project_id',
        'client_id',
        'payment_date',
        'amount',
        'payment_purpose',
        'service_stage'
    ];
    
    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];
    
    public function project() {
        return $this->belongsTo(Project::class);
    }
    
    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function act() {
        return $this->hasOne(Act::class);
    }
}
