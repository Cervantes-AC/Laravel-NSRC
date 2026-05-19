<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class FormValidationService
{
    public function validateRequired(array $data, array $fields): array
    {
        $rules = [];
        $messages = [];

        foreach ($fields as $field) {
            $rules[$field] = 'required';
            $messages["{$field}.required"] = "The {$field} field is required.";
        }

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        return [];
    }

    public function validateEmail(string $email): bool
    {
        $validator = Validator::make(
            ['email' => $email],
            ['email' => 'required|email']
        );

        return ! $validator->fails();
    }

    public function validatePhone(string $phone): bool
    {
        $validator = Validator::make(
            ['phone' => $phone],
            ['phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15']
        );

        return ! $validator->fails();
    }

    public function validateDateRange($start, $end): bool
    {
        if (! $start || ! $end) {
            return false;
        }

        $startDate = $start instanceof \DateTimeInterface ? $start : now()->parse($start);
        $endDate = $end instanceof \DateTimeInterface ? $end : now()->parse($end);

        return $endDate->greaterThanOrEqualTo($startDate);
    }

    public function getValidationRules(string $context): array
    {
        return match ($context) {
            'user_registration' => [
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'full_name' => 'required|string|max:255',
                'role' => 'required|string|in:admin,volunteer',
            ],
            'user_profile' => [
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'personal_contact_number' => 'nullable|string|max:20',
                'current_address' => 'nullable|string|max:500',
            ],
            'duty_log' => [
                'full_name' => 'required|string|max:255',
                'attendance' => 'required|string|in:Time In,Time Out',
                'date_time' => 'required|date',
                'location' => 'required|string|max:255',
            ],
            'import' => [
                'file' => 'required|file|mimes:csv,xlsx,xls,txt|max:5120',
            ],
            'settings' => [
                'key' => 'required|string|max:255',
                'value' => 'required|string',
            ],
            default => [],
        };
    }
}
