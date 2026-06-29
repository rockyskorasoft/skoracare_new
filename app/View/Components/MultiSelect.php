<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MultiSelect extends Component
{
    public $id, $name, $label, $placeholder, $options, $class, $value, $selected, $divClass, $labelClass;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $id = null,
        $name = 'name',
        $label = 'Select',
        $placeholder = 'select',
        $options = [],
        $class = '',
        ?string $divClass = '',
        $value = [],
        $selected = null,
        $labelClass = ''
    )
    {
        $this->options = $options;
        $this->label = $label;
        $this->name = $name;
        $this->id = $id ?: $name;
        $this->class = $class;
        $this->divClass = $divClass;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->selected = $selected;
        $this->labelClass = $labelClass;
    }

    public function render(): View|Closure|string
    {
        return view('components.multi-select');
    }
}
