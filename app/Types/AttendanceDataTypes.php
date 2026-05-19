<?php

namespace App\Types;

/**
 * Attendance Data Type Definitions
 *
 * This file defines all type structures for import/export operations
 * to ensure data consistency between import and export services.
 */

/**
 * Raw attendance record from database or import source
 *
 * @typedef {object} AttendanceRecord
 *
 * @property {string} full_name - Volunteer's full name (required)
 * @property {string} attendance - Attendance type/status (required)
 * @property {string|null} date_time - Date and time of attendance (nullable)
 * @property {string|null} location - Location of attendance (nullable)
 * @property {string|null} shift_type - Type of shift (nullable)
 * @property {string|null} source_signature - Unique signature for duplicate detection (nullable)
 * @property {array|null} source_payload - Additional metadata as JSON (nullable)
 */
class AttendanceRecord
{
    public function __construct(
        public readonly string $full_name,
        public readonly string $attendance,
        public readonly ?string $date_time = null,
        public readonly ?string $location = null,
        public readonly ?string $shift_type = null,
        public readonly ?string $source_signature = null,
        public readonly ?array $source_payload = null,
    ) {}

    /**
     * Convert to array for export
     */
    public function toArray(): array
    {
        return [
            'full_name' => $this->full_name,
            'attendance' => $this->attendance,
            'date_time' => $this->date_time,
            'location' => $this->location,
            'shift_type' => $this->shift_type,
            'source_signature' => $this->source_signature,
            'source_payload' => $this->source_payload ? json_encode($this->source_payload) : null,
        ];
    }

    /**
     * Create from array (import)
     */
    public static function fromArray(array $data): self
    {
        $sourcePayload = $data['source_payload'] ?? null;

        // Handle JSON string conversion
        if (is_string($sourcePayload) && ! empty($sourcePayload)) {
            try {
                $sourcePayload = json_decode($sourcePayload, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $sourcePayload = null;
            }
        }

        return new self(
            full_name: $data['full_name'] ?? '',
            attendance: $data['attendance'] ?? '',
            date_time: $data['date_time'] ?? $data['timestamp'] ?? null,
            location: $data['location'] ?? null,
            shift_type: $data['shift_type'] ?? null,
            source_signature: $data['source_signature'] ?? null,
            source_payload: $sourcePayload,
        );
    }
}

/**
 * Normalized import row structure
 *
 * @typedef {object} NormalizedImportRow
 *
 * @property {string} full_name - Trimmed volunteer name
 * @property {string} attendance - Trimmed attendance value
 * @property {mixed} date_time - Parsed datetime
 * @property {string|null} location - Trimmed location
 * @property {string|null} shift_type - Trimmed shift type
 * @property {string|null} source_signature - Trimmed source signature
 * @property {string|null} source_payload - JSON string or null
 */
class NormalizedImportRow
{
    public function __construct(
        public readonly string $full_name,
        public readonly string $attendance,
        public readonly mixed $date_time,
        public readonly ?string $location = null,
        public readonly ?string $shift_type = null,
        public readonly ?string $source_signature = null,
        public readonly ?string $source_payload = null,
    ) {}

    /**
     * Convert to array for database insertion
     */
    public function toArray(): array
    {
        return [
            'full_name' => $this->full_name,
            'attendance' => $this->attendance,
            'date_time' => $this->date_time,
            'location' => $this->location,
            'shift_type' => $this->shift_type,
            'source_signature' => $this->source_signature,
            'source_payload' => $this->source_payload ? json_decode($this->source_payload, true) : null,
        ];
    }
}

/**
 * Export data structure
 *
 * @typedef {object} ExportData
 *
 * @property {string} full_name - Volunteer's full name
 * @property {string} attendance - Attendance type/status
 * @property {string|null} date_time - Date and time
 * @property {string|null} location - Location
 * @property {string|null} shift_type - Shift type
 * @property {string|null} source_signature - Source signature
 * @property {string|null} source_payload - JSON payload as string
 */
class ExportData
{
    public function __construct(
        public readonly string $full_name,
        public readonly string $attendance,
        public readonly ?string $date_time = null,
        public readonly ?string $location = null,
        public readonly ?string $shift_type = null,
        public readonly ?string $source_signature = null,
        public readonly ?string $source_payload = null,
    ) {}

