<x-guest-layout>

    <div class="card shadow border-0 overflow-hidden">

        {{-- Card Header --}}
        <div class="card-header bg-primary text-white py-4 text-center border-0">
            <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
                <i class="bi bi-heart-pulse-fill fs-1"></i>
                <h2 class="fw-bold mb-0">HealthLee</h2>
            </div>
            <p class="mb-0 opacity-75">Create your account</p>
        </div>

        <div class="card-body p-5">

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Role Selection --}}
                <div class="mb-4">
                    <x-input-label for="role" :value="__('Register as')" class="fw-semibold" />
                    <select id="role"
                            name="role"
                            class="form-select form-select-lg @error('role') is-invalid @enderror"
                            required>
                        <option value="" disabled selected>Select role...</option>
                        <option value="patient" {{ old('role') === 'patient' ? 'selected' : '' }}>Patient</option>
                        <option value="admin"   {{ old('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" />
                </div>

                <hr class="my-4">
                <p class="fw-semibold text-muted mb-3">Personal Information</p>

                {{-- Name Row --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <x-input-label for="first_name" :value="__('First Name')" class="fw-semibold" />
                        <x-text-input id="first_name"
                                      type="text"
                                      name="first_name"
                                      :value="old('first_name')"
                                      :class="$errors->get('first_name') ? 'is-invalid' : ''"
                                      required
                                      autocomplete="given-name" />
                        <x-input-error :messages="$errors->get('first_name')" />
                    </div>
                    <div class="col-md-4">
                        <x-input-label for="middle_name" :value="__('Middle Name')" class="fw-semibold" />
                        <x-text-input id="middle_name"
                                      type="text"
                                      name="middle_name"
                                      :value="old('middle_name')"
                                      :class="$errors->get('middle_name') ? 'is-invalid' : ''"
                                      autocomplete="additional-name" />
                        <x-input-error :messages="$errors->get('middle_name')" />
                    </div>
                    <div class="col-md-4">
                        <x-input-label for="last_name" :value="__('Last Name')" class="fw-semibold" />
                        <x-text-input id="last_name"
                                      type="text"
                                      name="last_name"
                                      :value="old('last_name')"
                                      :class="$errors->get('last_name') ? 'is-invalid' : ''"
                                      required
                                      autocomplete="family-name" />
                        <x-input-error :messages="$errors->get('last_name')" />
                    </div>
                </div>

                {{-- Gender & Age Row --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <x-input-label for="gender" :value="__('Gender')" class="fw-semibold" />
                        <select id="gender"
                                name="gender"
                                class="form-select @error('gender') is-invalid @enderror"
                                required>
                            <option value="" disabled selected>Select gender...</option>
                            <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="age" :value="__('Age')" class="fw-semibold" />
                        <x-text-input id="age"
                                      type="number"
                                      name="age"
                                      :value="old('age')"
                                      :class="$errors->get('age') ? 'is-invalid' : ''"
                                      min="1"
                                      max="120"
                                      required />
                        <x-input-error :messages="$errors->get('age')" />
                    </div>
                </div>

                {{-- Contact --}}
                <div class="mb-3">
                    <x-input-label for="contact_number" :value="__('Contact Number')" class="fw-semibold" />
                    <x-text-input id="contact_number"
                                  type="text"
                                  name="contact_number"
                                  :value="old('contact_number')"
                                  :class="$errors->get('contact_number') ? 'is-invalid' : ''"
                                  required />
                    <x-input-error :messages="$errors->get('contact_number')" />
                </div>

                <hr class="my-4">
                <p class="fw-semibold text-muted mb-3">Address</p>

                {{-- Street --}}
                <div class="mb-3">
                    <x-input-label for="street" :value="__('Street')" class="fw-semibold" />
                    <x-text-input id="street"
                                  type="text"
                                  name="street"
                                  :value="old('street')"
                                  :class="$errors->get('street') ? 'is-invalid' : ''" />
                    <x-input-error :messages="$errors->get('street')" />
                </div>

                {{-- Barangay & City --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <x-input-label for="barangay" :value="__('Barangay')" class="fw-semibold" />
                        <x-text-input id="barangay"
                                      type="text"
                                      name="barangay"
                                      :value="old('barangay')"
                                      :class="$errors->get('barangay') ? 'is-invalid' : ''" />
                        <x-input-error :messages="$errors->get('barangay')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="city" :value="__('City')" class="fw-semibold" />
                        <x-text-input id="city"
                                      type="text"
                                      name="city"
                                      :value="old('city')"
                                      :class="$errors->get('city') ? 'is-invalid' : ''" />
                        <x-input-error :messages="$errors->get('city')" />
                    </div>
                </div>

                {{-- Province & Zip --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <x-input-label for="province" :value="__('Province')" class="fw-semibold" />
                        <x-text-input id="province"
                                      type="text"
                                      name="province"
                                      :value="old('province')"
                                      :class="$errors->get('province') ? 'is-invalid' : ''" />
                        <x-input-error :messages="$errors->get('province')" />
                    </div>
                    <div class="col-md-4">
                        <x-input-label for="zip_code" :value="__('Zip Code')" class="fw-semibold" />
                        <x-text-input id="zip_code"
                                      type="text"
                                      name="zip_code"
                                      :value="old('zip_code')"
                                      :class="$errors->get('zip_code') ? 'is-invalid' : ''" />
                        <x-input-error :messages="$errors->get('zip_code')" />
                    </div>
                </div>

                <hr class="my-4">
                <p class="fw-semibold text-muted mb-3">Account Credentials</p>

                {{-- Email --}}
                <div class="mb-3">
                    <x-input-label for="email" :value="__('Email Address')" class="fw-semibold" />
                    <x-text-input id="email"
                                  type="email"
                                  name="email"
                                  :value="old('email')"
                                  :class="$errors->get('email') ? 'is-invalid' : ''"
                                  required
                                  autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <x-input-label for="password" :value="__('Password')" class="fw-semibold" />
                    <x-text-input id="password"
                                  type="password"
                                  name="password"
                                  :class="$errors->get('password') ? 'is-invalid' : ''"
                                  required
                                  autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="fw-semibold" />
                    <x-text-input id="password_confirmation"
                                  type="password"
                                  name="password_confirmation"
                                  :class="$errors->get('password_confirmation') ? 'is-invalid' : ''"
                                  required
                                  autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" />
                </div>

                {{-- Submit --}}
                <div class="d-grid">
                    <x-primary-button class="btn-lg py-3 justify-content-center">
                        {{ __('Create Account') }}
                    </x-primary-button>
                </div>

            </form>
        </div>

        {{-- Card Footer --}}
        <div class="card-footer bg-light py-3 text-center border-0">
            <small class="text-muted">
                Already have an account?
                <a href="{{ route('login') }}" class="text-primary fw-medium text-decoration-none">Login here</a>
            </small>
        </div>

    </div>

</x-guest-layout>

{{-- <x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" :class="$errors->get('name') ? 'is-invalid' : ''" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <!-- Email Address -->
        <div class="mt-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" :class="$errors->get('email') ? 'is-invalid' : ''" required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="mt-3">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" type="password" name="password" :class="$errors->get('password') ? 'is-invalid' : ''" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-3">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" type="password" name="password_confirmation" :class="$errors->get('password_confirmation') ? 'is-invalid' : ''"
                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="text-end mt-3">
            <a class="text-secondary" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}
