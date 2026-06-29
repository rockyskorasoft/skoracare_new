<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputField extends Component
{
    public $label, $name, $type, $id, $value, $placeholder, $class, $isToggle, $toggleClass, $labelClass, $textAmount, $errorField;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $label = null,
        string $name,
        string $type = 'text',
        ?string $id = null,
        ?string $value = null,
        ?string $placeholder = null,
        ?string $class = null,
        bool $isToggle = false,
        ?string $toggleClass = null,
        ?string $labelClass = null,
        ?string $errorField = null,
        ?string $textAmount = null
    ) {
        $this->class = !empty($class) ? $class : 'col-md-6';
        $this->label = $label;
        $this->name = $name;
        $this->type = $type;
        $this->id = $id ?? $name;
        $this->value = $value ?? old($name);
        $this->placeholder = $placeholder ?? $name;
        $this->isToggle = $isToggle;
        $this->toggleClass = $toggleClass;
        $this->labelClass = $labelClass;
        $this->errorField = !empty($errorField) ? $errorField : $name;
        $this->textAmount = $textAmount;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input-field');
    }
}
