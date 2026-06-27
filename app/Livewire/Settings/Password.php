<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Password extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed'],
            ]);
       
            $this->reset('current_password', 'password', 'password_confirmation');

    
        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

         $this->redirectRoute('settings.profile', navigate: true);
        $this->dispatch('show-notification','Password updated successfully');

    }
}