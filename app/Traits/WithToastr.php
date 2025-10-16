<?php

namespace App\Traits;

use Flasher\Prime\FlasherInterface;

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
        flash()->success($message, $title);
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
        flash()->error($message, $title);
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
        flash()->warning($message, $title);
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
        flash()->info($message, $title);
    }

    /**
     * Display a toast notification with custom options
     *
     * @param string $type
     * @param string $message
     * @param string|null $title
     * @param array $options
     * @return void
     */
    protected function toast(string $type, string $message, ?string $title = null, array $options = []): void
    {
        $notification = flash($type, $message, $title);
        
        if (!empty($options)) {
            $notification->options($options);
        }
    }

    /**
     * Flash a success message to session (legacy support)
     *
     * @param string $message
     * @return void
     */
    protected function flashSuccess(string $message): void
    {
        flash()->success($message);
    }

    /**
     * Flash an error message to session (legacy support)
     *
     * @param string $message
     * @return void
     */
    protected function flashError(string $message): void
    {
        flash()->error($message);
    }

    /**
     * Flash a warning message to session (legacy support)
     *
     * @param string $message
     * @return void
     */
    protected function flashWarning(string $message): void
    {
        flash()->warning($message);
    }

    /**
     * Flash an info message to session (legacy support)
     *
     * @param string $message
     * @return void
     */
    protected function flashInfo(string $message): void
    {
        flash()->info($message);
    }

    /**
     * Display a notification that persists across Livewire updates
     *
     * @param string $type
     * @param string $message
     * @param string|null $title
     * @return void
     */
    protected function toastNow(string $type, string $message, ?string $title = null): void
    {
        flash()->now($type, $message, $title);
    }

    /**
     * Display a success notification immediately
     *
     * @param string $message
     * @param string|null $title
     * @return void
     */
    protected function toastSuccessNow(string $message, ?string $title = 'Success'): void
    {
        flash()->now('success', $message, $title);
    }

    /**
     * Display an error notification immediately
     *
     * @param string $message
     * @param string|null $title
     * @return void
     */
    protected function toastErrorNow(string $message, ?string $title = 'Error'): void
    {
        flash()->now('error', $message, $title);
    }
}
