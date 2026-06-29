<div class="{{ $divClass ?? '' }}">
    <label for="{{ $id }}" class="form-label {{ $labelClass }}">{{ $label }}</label>
    <select id="{{ $id }}" name="{{ $name }}"
        {{ $attributes->merge(['class' => 'form-select ' . ($class ?? '') . ($errors->has($errorField ?? $name) ? ' is-invalid' : '')]) }}>
        <option value="" disabled {{ old($name, $value) === null || old($name, $value) === '' ? 'selected' : '' }}>
            {{ $placeholder ?? __('labels.select') }}
        </option>
        @foreach ($options as $option)
            <option value="{{ $option['id'] }}" {{ old($name, $value) == $option['id'] ? 'selected' : '' }}>
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
    @error($errorField ?? $name)
        <span class="invalid-feedback" role="alert">{{ $message }}</span>
    @enderror
</div>