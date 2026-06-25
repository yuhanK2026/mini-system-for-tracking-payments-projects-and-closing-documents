<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Project;
use App\Models\Client;
use App\Models\Act;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Apply filters
        $query = Payment::with(['project.client', 'act']);
        
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }
        
        if ($request->has('act_status')) {
            $query->whereHas('act', function ($q) use ($request) {
                switch ($request->act_status) {
                    case 'not_sent':
                        $q->where('is_sent', false)->where('is_signed', false);
                        break;
                    case 'awaiting_signature':
                        $q->where('is_sent', true)->where('is_signed', false);
                        break;
                    case 'closed':
                        $q->where('is_sent', true)->where('is_signed', true);
                        break;
                }
            });
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_purpose', 'like', "%{$search}%")
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('client', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                  });
            });
        }
        
        $payments = $query->get();
        $projects = Project::with(['payments.act', 'client'])->get();
        $clients = Client::with(['projects.payments'])->get();
        
        // Calculate project statistics
        $projects = $projects->map(function($project) {
            $project->total_payments = $project->payments->sum('amount');
            $project->payments_count = $project->payments->count();
            
            $closedActs = $project->payments->filter(function($payment) {
                return $payment->act && $payment->act->is_sent && $payment->act->is_signed;
            });
            $project->closed_acts_count = $closedActs->count();
            
            $unclosedActs = $project->payments->filter(function($payment) {
                return !$payment->act || !$payment->act->is_signed;
            });
            $project->unclosed_acts_count = $unclosedActs->count();
            
            return $project;
        });

        return response()->json([
            'summary' => $this->summary($payments, $projects),
            'projects' => $projects,
            'payments' => $payments,
            'clients' => $clients,
            'filters' => [
                'projects' => Project::select('id', 'name')->get(),
                'clients' => Client::select('id', 'name')->get(),
                'act_statuses' => [
                    ['value' => 'not_sent', 'label' => 'Not Sent'],
                    ['value' => 'awaiting_signature', 'label' => 'Awaiting Signature'],
                    ['value' => 'closed', 'label' => 'Closed'],
                    ['value' => 'attention_required', 'label' => 'Attention Required']
                ]
            ]
        ]);
    }
    
    public function updateActStatus(Request $request, $actId)
    {
        $request->validate([
            'is_sent' => 'boolean',
            'is_signed' => 'boolean',
            'manager_comment' => 'nullable|string'
        ]);
        
        $act = Act::findOrFail($actId);
        
        $updates = [];
        if ($request->has('is_sent') && $request->is_sent) {
            $updates['is_sent'] = true;
            $updates['sent_at'] = now();
        }
        
        if ($request->has('is_signed') && $request->is_signed) {
            $updates['is_signed'] = true;
            $updates['signed_at'] = now();
        }
        
        if ($request->has('manager_comment')) {
            $updates['manager_comment'] = $request->manager_comment;
        }
        
        $act->update($updates);
        
        return response()->json($act);
    }

    private function summary($payments, $projects)
    {
        $now = now();
        $thirtyDaysAgo = $now->subDays(30);
        
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
                
            // Additional metrics from requirements
            'attention_required' => $payments
                ->filter(fn($p) => $p->act && $p->act->status === 'attention_required')
                ->count(),
                
            'recent_payments_amount' => $payments
                ->filter(fn($p) => $p->payment_date >= $thirtyDaysAgo)
                ->sum('amount'),
        ];
    }
}