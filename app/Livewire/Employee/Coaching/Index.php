<?php

namespace App\Livewire\Employee\Coaching;

use App\Models\ConversationAnalysis;
use App\Services\EmployeeContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.employee')]
#[Title('مربیگری هوش مصنوعی')]
class Index extends Component
{
    public function render()
    {
        $analyses = ConversationAnalysis::query()
            ->where('organization_user_id', EmployeeContext::membership()->id)
            ->latest('analyzed_at')
            ->limit(20)
            ->get();

        $strengths = [];
        $weaknesses = [];
        $actions = [];

        foreach ($analyses as $analysis) {
            foreach ($analysis->strengths_json ?? [] as $s) {
                $strengths[$s] = ($strengths[$s] ?? 0) + 1;
            }
            foreach ($analysis->weaknesses_json ?? [] as $w) {
                $weaknesses[$w] = ($weaknesses[$w] ?? 0) + 1;
            }
            foreach ($analysis->next_actions_json ?? [] as $a) {
                $actions[$a] = ($actions[$a] ?? 0) + 1;
            }
        }

        arsort($strengths);
        arsort($weaknesses);
        arsort($actions);

        return view('livewire.employee.coaching.index', [
            'strengths' => array_slice($strengths, 0, 8, true),
            'weaknesses' => array_slice($weaknesses, 0, 8, true),
            'actions' => array_slice($actions, 0, 8, true),
            'latest' => $analyses->first(),
        ]);
    }
}
