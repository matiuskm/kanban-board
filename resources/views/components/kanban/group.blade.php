@props(['group'])

<div x-data="{ showAddTaskForm: false }" x-on:task-created.window="showAddTaskForm = false" {{ $attributes->whereStartsWith('wire:') }} class="flex flex-col flex-shrink-0 self-start max-h-full w-80 ring-1 bg-gray-100 dark:bg-gray-900 ring-gray-950/10 dark:ring-white/10 rounded-md">
    <h3 class="flex-shrink-0 p-3 pb-1 text-sm font-medium">{{ $group->name }}</h3>
    <div class="flex-1 min-h-0 overflow-y-auto" style="scrollbar-width: thin;">
        <div {{ $attributes->whereStartsWith('x-sort') }} class="pt-1 pb-3 flex flex-col gap-3 px-3">
            {{ $slot }}
        </div>
    </div>
    <div class="p-3">
        <template x-if="showAddTaskForm == true">
            <form wire:submit="createTask({{ $group->getKey() }})">
                <x-text-input wire:model="description" placeholder="Task description" />
                @error('description')
                    <span class="text-rose-500 dark:text-rose-400 text-xs pt-1">{{ $message }}</span>
                @enderror
                <div class="flex items-center justify-start gap-2 pt-2">
                    <x-primary-button>
                        Save
                    </x-primary-button>
                    <x-secondary-button @click="showAddTaskForm = false" type="button">
                        Cancel
                    </x-secondary-button>
                </div>
            </form>
        </template>

        <button x-show="showAddTaskForm == false" @click="showAddTaskForm = true" class="flex items-center justify-start gap-1 w-full" type="button">
            <x-heroicon-o-plus-circle class="size-5 text-gray-500 dark:text-gray-400" />
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Add a card</span>
        </button>
    </div>
</div>
