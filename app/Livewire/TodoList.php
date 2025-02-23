<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    public $name;
    public $search;
    public $editingTodoId;
    public $editingTodoName;

    protected $rules = [
        'name' => 'required|min:3|max:50',
        'editingTodoName' => 'required|min:3|max:50',
    ];

    public function create()
    {
        $validated = $this->validateOnly('name');

        Todo::create($validated);
        
        $this->reset('name');

        session()->flash('success', 'Todo created successfully.');
    }

    public function delete(Todo $todo)
    {
        $todo->delete();
    }

    public function edit(Todo $todo)
    {
        $this->editingTodoId = $todo->id;
        $this->editingTodoName = Todo::find($todo->id)->name;
    }
    public function toggle(Todo $todo)
    {
        $todo = Todo::find($todo->id);
        $todo->completed = !$todo->completed;
        $todo->save();
    }
    public function cancel()
    {
        $this->reset('editingTodoName', 'editingTodoId');
    }

    public function update()
    {
        $validated = $this->validateOnly('editingTodoName');

        Todo::find($this->editingTodoId)->update(
            [
                'name' => $this->editingTodoName
            ]
        );

        $this->cancel();
    }

    public function render()
    {
        return view('livewire.todo-list',[
            'todos' => Todo::latest()->where('name', 'like', '%' . $this->search . '%') ->paginate(5)
        ]);
    }
}
