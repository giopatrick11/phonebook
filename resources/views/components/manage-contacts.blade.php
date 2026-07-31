<?php

use App\Models\Contact;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $category = 'Personal';

    public ?int $contactId = null;
    public bool $isEditing = false;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],

            'phone' => ['required', 'digits_between:7,15', Rule::unique('contacts', 'phone')->ignore($this->contactId)],

            'email' => ['nullable', 'email:rfc', 'max:255'],

            'address' => ['nullable', 'string', 'max:500'],

            'category' => ['required', Rule::in(['Personal', 'Family', 'Work', 'Emergency'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The contact name is required.',
            'name.max' => 'The contact name must not exceed 100 characters.',

            'phone.required' => 'The phone number is required.',
            'phone.digits_between' => 'The phone number must contain only 7 to 15 digits.',
            'phone.unique' => 'This phone number already exists.',

            'email.email' => 'Please enter a valid email address, such as name@example.com.',
            'email.max' => 'The email address must not exceed 255 characters.',

            'address.max' => 'The address must not exceed 500 characters.',

            'category.required' => 'Please select a category.',
            'category.in' => 'Please select a valid category.',
        ];
    }

    public function updatedEmail(): void
    {
        $this->validateOnly('email');
    }

    public function updatedPhone(): void
    {
        $this->validateOnly('phone');
    }

    public function save(): void
    {
        $validated = $this->validate();

        Contact::create($validated);

        $this->resetForm();
        $this->resetPage();

        session()->flash('success', 'Contact added successfully.');
    }

    public function edit(int $id): void
    {
        $contact = Contact::findOrFail($id);

        $this->contactId = $contact->id;
        $this->name = $contact->name;
        $this->phone = $contact->phone;
        $this->email = $contact->email ?? '';
        $this->address = $contact->address ?? '';
        $this->category = $contact->category;
        $this->isEditing = true;

        $this->resetValidation();
    }

    public function update(): void
    {
        if ($this->contactId === null) {
            return;
        }

        $validated = $this->validate();

        Contact::findOrFail($this->contactId)->update($validated);

        $this->resetForm();

        session()->flash('success', 'Contact updated successfully.');
    }

    public function delete(int $id): void
    {
        Contact::findOrFail($id)->delete();

        if ($this->contactId === $id) {
            $this->resetForm();
        }

        $this->resetPage();

        session()->flash('success', 'Contact deleted successfully.');
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'phone', 'email', 'address', 'contactId', 'isEditing']);

        $this->category = 'Personal';

        $this->resetValidation();
    }

    public function with(): array
    {
        return [
            'contacts' => Contact::latest()->paginate(10),
        ];
    }
};
?>

<div class="mx-auto max-w-6xl p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">
                Manage Contacts
            </h1>

            <p class="text-gray-600">
                Create, edit, and delete contacts
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('phonebook') }}" wire:navigate class="rounded bg-gray-200 px-4 py-2">
                View Phonebook
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="rounded bg-red-600 px-4 py-2 text-white">
                    Logout
                </button>
            </form>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded border bg-white p-5">
            <h2 class="mb-4 text-lg font-semibold">
                {{ $isEditing ? 'Edit Contact' : 'Add Contact' }}
            </h2>

            <form wire:submit="{{ $isEditing ? 'update' : 'save' }}" class="space-y-4">
                <div>
                    <label for="name" class="mb-1 block">
                        Name
                    </label>

                    <input id="name" type="text" wire:model="name" class="w-full rounded border px-3 py-2">

                    @error('name')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="mb-1 block">
                        Phone Number
                    </label>

                    <input id="phone" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="15"
                        wire:model.live.debounce.500ms="phone" placeholder="09171234567"
                        class="w-full rounded border px-3 py-2">

                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block">
                        Email
                    </label>

                    <input id="email" type="email" wire:model.live.debounce.500ms="email"
                        placeholder="name@example.com" class="w-full rounded border px-3 py-2">

                    @error('email')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="address" class="mb-1 block">
                        Address
                    </label>

                    <textarea id="address" wire:model="address" rows="3" class="w-full rounded border px-3 py-2"></textarea>

                    @error('address')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="mb-1 block">
                        Category
                    </label>

                    <select id="category" wire:model="category" class="w-full rounded border px-3 py-2">
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

                    @error('category')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white">
                        {{ $isEditing ? 'Update Contact' : 'Save Contact' }}
                    </button>

                    @if ($isEditing)
                        <button type="button" wire:click="cancelEdit" class="rounded bg-gray-200 px-4 py-2">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <div class="md:col-span-2">
            <div class="mb-4 text-sm text-gray-600">
                Showing
                {{ $contacts->firstItem() ?? 0 }}
                to
                {{ $contacts->lastItem() ?? 0 }}
                of
                {{ $contacts->total() }}
                {{ $contacts->total() === 1 ? 'contact' : 'contacts' }}
            </div>

            <div id="manage-contacts-table" class="overflow-x-auto rounded border bg-white">
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
                                Category
                            </th>

                            <th class="px-4 py-3 text-right">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($contacts as $contact)
                            <tr wire:key="contact-{{ $contact->id }}" class="border-t">
                                <td class="px-4 py-3">
                                    <div class="font-medium">
                                        {{ $contact->name }}
                                    </div>

                                    <div class="text-sm text-gray-500">
                                        {{ $contact->email ?: 'No email' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    {{ $contact->phone }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $contact->category }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-3">
                                        <button type="button" wire:click="edit({{ $contact->id }})"
                                            class="text-blue-600 hover:underline">
                                            Edit
                                        </button>

                                        <button type="button" wire:click="delete({{ $contact->id }})"
                                            wire:confirm="Are you sure you want to delete this contact?"
                                            class="text-red-600 hover:underline">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    No contacts yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($contacts->hasPages())
                <div class="mt-5">
                    {{ $contacts->links(data: ['scrollTo' => '#manage-contacts-table']) }}
                </div>
            @endif
        </div>
    </div>
</div>
