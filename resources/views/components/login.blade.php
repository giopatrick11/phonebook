<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $validated = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        if (!Auth::attempt($credentials, $this->remember)) {
            $this->addError('email', 'The provided credentials do not match our records.');

            return;
        }

        request()->session()->regenerate();

        $this->redirectRoute('contacts.manage', navigate: true);
    }
};
?>

<div class="mx-auto max-w-md p-6">
    <div class="rounded border bg-white p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold">
                Admin Login
            </h1>

            <p class="mt-1 text-gray-600">
                Sign in to manage phonebook contacts.
            </p>
        </div>

        <form wire:submit="login" class="space-y-4">
            <div>
                <label for="email" class="mb-1 block font-medium">
                    Email
                </label>

                <input id="email" type="email" wire:model="email" autocomplete="email"
                    class="w-full rounded border px-3 py-2" placeholder="admin@example.com">

                @error('email')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block font-medium">
                    Password
                </label>

                <input id="password" type="password" wire:model="password" autocomplete="current-password"
                    class="w-full rounded border px-3 py-2">

                @error('password')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- <label class="flex items-center gap-2">
                <input type="checkbox" wire:model="remember">

                <span>Remember me</span>
            </label> --}}

            <button type="submit" class="w-full rounded bg-blue-600 px-4 py-2 text-white">
                Sign In
            </button>
        </form>

        <div class="mt-5 text-center">
            <a href="{{ route('phonebook') }}" wire:navigate class="text-blue-600 hover:underline">
                Return to Phonebook
            </a>
        </div>
    </div>
</div>
