<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ForgotPassword extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __($status));
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
