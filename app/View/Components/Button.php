<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public $type, $buttons, $class, $id, $btnClass;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $buttons, string $type, ?string $class = null, string $id = '', ?string $btnClass = 'btn-primary')
    {
        $this->buttons = $buttons ?? '';
        $this->type = $type;
        $this->class = $class;
        $this->id = $id;
        $this->btnClass = $btnClass;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
