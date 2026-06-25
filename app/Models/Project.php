<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'status'
    ];
    
    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }
    
    // Accessor for project status with better labels
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'completed' => 'Completed',
            'on_hold' => 'On Hold',
            default => ucfirst($this->status)
        };
    }
}
