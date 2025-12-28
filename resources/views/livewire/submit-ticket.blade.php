<div>
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="create">
        {{ $this->form }}

        <div class="mt-4">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-500">
                Submit Ticket
            </button>
        </div>
    </form>
</div>
