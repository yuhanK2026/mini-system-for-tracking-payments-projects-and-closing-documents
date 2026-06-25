<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['project.client', 'act'])->get();
        $projects = Project::with('payments.act')->get();

        return response()->json([
            'summary' => $this->summary($payments, $projects),
            'projects' => $projects,
            'payments' => $payments,
        ]);
    }

    private function summary($payments, $projects)
    {
        return [
            'total_amount' => $payments->sum('amount'),
            'payments_count' => $payments->count(),
            'projects_count' => $projects->count(),

            'closed_acts_amount' => $payments
                ->filter(fn($p) => $p->act && $p->act->is_sent && $p->act->is_signed)
                ->sum('amount'),

            'unclosed_acts_amount' => $payments
                ->filter(fn($p) => !$p->act || !$p->act->is_signed)
                ->sum('amount'),

            'no_act_sent' => $payments
                ->filter(fn($p) => !$p->act || !$p->act->is_sent)
                ->count(),

            'sent_not_signed' => $payments
                ->filter(fn($p) => $p->act && $p->act->is_sent && !$p->act->is_signed)
                ->count(),
        ];
    }
}