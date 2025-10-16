<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class BaseService
{
    /**
     * Validate if a value is unique in the database for a given model and column.
     *
     * @param string $modelClass The fully qualified model class name
     * @param string $column The column to check for uniqueness
     * @param mixed $value The value to validate
     * @param int|null $excludeId Optional ID to exclude from the check (for updates)
     * @return bool True if unique, false otherwise
     */
    protected function validateUnique(string $modelClass, string $column, mixed $value, ?int $excludeId = null): bool
    {
        $query = $modelClass::where($column, $value);
        
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }

    /**
     * Generate a unique slug from a given string.
     *
     * @param string $modelClass The fully qualified model class name
     * @param string $text The text to convert to a slug
     * @param string $column The column name where slug is stored (default: 'slug')
     * @param int|null $excludeId Optional ID to exclude from uniqueness check
     * @return string The unique slug
     */
    protected function generateSlug(string $modelClass, string $text, string $column = 'slug', ?int $excludeId = null): string
    {
        $slug = Str::slug($text);
        $originalSlug = $slug;
        $counter = 1;

        while (!$this->validateUnique($modelClass, $column, $slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Upload a file to storage and return the path.
     *
     * @param UploadedFile $file The uploaded file
     * @param string $directory The directory to store the file in
     * @param string $disk The storage disk to use (default: 'public')
     * @param bool $preserveOriginalName Whether to preserve the original filename (default: false)
     * @return string The stored file path
     */
    protected function uploadFile(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        bool $preserveOriginalName = false
    ): string {
        if ($preserveOriginalName) {
            $filename = $file->getClientOriginalName();
        } else {
            $filename = $this->generateUniqueFilename($file);
        }

        $path = $file->storeAs($directory, $filename, $disk);

        return $path;
    }

    /**
     * Generate a unique filename for an uploaded file.
     *
     * @param UploadedFile $file The uploaded file
     * @return string The unique filename
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;

        return $filename;
    }

    /**
     * Delete a file from storage.
     *
     * @param string $path The file path to delete
     * @param string $disk The storage disk (default: 'public')
     * @return bool True if deleted successfully
     */
    protected function deleteFile(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Get the full URL for a stored file.
     *
     * @param string $path The file path
     * @param string $disk The storage disk (default: 'public')
     * @return string The full URL
     */
    protected function getFileUrl(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }
}
