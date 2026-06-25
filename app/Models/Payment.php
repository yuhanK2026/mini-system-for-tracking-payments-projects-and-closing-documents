<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function project() {
    return $this->belongsTo(Project::class);
    }

    public function act() {
        return $this->hasOne(Act::class);
    }
}
