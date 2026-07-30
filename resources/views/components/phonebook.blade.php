<?php

use App\Models\Contact;
use Livewire\Component;

new class extends Component {
    public string $search = '';

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
                            ->orWhere('address', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name')
                ->get(),
        ];
    }
};
?>

<div class="mx-auto max-w-6xl p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Phonebook</h1>
            <p class="text-gray-600">Search and view contacts</p>
        </div>

        <a href="{{ route('contacts.manage') }}" wire:navigate class="rounded bg-blue-600 px-4 py-2 text-white">
            Manage Contacts
        </a>
    </div>

    <div class="mb-4">
        <label for="search" class="mb-1 block font-medium">
            Search Contacts
        </label>

        <input id="search" type="search" wire:model.live.debounce.300ms="search"
            placeholder="Search by name, phone, email, address, or category" class="w-full rounded border px-3 py-2">
    </div>

    <div class="overflow-x-auto rounded border bg-white">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Phone</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Address</th>
                    <th class="px-4 py-3 text-left">Category</th>
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
                            @if ($search)
                                No contacts match "{{ $search }}".
                            @else
                                No contacts available.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
