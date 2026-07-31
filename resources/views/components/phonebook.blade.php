<?php

use App\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'categoryFilter']);

        $this->resetPage();
    }

    public function with(): array
    {
        $search = trim($this->search);

        return [
            'contacts' => Contact::query()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
                    });
                })
                ->when($this->categoryFilter !== '', function ($query) {
                    $query->where('category', $this->categoryFilter);
                })
                ->orderBy('name')
                ->paginate(10),
        ];
    }
};
?>

<div class="mx-auto max-w-6xl p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">
                Phonebook
            </h1>

            <p class="text-gray-600">
                Search and view contacts
            </p>
        </div>

        <a href="{{ route('contacts.manage') }}" wire:navigate class="rounded bg-blue-600 px-4 py-2 text-white">
            Manage Contacts
        </a>
    </div>

    <div class="mb-4 grid gap-4 md:grid-cols-3">
        <div class="md:col-span-2">
            <label for="search" class="mb-1 block font-medium">
                Search Contacts
            </label>

            <input id="search" type="search" wire:model.live.debounce.300ms="search"
                placeholder="Search by name, phone number, email, or address" class="w-full rounded border px-3 py-2">
        </div>

        <div>
            <label for="categoryFilter" class="mb-1 block font-medium">
                Filter by Category
            </label>

            <select id="categoryFilter" wire:model.live="categoryFilter" class="w-full rounded border px-3 py-2">
                <option value="">
                    All Categories
                </option>

                <option value="Personal">
                    Personal
                </option>

                <option value="Family">
                    Family
                </option>

                <option value="Work">
                    Work
                </option>

                <option value="Emergency">
                    Emergency
                </option>
            </select>
        </div>
    </div>

    <div class="mb-4 flex items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Showing
            {{ $contacts->firstItem() ?? 0 }}
            to
            {{ $contacts->lastItem() ?? 0 }}
            of
            {{ $contacts->total() }}
            {{ $contacts->total() === 1 ? 'contact' : 'contacts' }}
        </p>

        @if ($search !== '' || $categoryFilter !== '')
            <button type="button" wire:click="clearFilters" class="text-sm text-blue-600 hover:underline">
                Clear filters
            </button>
        @endif
    </div>

    <div id="contacts-table" class="overflow-x-auto rounded border bg-white">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">
                        Name
                    </th>

                    <th class="px-4 py-3 text-left">
                        Phone Number
                    </th>

                    <th class="px-4 py-3 text-left">
                        Email
                    </th>

                    <th class="px-4 py-3 text-left">
                        Address
                    </th>

                    <th class="px-4 py-3 text-left">
                        Category
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($contacts as $contact)
                    <tr wire:key="contact-{{ $contact->id }}" class="border-t">
                        <td class="px-4 py-3 font-medium">
                            {{ $contact->name }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $contact->phone }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $contact->email ?: 'N/A' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $contact->address ?: 'N/A' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $contact->category }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            @if ($search !== '' || $categoryFilter !== '')
                                No contacts match the selected filters.
                            @else
                                No contacts available.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($contacts->hasPages())
        <div class="mt-5">
            {{ $contacts->links(data: ['scrollTo' => '#contacts-table']) }}
        </div>
    @endif
</div>
