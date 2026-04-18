<x-layouts::auth :title="__('Two-factor authentication')">
    <div class="flex flex-col gap-6">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $dispatch('clear-2fa-auth-code');

                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : $dispatch('focus-2fa-auth-code');
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    :title="__('Authentication code')"
                    :description="__('Enter the authentication code provided by your authenticator application.')"
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    :title="__('Recovery code')"
                    :description="__('Please confirm access to your account by entering one of your emergency recovery codes.')"
                />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}" class="auth-form">
                @csrf

                <div class="space-y-5">
                    <div x-show="!showRecoveryInput">
                        <label for="code" class="ops-field-label">{{ __('Authentication code') }}</label>
                        <input
                            id="code"
                            type="text"
                            name="code"
                            x-model="code"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            maxlength="6"
                            class="ops-control text-center tracking-[0.3em]"
                            placeholder="123456"
                        >
                        @error('code') <span class="ops-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div x-show="showRecoveryInput">
                        <label for="recovery_code" class="ops-field-label">{{ __('Recovery code') }}</label>
                        <input
                            id="recovery_code"
                            type="text"
                            name="recovery_code"
                            x-ref="recovery_code"
                            x-bind:required="showRecoveryInput"
                            autocomplete="one-time-code"
                            x-model="recovery_code"
                            class="ops-control"
                        >

                        @error('recovery_code')
                            <span class="ops-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="ops-button ops-button--primary w-full">
                        {{ __('Continue') }}
                    </button>
                </div>

                <div class="ops-text-muted mt-5 space-x-0.5 text-center text-sm leading-5">
                    <span>{{ __('or you can') }}</span>
                    <div class="auth-link inline cursor-pointer">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('login using a recovery code') }}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('login using an authentication code') }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
