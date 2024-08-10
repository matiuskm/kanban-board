<?php

use App\Livewire\Board;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Livewire\Livewire;
use App\Models\Task;

use function Pest\Laravel\assertDatabaseHas;

it('shows all groups', function () {
    Group::factory(3)
        ->state(new Sequence(
            ['name' => 'To Do'],
            ['name' => 'In Progress'],
            ['name' => 'Done']
        ))
        ->create();

    Livewire::test(Board::class)
        ->assertSeeText([
            'To Do',
            'In Progress',
            'Done',
        ]);
});

it('it shows all tasks from a group', function () {
    /**
     * type App\Models\Group
     */
    $group = Group::factory()->create();
    Task::factory(3)
        ->state(new Sequence(
            ['description' => 'Task 1'],
            ['description' => 'Task 2'],
            ['description' => 'Task 3']
        ))
        ->for($group)
        ->create();

    Livewire::test(Board::class)
        ->assertSeeText([
            'Task 1',
            'Task 2',
            'Task 3',
        ]);
});

it('shows tasks in order', function () {
    $group = Group::factory()->create();
    Task::factory(3)
        ->state(new Sequence(
            ['sort' => 1, 'description' => 'Task 2'],
            ['sort' => 0, 'description' => 'Task 1'],
            ['sort' => 2, 'description' => 'Task 3']
        ))
        ->for($group)
        ->create();

    Livewire::test(Board::class)
        ->assertSeeTextInOrder([
            'Task 1',
            'Task 2',
            'Task 3',
        ]);
});

it('can move task to target position', function () {
    $group = Group::factory()->create();
    Task::factory(3)
        ->state(new Sequence(
            ['sort' => 0],
            ['sort' => 1],
            ['sort' => 2]
        ))
        ->for($group)
        ->create();

    Livewire::test(Board::class)
        ->call('sort', 1, 2);

    expect($group->tasks)
        ->find(1)->sort->toBe(2);
});

it('sort tasks after dragging down', function () {
    $group = Group::factory()->create();
    Task::factory(3)
        ->state(new Sequence(
            ['sort' => 0],
            ['sort' => 1],
            ['sort' => 2]
        ))
        ->for($group)
        ->create();

    Livewire::test(Board::class)
        ->call('sort', 1, 2);

    $group->refresh();

    expect($group->tasks)
        ->find(2)->sort->toBe(0)
        ->find(3)->sort->toBe(1);
});

it('sort tasks after dragging up', function () {
    $group = Group::factory()->create();
    Task::factory(3)
        ->state(new Sequence(
            ['sort' => 0],
            ['sort' => 1],
            ['sort' => 2]
        ))
        ->for($group)
        ->create();

    Livewire::test(Board::class)
        ->call('sort', 3, 0);

    $group->refresh();

    expect($group->tasks)
        ->find(1)->sort->toBe(1)
        ->find(2)->sort->toBe(2);
});

it('can create tasks', function () {
    $group = Group::factory()->create();
    Task::factory(2)
        ->state(new Sequence(
            ['description' => 'Task 1', 'sort' => 0],
            ['description' => 'Task 2', 'sort' => 1],
        ))
        ->for($group)
        ->create();

    Livewire::test(Board::class)
        ->set('description', 'Task 3')
        ->call('createTask', $group->id);

    assertDatabaseHas('tasks', [
        'description' => 'Task 3',
    ]);
});

it('properly sorts new tasks', function () {
    $group = Group::factory()->create();
    Task::factory(2)
        ->state(new Sequence(
            ['sort' => 0],
            ['sort' => 1],
        ))
        ->for($group)
        ->create();

    Livewire::test(Board::class)
        ->set('description', 'New Task')
        ->call('createTask', $group->id);

    assertDatabaseHas('tasks', [
        'description' => 'New Task',
        'sort' => 2,
    ]);
});
