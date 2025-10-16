<?php

namespace App\Livewire\Examples;

use Livewire\Component;
use App\Traits\WithToastr;

class ToastrExample extends Component
{
    use WithToastr;

    public function showSuccess()
    {
        $this->toastSuccess('Operation completed successfully!', 'Success');
    }

    public function showError()
    {
        $this->toastError('Something went wrong. Please try again.', 'Error');
    }

    public function showWarning()
    {
        $this->toastWarning('Please review your input before proceeding.', 'Warning');
    }

    public function showInfo()
    {
        $this->toastInfo('Here is some useful information for you.', 'Info');
    }

    public function showCustom()
    {
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'This is a custom toast with specific options!',
            'title' => 'Custom Toast'
        ]);
    }

    public function showMultiple()
    {
        $this->toastInfo('First notification');
        $this->toastSuccess('Second notification');
        $this->toastWarning('Third notification');
    }

    public function render()
    {
        return view('livewire.examples.toastr-example');
    }
}
