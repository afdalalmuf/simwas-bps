<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SpjVerificationField extends Component
{

    public $documentType;
    public $verification;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($documentType, $verification = null)
    {
        $this->documentType = $documentType;
        $this->verification = $verification;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.spj-verification-field');
    }
}
