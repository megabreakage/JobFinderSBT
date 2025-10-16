<?php

if (!function_exists('toast')) {
    /**
     * Display a toast notification
     *
     * @param string $type
     * @param string $message
     * @param string|null $title
     * @return void
     */
    function toast(string $type, string $message, ?string $title = null): void
    {
        session()->flash('toast', [
            'type' => $type,
            'message' => $message,
            'title' => $title
        ]);
    }
}

if (!function_exists('toast_success')) {
    /**
     * Display a success toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return void
     */
    function toast_success(string $message, ?string $title = 'Success'): void
    {
        toast('success', $message, $title);
    }
}

if (!function_exists('toast_error')) {
    /**
     * Display an error toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return void
     */
    function toast_error(string $message, ?string $title = 'Error'): void
    {
        toast('error', $message, $title);
    }
}

if (!function_exists('toast_warning')) {
    /**
     * Display a warning toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return void
     */
    function toast_warning(string $message, ?string $title = 'Warning'): void
    {
        toast('warning', $message, $title);
    }
}

if (!function_exists('toast_info')) {
    /**
     * Display an info toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return void
     */
    function toast_info(string $message, ?string $title = 'Info'): void
    {
        toast('info', $message, $title);
    }
}
