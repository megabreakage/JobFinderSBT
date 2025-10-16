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
        // PHPFlasher allows custom options
        flash()->success('This is a custom toast with specific options!', 'Custom Toast')
            ->options([
                'timeOut' => 10000,
                'progressBar' => true,
            ]);
    }

    public function showMultiple()
    {
        $this->toastInfo('First notification');
        $this->toastSuccess('Second notification');
        $this->toastWarning('Third notification');
    }

    public function showPersistent()
    {
        // This notification persists across Livewire updates
        $this->toastSuccess('This notification persists across Livewire updates!', 'Persistent');
    }

    public function showImmediate()
    {
        // Show notification immediately (for current request only)
        $this->toastNow('success', 'This shows immediately!', 'Immediate');
    }

    public function showWithDelay()
    {
        // Show notification with custom delay
        flash()->success('This notification has a custom delay!', 'Delayed')
            ->delay(2000);
    }

    public function showWithPriority()
    {
        // Show notification with priority
        flash()->success('High priority notification!', 'Priority')
            ->priority(10);
    }

    public function render()
    {
        return view('livewire.examples.toastr-example');
    }
}
