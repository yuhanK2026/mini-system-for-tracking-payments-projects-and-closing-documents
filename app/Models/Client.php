<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'inn',
        'ogrn'
    ];
    
    public function projects() {
        return $this->hasMany(Project::class);
    }
    
    public function payments() {
        return $this->hasMany(Payment::class);
    }
}
