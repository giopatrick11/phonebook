<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'Juan Dela Cruz',
                'phone' => '09171234567',
                'email' => 'juan@example.com',
                'address' => 'Cagayan de Oro City',
                'category' => 'Personal',
            ],
            [
                'name' => 'Maria Santos',
                'phone' => '09181234567',
                'email' => 'maria@example.com',
                'address' => 'Iligan City',
                'category' => 'Family',
            ],
            [
                'name' => 'Carlo Reyes',
                'phone' => '09191234567',
                'email' => 'carlo@example.com',
                'address' => 'Makati City',
                'category' => 'Work',
            ],
            [
                'name' => 'Ana Mendoza',
                'phone' => '09201234567',
                'email' => null,
                'address' => 'Quezon City',
                'category' => 'Emergency',
            ],
            [
                'name' => 'Paolo Garcia',
                'phone' => '09211234567',
                'email' => 'paolo@example.com',
                'address' => 'Davao City',
                'category' => 'Personal',
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::updateOrCreate(
                ['phone' => $contact['phone']],
                $contact
            );
        }
    }
}
