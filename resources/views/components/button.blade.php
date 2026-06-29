<button type="{{ $type }}" id="{{ $id }}"  {{ $attributes->merge(['class' => $class ?: 'btn btn-secondary mt-2 mt-sm-0']) }}>
    {{ $buttons }}
</button>