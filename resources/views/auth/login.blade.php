<x-guest-layout>

    <div class="card shadow border-0 overflow-hidden">

        {{-- Card Header with Branding --}}
        <div class="card-header bg-primary text-white py-4 text-center border-0">
            <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
                <i class="bi bi-heart-pulse-fill fs-1"></i>
                <h2 class="fw-bold mb-0">HealthLee</h2>
            </div>
            <p class="mb-0 opacity-75">Healthcare Appointment System</p>
        </div>

        <div class="card-body p-5">

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email Address')" class="fw-semibold" />
                    <x-text-input id="email"
                                  type="email"
                                  name="email"
                                  :value="old('email')"
                                  :class="$errors->get('email') ? 'is-invalid form-control-lg' : 'form-control-lg'"
                                  required
                                  autofocus
                                  autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" class="fw-semibold" />
                    <x-text-input id="password"
                                  type="password"
                                  name="password"
                                  :class="$errors->get('password') ? 'is-invalid form-control-lg' : 'form-control-lg'"
                                  required
                                  autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                {{-- Remember Me --}}
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="remember"
                               id="remember_me"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember_me">
                            {{ __('Remember me') }}
                        </label>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="d-grid">
                    <x-primary-button class="btn-lg py-3 justify-content-center">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>

                {{-- Forgot Password --}}
                @if (Route::has('password.request'))
                    <div class="text-center mt-4">
                        <a class="text-muted text-decoration-none" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    </div>
                @endif

            </form>
        </div>

        {{-- Card Footer --}}
        <div class="card-footer bg-light py-3 text-center border-0">
            <small class="text-muted">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-primary fw-medium text-decoration-none">Register here</a>
            </small>
        </div>

    </div>

</x-guest-layout>