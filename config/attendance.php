<?php

return [
    'mysql' => [
        'table' => env('MYSQL_ATTENDANCE_TABLE', 'attendance_source'),
        'name_column' => env('MYSQL_ATTENDANCE_NAME_COLUMN', 'full_name'),
        'attendance_column' => env('MYSQL_ATTENDANCE_COLUMN', 'attendance'),
        'date_column' => env('MYSQL_ATTENDANCE_DATE_COLUMN', 'date_time'),
        'location_column' => env('MYSQL_ATTENDANCE_LOCATION_COLUMN', 'location'),
        'shift_column' => env('MYSQL_ATTENDANCE_SHIFT_COLUMN', 'shift_type'),
    ],
];
