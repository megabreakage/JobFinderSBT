<?php

namespace App\Traits;

trait WithToastr
{
    /**
     * Display a success toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return void
     */
    protected function toastSuccess(string $message, ?string $title = 'Success'): void
    {
        $this->dispatchToast('success', $message, $title);
    }

    /**
     * Display an error toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return void
     */
    protected function toastError(string $message, ?string $title = 'Error'): void
    {
        $this->dispatchToast('error', $message, $title);
    }

    /**
     * Display a warning toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return void
     */
    protected function toastWarning(string $message, ?string $title = 'Warning'): void
    {
        $this->dispatchToast('warning', $message, $title);
    }

    /**
     * Display an info toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return void
     */
    protected function toastInfo(string $message, ?string $title = 'Info'): void
    {
        $this->dispatchToast('info', $message, $title);
    }

    /**
     * Dispatch toast notification
     *
     * @param string $type
     * @param string $message
     * @param string|null $title
     * @return void
     */
    protected function dispatchToast(string $type, string $message, ?string $title = null): void
    {
        // For Livewire components
        if (method_exists($this, 'dispatch')) {
            $this->dispatch('toast', [
                'type' => $type,
                'message' => $message,
                'title' => $title
            ]);
        }
        // For regular controllers
        else {
            session()->flash('toast', [
                'type' => $type,
                'message' => $message,
                'title' => $title
            ]);
        }
    }

    /**
     * Flash a success message to session
     *
     * @param string $message
     * @return void
     */
    protected function flashSuccess(string $message): void
    {
        session()->flash('success', $message);
    }

    /**
     * Flash an error message to session
     *
     * @param string $message
     * @return void
     */
    protected function flashError(string $message): void
    {
        session()->flash('error', $message);
    }

    /**
     * Flash a warning message to session
     *
     * @param string $message
     * @return void
     */
    protected function flashWarning(string $message): void
    {
        session()->flash('warning', $message);
    }

    /**
     * Flash an info message to session
     *
     * @param string $message
     * @return void
     */
    protected function flashInfo(string $message): void
    {
        session()->flash('info', $message);
    }
}
