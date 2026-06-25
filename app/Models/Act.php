<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Act extends Model
{
    protected $fillable = [
        'payment_id',
        'is_sent',
        'sent_at',
        'is_signed',
        'signed_at',
        'manager_comment'
    ];
    
    protected $casts = [
        'is_sent' => 'boolean',
        'is_signed' => 'boolean',
        'sent_at' => 'datetime',
        'signed_at' => 'datetime',
    ];
    
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function getStatusAttribute()
    {
        if (!$this->is_sent && !$this->is_signed) {
            return 'not_sent';
        }

        if ($this->is_sent && !$this->is_signed) {
            // Check if sent more than 30 days ago - attention required
            if ($this->sent_at && $this->sent_at->diffInDays(Carbon::now()) > 30) {
                return 'attention_required';
            }
            return 'awaiting_signature';
        }

        if ($this->is_sent && $this->is_signed) {
            return 'closed';
        }

        // Check if payment is old and act not sent
        if ($this->payment && $this->payment->payment_date) {
            $paymentDate = Carbon::parse($this->payment->payment_date);
            if ($paymentDate->diffInDays(Carbon::now()) > 60 && !$this->is_sent) {
                return 'attention_required';
            }
        }

        return 'attention_required';
    }
}
