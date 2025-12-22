<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Contact extends Component
{
    public $source;
    public $city;
    public $listingKey;

    /**
     * Create a new component instance.
     */
    public function __construct($source = 'unknown', $city = null, $listingKey = null)
    {
        $this->source = $source;
        $this->city = $city;
        $this->listingKey = $listingKey;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.contact');
    }
}
