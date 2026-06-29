@php
    $selectedValues = old($name);
    if ($selectedValues === null) {
        $selectedValues = $selected ?? ($value ?? []);
    }

    $selectedValues = is_array($selectedValues) ? $selectedValues : (array) $selectedValues;
    $selectedValues = array_map('strval', $selectedValues);
@endphp

<div class="col-6 col-xxl-6 {{ $divClass ?? '' }}">
    <label for="{{ $id }}" class="form-label {{ $labelClass ?? 'required' }}">
        {{ $label }}
    </label>

    <select id="{{ $id }}" name="{{ $name }}[]"
        class="form-control {{ $class }} @error($name) is-invalid @enderror"
        data-placeholder="{{ $placeholder ?? __('labels.select') }}" {{ $attributes }} multiple>
        @foreach ($options as $key => $option)
            @php
                $optionValue = $key;
                $optionLabel = $option;

                if (is_object($option)) {
                    $optionValue = $option->id ?? $key;
                    $optionLabel =
                        $option->full_name ??
                        ($option->name ??
                            trim(
                                ($option->first_name ?? '') .
                                    ' ' .
                                    ($option->middle_name ?? '') .
                                    ' ' .
                                    ($option->last_name ?? ''),
                            ));
                } elseif (is_array($option)) {
                    $optionValue = $option['id'] ?? $key;
                    $optionLabel =
                        $option['label'] ??
                        ($option['name'] ??
                            trim(
                                ($option['first_name'] ?? '') .
                                    ' ' .
                                    ($option['middle_name'] ?? '') .
                                    ' ' .
                                    ($option['last_name'] ?? ''),
                            ));
                } elseif (is_numeric($key)) {
                    $optionValue = $option;
                }

                if (is_string($optionLabel)) {
                    $optionLabel = trim($optionLabel);
                }

                if ($optionLabel === '' || $optionLabel === null) {
                    $optionLabel = 'Option #' . $optionValue;
                }

                $optionValue = (string) $optionValue;
            @endphp

            <option value="{{ $optionValue }}" {{ in_array($optionValue, $selectedValues, true) ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
