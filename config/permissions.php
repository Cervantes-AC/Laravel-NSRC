<?php

return [
    'admin' => [
        'dashboard' => ['view'],
        'personnel' => ['view', 'create', 'edit', 'delete'],
        'announcements' => ['view', 'create', 'edit', 'delete', 'publish'],
        'sessions' => ['view', 'create', 'edit', 'delete', 'restore'],
        'accounts' => ['view', 'approve', 'reject', 'suspend', 'delete'],
        'reports' => ['view', 'generate', 'export'],
        'settings' => ['view', 'update'],
        'audit_logs' => ['view', 'export'],
        'import' => ['view', 'process'],
        'backup' => ['view', 'run', 'download'],
        'analytics' => ['view'],
        'ai' => ['supertool', 'agent', 'teammate'],
        'notifications' => ['view', 'manage'],
    ],
    'member' => [
        'dashboard' => ['view'],
        'attendance' => ['view'],
        'performance' => ['view'],
        'ai' => ['agent'],
        'notifications' => ['view'],
        'announcements' => ['view'],
        'settings' => ['view', 'update'],
    ],
];
