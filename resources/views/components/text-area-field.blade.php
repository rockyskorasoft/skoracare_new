<div class="{{ $divClass ?? '' }}">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 {{ $labelClass ?? '' }}">{{ $label }}</label>
    @endif
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm form-control editor {{ $class ?? ''}} @error($name) is-invalid @enderror">{{ $value }}</textarea>
    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>