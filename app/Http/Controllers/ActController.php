<?php

namespace App\Http\Controllers;

use App\Models\Act;
use App\Models\Payment;
use Illuminate\Http\Request;

class ActController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'is_sent' => 'boolean',
            'is_signed' => 'boolean',
            'manager_comment' => 'nullable|string'
        ]);
        
        // Check if act already exists for this payment
        $act = Act::where('payment_id', $request->payment_id)->first();
        
        if ($act) {
            return response()->json(['error' => 'Act already exists for this payment'], 409);
        }
        
        $act = Act::create([
            'payment_id' => $request->payment_id,
            'is_sent' => $request->is_sent ?? false,
            'is_signed' => $request->is_signed ?? false,
            'manager_comment' => $request->manager_comment,
            'sent_at' => $request->is_sent ? now() : null,
            'signed_at' => $request->is_signed ? now() : null,
        ]);
        
        return response()->json($act, 201);
    }
}