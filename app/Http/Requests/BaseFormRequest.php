<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Override this method in child classes for specific authorization logic.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Must be implemented by child classes.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    abstract public function rules(): array;

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'required_if' => 'The :attribute field is required when :other is :value.',
            'required_unless' => 'The :attribute field is required unless :other is in :values.',
            'required_with' => 'The :attribute field is required when :values is present.',
            'required_with_all' => 'The :attribute field is required when :values are present.',
            'required_without' => 'The :attribute field is required when :values is not present.',
            'required_without_all' => 'The :attribute field is required when none of :values are present.',
            'string' => 'The :attribute must be a string.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
            'exists' => 'The selected :attribute is invalid.',
            'min' => 'The :attribute must be at least :min characters.',
            'max' => 'The :attribute must not exceed :max characters.',
            'min.numeric' => 'The :attribute must be at least :min.',
            'max.numeric' => 'The :attribute must not exceed :max.',
            'min.array' => 'The :attribute must have at least :min items.',
            'max.array' => 'The :attribute must not have more than :max items.',
            'between' => 'The :attribute must be between :min and :max.',
            'between.numeric' => 'The :attribute must be between :min and :max.',
            'in' => 'The selected :attribute is invalid.',
            'not_in' => 'The selected :attribute is invalid.',
            'numeric' => 'The :attribute must be a number.',
            'integer' => 'The :attribute must be an integer.',
            'digits' => 'The :attribute must be :digits digits.',
            'digits_between' => 'The :attribute must be between :min and :max digits.',
            'size' => 'The :attribute must be :size.',
            'size.numeric' => 'The :attribute must be :size.',
            'size.file' => 'The :attribute must be :size kilobytes.',
            'size.string' => 'The :attribute must be :size characters.',
            'size.array' => 'The :attribute must contain :size items.',
            'date' => 'The :attribute is not a valid date.',
            'date_format' => 'The :attribute does not match the format :format.',
            'before' => 'The :attribute must be a date before :date.',
            'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
            'after' => 'The :attribute must be a date after :date.',
            'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
            'boolean' => 'The :attribute field must be true or false.',
            'confirmed' => 'The :attribute confirmation does not match.',
            'same' => 'The :attribute and :other must match.',
            'different' => 'The :attribute and :other must be different.',
            'url' => 'The :attribute must be a valid URL.',
            'regex' => 'The :attribute format is invalid.',
            'alpha' => 'The :attribute must only contain letters.',
            'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.',
            'alpha_num' => 'The :attribute must only contain letters and numbers.',
            'array' => 'The :attribute must be an array.',
            'json' => 'The :attribute must be a valid JSON string.',
            'file' => 'The :attribute must be a file.',
            'image' => 'The :attribute must be an image.',
            'mimes' => 'The :attribute must be a file of type: :values.',
            'mimetypes' => 'The :attribute must be a file of type: :values.',
            'uploaded' => 'The :attribute failed to upload.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => 'email address',
            'phone' => 'phone number',
            'first_name' => 'first name',
            'last_name' => 'last name',
            'job_title' => 'job title',
            'company_id' => 'company',
            'industry_id' => 'industry',
            'user_id' => 'user',
            'job_type' => 'job type',
            'experience_level' => 'experience level',
            'location_type' => 'location type',
            'salary_min' => 'minimum salary',
            'salary_max' => 'maximum salary',
            'salary_currency' => 'salary currency',
            'is_active' => 'active status',
            'is_verified' => 'verification status',
            'is_featured' => 'featured status',
        ];
    }

    /**
     * Handle a failed validation attempt.
     * For API requests, return JSON response instead of redirecting.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Common validation rules for email fields.
     *
     * @param bool $required
     * @param string|null $table
     * @param int|null $ignoreId
     * @return array
     */
    protected function emailRules(bool $required = true, ?string $table = null, ?int $ignoreId = null): array
    {
        $rules = $required ? ['required'] : ['nullable'];
        $rules[] = 'string';
        $rules[] = 'email';
        $rules[] = 'max:255';

        if ($table) {
            $uniqueRule = "unique:{$table},email";
            if ($ignoreId) {
                $uniqueRule .= ",{$ignoreId}";
            }
            $rules[] = $uniqueRule;
        }

        return $rules;
    }

    /**
     * Common validation rules for phone fields.
     *
     * @param bool $required
     * @param string|null $table
     * @param int|null $ignoreId
     * @return array
     */
    protected function phoneRules(bool $required = true, ?string $table = null, ?int $ignoreId = null): array
    {
        $rules = $required ? ['required'] : ['nullable'];
        $rules[] = 'string';
        $rules[] = 'regex:/^[+]?[0-9\s\-()]+$/';
        $rules[] = 'min:10';
        $rules[] = 'max:20';

        if ($table) {
            $uniqueRule = "unique:{$table},phone";
            if ($ignoreId) {
                $uniqueRule .= ",{$ignoreId}";
            }
            $rules[] = $uniqueRule;
        }

        return $rules;
    }

    /**
     * Common validation rules for password fields.
     *
     * @param bool $required
     * @param bool $confirmed
     * @return array
     */
    protected function passwordRules(bool $required = true, bool $confirmed = true): array
    {
        $rules = $required ? ['required'] : ['nullable'];
        $rules[] = 'string';
        $rules[] = 'min:8';
        $rules[] = 'max:255';
        $rules[] = 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'; // At least one lowercase, uppercase, and digit

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    /**
     * Common validation rules for slug fields.
     *
     * @param bool $required
     * @param string|null $table
     * @param int|null $ignoreId
     * @return array
     */
    protected function slugRules(bool $required = true, ?string $table = null, ?int $ignoreId = null): array
    {
        $rules = $required ? ['required'] : ['nullable'];
        $rules[] = 'string';
        $rules[] = 'max:255';
        $rules[] = 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'; // Lowercase letters, numbers, and hyphens

        if ($table) {
            $uniqueRule = "unique:{$table},slug";
            if ($ignoreId) {
                $uniqueRule .= ",{$ignoreId}";
            }
            $rules[] = $uniqueRule;
        }

        return $rules;
    }

    /**
     * Common validation rules for image upload fields.
     *
     * @param bool $required
     * @param int $maxSizeKb Maximum file size in kilobytes
     * @return array
     */
    protected function imageRules(bool $required = true, int $maxSizeKb = 5120): array
    {
        $rules = $required ? ['required'] : ['nullable'];
        $rules[] = 'image';
        $rules[] = 'mimes:jpeg,jpg,png,gif,webp';
        $rules[] = "max:{$maxSizeKb}";

        return $rules;
    }

    /**
     * Common validation rules for file upload fields.
     *
     * @param bool $required
     * @param array $mimes Allowed MIME types
     * @param int $maxSizeKb Maximum file size in kilobytes
     * @return array
     */
    protected function fileRules(bool $required = true, array $mimes = [], int $maxSizeKb = 10240): array
    {
        $rules = $required ? ['required'] : ['nullable'];
        $rules[] = 'file';

        if (!empty($mimes)) {
            $rules[] = 'mimes:' . implode(',', $mimes);
        }

        $rules[] = "max:{$maxSizeKb}";

        return $rules;
    }
}
