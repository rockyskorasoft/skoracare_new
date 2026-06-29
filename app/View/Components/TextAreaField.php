<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextAreaField extends Component
{
    public $id, $name, $label, $value, $rows, $labelClass, $class;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $id,
        string $name,
        ?string $class = null,
        ?string $labelClass = null,
        string $label = '',
        ?string $value = '',
        $rows = 4
    ) {
        $this->class = !empty($class) ? $class : 'col-md-6';
        $this->id = $id;
        $this->labelClass = $labelClass;
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->rows = $rows;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.text-area-field');
    }
}
