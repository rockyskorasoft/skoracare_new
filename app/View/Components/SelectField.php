<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectField extends Component
{
    public $id, $name, $label, $placeholder, $options, $class, $value, $divClass, $labelClass, $keyName = 'name', $errorField;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $id,
        string $name,
        ?string $label,
        ?string $placeholder,
        mixed $options = [],
        ?string $class = null,
        ?string $value = null,
        ?string $divClass = '',
        ?string $keyName = 'name',
        ?string $labelClass = null,
        ?string $errorField = null,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->options = $this->formatOptions($options, $keyName);
        $this->class = $class;
        $this->value = $value;
        $this->divClass = !empty($divClass) ? $divClass : 'col-md-6 col-xxl-6';
        $this->labelClass = $labelClass;
        $this->errorField = !empty($errorField) ? $errorField : $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-field');
    }

    /**
     * function for formatting the options
     *
     * @params mixed $options
     */
    private function formatOptions(mixed $options, ?string $keyName = 'name')
    {
        $formattedOptions = [];
        if (is_object($options)) {
            foreach ($options as $option) {
                $formatOption = [];
                if (isset($option->id)) {
                    $formatOption['id'] = $option->id;
                }
                if (isset($option->$keyName)) {
                    $formatOption['label'] = $option->$keyName;
                }
                $formattedOptions[] = $formatOption;
            }
        } else {
            $formattedOptions = $options;
        }
        return $formattedOptions;
    }
}
