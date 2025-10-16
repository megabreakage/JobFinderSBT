<?php

if (!function_exists('toast_success')) {
    /**
     * Display a success toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return \Flasher\Prime\Notification\NotificationInterface
     */
    function toast_success(string $message, ?string $title = 'Success')
    {
        return flash()->success($message, $title);
    }
}

if (!function_exists('toast_error')) {
    /**
     * Display an error toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return \Flasher\Prime\Notification\NotificationInterface
     */
    function toast_error(string $message, ?string $title = 'Error')
    {
        return flash()->error($message, $title);
    }
}

if (!function_exists('toast_warning')) {
    /**
     * Display a warning toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return \Flasher\Prime\Notification\NotificationInterface
     */
    function toast_warning(string $message, ?string $title = 'Warning')
    {
        return flash()->warning($message, $title);
    }
}

if (!function_exists('toast_info')) {
    /**
     * Display an info toast notification
     *
     * @param string $message
     * @param string|null $title
     * @return \Flasher\Prime\Notification\NotificationInterface
     */
    function toast_info(string $message, ?string $title = 'Info')
    {
        return flash()->info($message, $title);
    }
}

if (!function_exists('toast_now')) {
    /**
     * Display a toast notification immediately (for Livewire)
     *
     * @param string $type
     * @param string $message
     * @param string|null $title
     * @return \Flasher\Prime\Notification\NotificationInterface
     */
    function toast_now(string $type, string $message, ?string $title = null)
    {
        return flash()->now($type, $message, $title);
    }
}
