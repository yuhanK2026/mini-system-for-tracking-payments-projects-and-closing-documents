<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Act extends Model
{
    public function getStatusAttribute()
    {
        if (!$this->is_sent && !$this->is_signed) {
            return 'not_sent';
        }

        if ($this->is_sent && !$this->is_signed) {
            return 'awaiting_signature';
        }

        if ($this->is_sent && $this->is_signed) {
            return 'closed';
        }

        return 'attention_required';
    }
}
