<div class="{{ $class }}">
    <label for="{{ $id }}" class="form-label {{ $labelClass ?? '' }}">{{ $label }}
        <strong class="{{ $textAmount ?? '' }}"></strong></label>
    <div class="{{ $isToggle ? 'position-relative password-toggle-group' : '' }}">
        <input type="{{ $type }}" class="form-control {{ $class ?? '' }} @error($errorField) is-invalid @enderror"
            name="{{ $name }}" id="{{ $id }}" value="{{ old($name, $value ?? '') }}" placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'form-control']) }} />

        @if ($isToggle)
            <button type="button"
                class="btn btn-sm position-absolute shadow-none h-fit-content m-auto end-0 toggle-password-btn me-2 border-0 {{ $toggleClass ?? 'bg-primary text-white' }}"
                onclick="togglePassword('{{ $id }}', this)">
                <i class="bi bi-eye-fill"></i>
            </button>
        @endif

        @error($errorField)
            <span class="invalid-feedback" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>
</div>