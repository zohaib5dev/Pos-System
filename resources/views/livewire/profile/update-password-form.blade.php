<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('notify', [
            'message' => 'Password updated successfully',
            'type' => 'success'
        ]);
    }
}; ?>

<div class="col-md-6">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                Update Password
            </h3>

        </div>

        <form wire:submit="updatePassword">
            <div class="card-body">

                <!-- Current Password -->
                <div class="form-group">
                    <label for="current_password">
                        Current Password <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                        <input type="password"
                            wire:model="current_password"
                            id="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            placeholder="Enter your current password"
                            autocomplete="current-password">
                    </div>
                    @error('current_password')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="form-group">
                    <label for="password">
                        New Password <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                        </div>
                        <input type="password"
                            wire:model="password"
                            id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Enter new password"
                            autocomplete="new-password">
                    </div>
                    @error('password')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror


                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation">
                        Confirm New Password <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                        <input type="password"
                            wire:model="password_confirmation"
                            id="password_confirmation"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Confirm new password"
                            autocomplete="new-password">
                    </div>

                    <!-- Password Match Indicator -->
                    @if($password && $password_confirmation)
                    <div class="mt-1">
                        @if($password === $password_confirmation)
                        <small class="text-success">
                            <i class="fas fa-check-circle mr-1"></i>
                            Passwords match
                        </small>
                        @else
                        <small class="text-danger">
                            <i class="fas fa-times-circle mr-1"></i>
                            Passwords do not match
                        </small>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:target="updatePassword">
                        <i class="fas fa-save mr-1"></i> {{ __('Update Password') }}
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