    /**
     * Convert to array for CSV/Excel export
     */
    public function toArray(): array
    {
        return [
            'full_name' => $this->full_name,
            'attendance' => $this->attendance,
            'date_time' => $this->date_time,
            'location' => $this->location,
            'shift_type' => $this->shift_type,
            'source_signature' => $this->source_signature,
            'source_payload' => $this->source_payload,
        ];
    }
}

/**
 * Import validation result
 *
 * @typedef {object} ImportValidationResult
 *
 * @property {bool} valid - Whether validation passed
 * @property {array} errors - List of validation errors
 * @property {array} warnings - List of warnings
 * @property {string} filename - Original filename
 * @property {int} size - File size in bytes
 * @property {string} extension - File extension
 */
class ImportValidationResult
{
    public function __construct(
        public readonly bool $valid,
        public readonly array $errors = [],
        public readonly array $warnings = [],
        public readonly string $filename = '',
        public readonly int $size = 0,
        public readonly string $extension = '',
    ) {}
}

/**
 * Import preview result
 *
 * @typedef {object} ImportPreviewResult
 *
 * @property {array} preview - First 10 rows of preview data
 * @property {array} errors - Validation errors
 * @property {int} total_rows - Total rows in file
 */
class ImportPreviewResult
{
    public function __construct(
        public readonly array $preview = [],
        public readonly array $errors = [],
        public readonly int $total_rows = 0,
    ) {}
}

/**
 * Import processing result
 *
 * @typedef {object} ImportProcessResult
 *
 * @property {int} success - Number of successfully imported records
 * @property {int} failed - Number of failed records
 * @property {int} skipped - Number of skipped records (duplicates)
 * @property {array} errors - List of errors
 * @property {array} log - Detailed import log
 */
class ImportProcessResult
{
    public function __construct(
        public readonly int $success = 0,
        public readonly int $failed = 0,
        public readonly int $skipped = 0,
        public readonly array $errors = [],
        public readonly array $log = [],
    ) {}
}

/**
 * Duplicate detection result
 *
 * @typedef {object} DuplicateDetectionResult
 *
 * @property {array} duplicates - List of duplicate records found
 * @property {int} count - Total duplicates found
 */
class DuplicateDetectionResult
{
    public function __construct(
        public readonly array $duplicates = [],
        public readonly int $count = 0,
    ) {}
}

/**
 * Import log entry
 *
 * @typedef {object} ImportLogEntry
 *
 * @property {int} row - Row number
 * @property {string} status - Status (success, failed, skipped)
 * @property {string} message - Status message
 */
class ImportLogEntry
{
    public function __construct(
        public readonly int $row,
        public readonly string $status,
        public readonly string $message,
    ) {}
}

/**
 * Export format options
 */
enum ExportFormat: string
{
    case CSV = 'csv';
    case EXCEL = 'xlsx';
    case PDF = 'pdf';
    case JSON = 'json';
}

/**
 * Import file format options
 */
enum ImportFileFormat: string
{
    case CSV = 'csv';
    case EXCEL = 'xlsx';
    case TEXT = 'txt';
}

/**
 * Attendance status types
 */
enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case LATE = 'late';
    case EARLY_LEAVE = 'early_leave';
    case HALF_DAY = 'half_day';
    case UNKNOWN = 'unknown';
}

/**
 * Shift types
 */
enum ShiftType: string
{
    case MORNING = 'morning';
    case AFTERNOON = 'afternoon';
    case EVENING = 'evening';
    case NIGHT = 'night';
    case FULL_DAY = 'full_day';
    case FLEXIBLE = 'flexible';
}

/**
 * Import/Export field mapping
 *
 * Defines the required and optional fields for import/export operations
 */
class FieldMapping
{
    /**
     * Required fields for import/export
     */
    public const REQUIRED_FIELDS = [
        'full_name',
        'attendance',
    ];

    /**
     * Optional fields for import/export
     */
    public const OPTIONAL_FIELDS = [
        'date_time',
        'location',
        'shift_type',
        'source_signature',
        'source_payload',
    ];

    /**
     * All valid fields
     */
    public const ALL_FIELDS = [
        'full_name',
        'attendance',
        'date_time',
        'location',
        'shift_type',
        'source_signature',
        'source_payload',
    ];

    /**
     * Field aliases for import (alternative column names)
     */
    public const FIELD_ALIASES = [
        'date_time' => ['timestamp', 'datetime', 'date'],
        'full_name' => ['name', 'volunteer_name', 'person_name'],
        'attendance' => ['status', 'attendance_type', 'type'],
        'shift_type' => ['shift', 'shift_name'],
        'source_signature' => ['signature', 'source_id', 'unique_id'],
        'source_payload' => ['payload', 'metadata', 'extra_data'],
    ];

    /**
     * Get all valid column names for a field
     */
    public static function getValidNames(string $field): array
    {
        $names = [$field];
        if (isset(self::FIELD_ALIASES[$field])) {
            $names = array_merge($names, self::FIELD_ALIASES[$field]);
        }

        return $names;
    }

    /**
     * Normalize field name to standard name
     */
    public static function normalizeFieldName(string $name): ?string
    {
        $name = strtolower(trim($name));

        foreach (self::FIELD_ALIASES as $standard => $aliases) {
            if ($name === $standard || in_array($name, $aliases)) {
                return $standard;
            }
        }

        return null;
    }
}

/**
 * Data validation rules
 */
class ValidationRules
{
    /**
     * Get validation rules for import
     */
    public static function getImportRules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'attendance' => 'required|string|max:100',
            'date_time' => 'nullable|date_format:Y-m-d H:i:s|date_format:Y-m-d',
            'location' => 'nullable|string|max:255',
            'shift_type' => 'nullable|string|max:100',
            'source_signature' => 'nullable|string|max:255|unique:attendance,source_signature',
            'source_payload' => 'nullable|string|json',
        ];
    }

    /**
     * Get validation rules for export
     */
    public static function getExportRules(): array
    {
        return [
            'full_name' => 'required|string',
            'attendance' => 'required|string',
            'date_time' => 'nullable|date',
            'location' => 'nullable|string',
            'shift_type' => 'nullable|string',
            'source_signature' => 'nullable|string',
            'source_payload' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages
     */
    public static function getErrorMessages(): array
    {
        return [
            'full_name.required' => 'Volunteer name is required',
            'full_name.max' => 'Volunteer name cannot exceed 255 characters',
            'attendance.required' => 'Attendance status is required',
            'date_time.date_format' => 'Date/time must be in format: YYYY-MM-DD or YYYY-MM-DD HH:MM:SS',
            'source_signature.unique' => 'This record already exists (duplicate source signature)',
            'source_payload.json' => 'Source payload must be valid JSON',
        ];
    }
}
