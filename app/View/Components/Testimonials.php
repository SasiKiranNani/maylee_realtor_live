<?php

namespace App\View\Components;

use App\Models\Testimonial;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Closure;

class Testimonials extends Component
{
    public $testimonials;

    public function __construct()
    {
        $this->testimonials = Testimonial::where('status', true)->get();
    }

    public function render(): View|Closure|string
    {
        return view('components.testimonials', [
            'testimonials' => $this->testimonials,
        ]);
    }
}
