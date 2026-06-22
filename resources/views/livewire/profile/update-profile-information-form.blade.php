<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        $this->dispatch('notify', [
            'message' => 'Profile updated successfully',
            'type' => 'success'
        ]);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        $this->dispatch('notify', [
                'message' => 'Verification link sent to your email',
                'type' => 'success'
            ]);
 
    }
}; ?>


<div class="col-md-6">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Profile Information</h3>
        </div>


        <form wire:submit="updateProfileInformation">
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input type="text"
                        wire:model="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Enter your name"
                        required>
                    @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="text-danger">*</span></label>
                    <input type="email"
                        wire:model="email"
                        id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="Enter your email"
                        required>
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                    <div class="mt-3">
                        <div class="alert alert-warning">
                            <p class="mb-2">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ __('Your email address is unverified.') }}
                            </p>
                            <button type="button"
                                wire:click="sendVerification"
                                class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-envelope"></i>
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success mt-2">
                            <i class="fas fa-check-circle"></i>
                            {{ __('A new verification link has been sent to your email address.') }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:target="updateProfileInformation">
                        <i class="fas fa-save"></i> {{ __('Save Changes') }}
                    </span>

                </button>

                @if(session()->has('message') && session('message_type') === 'success')
                <span class="text-success ml-3">
                    <i class="fas fa-check-circle"></i> {{ session('message') }}
                </span>
                @endif
            </div>
        </form>
    </div>


</div>