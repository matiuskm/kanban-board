<?php

namespace App\Livewire;

use App\Models\Group;
use App\View\Components\KanbanLayout;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Board extends Component
{
    public $groups;

    public function mount() {
        $this->groups = Group::all();
    }
    #[Layout(KanbanLayout::class)]
    public function render()
    {
        return view('livewire.board');
    }

    public function sort($taskId, $targetSortPosition) {
        $task = \App\Models\Task::find($taskId);
        $currentSortPosition = $task->sort;

        if ($currentSortPosition == $targetSortPosition) {
            return;
        }

        DB::transaction(function () use($task, $currentSortPosition, $targetSortPosition) {
            $group = $task->group;

            $task->update(['sort' => -1]);

            $tasks = $group->tasks()->whereBetween('sort', [min($currentSortPosition, $targetSortPosition), max($currentSortPosition, $targetSortPosition)]);

            if ($currentSortPosition > $targetSortPosition) {
                $tasks->increment('sort');
            } else {
                $tasks->decrement('sort');
            }

            $task->update(['sort' => $targetSortPosition]);
        });
    }
}
