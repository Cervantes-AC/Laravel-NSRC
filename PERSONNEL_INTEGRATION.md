# Personnel Component Integration Guide

## Quick Start

### 1. Verify Database Structure

Ensure your database has the required tables and relationships:

```php
// User model should have:
- id (primary key)
- full_name
- email
- serial_number
- role
- avatar (optional)
- dutySessions() relationship

// DutySession model should have:
- id (primary key)
- full_name
- date
- time_in (datetime)
- time_out (datetime, nullable)
- duration_minutes
- status
- volunteer_id (foreign key to users)
- volunteer() relationship
```

### 2. Add Route

In your `routes/web.php`:

```php
use App\Livewire\Personnel;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/personnel', Personnel::class)->name('personnel.index');
});
```

### 3. Create Navigation Link

In your navigation template:

```blade
<a href="{{ route('personnel.index') }}" class="nav-link">
    Personnel
</a>
```

### 4. Verify Livewire is Installed

```bash
composer require livewire/livewire
```

Add to your layout:

```blade
@livewireStyles
<!-- ... -->
@livewireScripts
```

## Component Features Walkthrough

### Search Functionality

The search bar filters personnel by:
- Full name (case-insensitive)
- Serial number
- Email address

```blade
<input wire:model.debounce.300ms="search" type="text" placeholder="Search personnel...">
```

### Compliance Filtering

Three filter options:
- **All**: Shows all personnel
- **Clean**: Shows only personnel with no compliance issues
- **Issues**: Shows only personnel with detected issues

```blade
<button wire:click="$set('complianceFilter', 'all')">All</button>
<button wire:click="$set('complianceFilter', 'compliance_only')">Clean</button>
<button wire:click="$set('complianceFilter', 'issues_only')">Issues</button>
```

### Sorting

Click any sort button to toggle between ascending/descending:

```blade
<button wire:click="toggleSort('name')">Name</button>
<button wire:click="toggleSort('sessions')">Sessions</button>
<button wire:click="toggleSort('hours')">Hours</button>
<button wire:click="toggleSort('issues')">Issues</button>
```

### View Modes

Switch between list and grid views:

```blade
<button wire:click="$set('viewMode', 'list')">List View</button>
<button wire:click="$set('viewMode', 'grid')">Grid View</button>
```

### Pagination

Navigate through pages:

```blade
<button wire:click="previousPage">Previous</button>
<button wire:click="gotoPage({{ $page }})">{{ $page }}</button>
<button wire:click="nextPage">Next</button>
```

## Metrics Explanation

### Regular Hours
- Time logged up to 8 hours per day
- Formula: `MIN(daily_hours, 8h)`
- Example: 6 hours logged = 6 hours regular

### Overtime Hours
- Time logged beyond 8 hours per day
- Formula: `MAX(0, daily_hours - 8h)`
- Example: 10 hours logged = 8 hours regular + 2 hours overtime

### Undertime Hours
- Sessions with less than 1 hour duration
- Formula: `daily_hours < 1h ? daily_hours : 0`
- Example: 30 minutes logged = 30 minutes undertime

### Session Count
- Total number of duty sessions recorded
- Includes all sessions regardless of duration

### Invalid Record Count
- Number of sessions with compliance issues
- Issues include: missing timeout, zero duration, future dates

## Compliance Issues

### MISSING_TIMEOUT
**Condition**: No time_out recorded for a session
**Impact**: Session appears still open
**Resolution**: Record the time-out or mark session as complete

### ZERO_DURATION
**Condition**: time_out is not after time_in
**Impact**: Invalid session duration
**Resolution**: Correct the time-in or time-out values

### FUTURE_DATE
**Condition**: Session date is in the future
**Impact**: Invalid session date
**Resolution**: Correct the session date to today or earlier

## Customization

### Change Items Per Page

In `Personnel.php`:

```php
public int $perPage = 10; // Change this value
```

Or make it dynamic:

```php
public int $perPage = 10;

public function updatingPerPage(): void
{
    $this->resetPage();
}
```

### Add Custom Sorting

In the `render()` method, add to the match statement:

```php
match ($this->sortBy) {
    'name' => $cmp = strcmp($a['fullName'], $b['fullName']),
    'custom_field' => $cmp = $a['customField'] <=> $b['customField'],
    // ...
}
```

### Modify Issue Detection

In the `deriveIssues()` method:

```php
private function deriveIssues(DutySession $session): array
{
    $issues = [];
    
    // Add your custom issue detection logic here
    if (/* your condition */) {
        $issues[] = [
            'date' => $session->date->format('Y-m-d'),
            'type' => 'CUSTOM_ISSUE',
            'description' => 'Your description',
        ];
    }
    
    return $issues;
}
```

### Change Color Scheme

In the Blade template, modify the `$roleColors` array:

```php
$roleColors = [
    'admin' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-800', ...],
    // ...
];
```

## Performance Optimization

### 1. Cache Metrics

Add caching to the metrics calculation:

```php
private function calculateMetrics(User $user): array
{
    return Cache::remember("user_metrics_{$user->id}", 3600, function () use ($user) {
        // Metrics calculation logic
    });
}
```

### 2. Eager Load Relationships

In the `render()` method:

```php
$allUsers = User::query()
    ->where('role', '!=', 'admin')
    ->with('dutySessions')
    ->get();
```

### 3. Limit Query Results

Add pagination at the database level:

```php
$allUsers = User::query()
    ->where('role', '!=', 'admin')
    ->paginate(50);
```

## Troubleshooting

### Component Not Showing

1. Verify Livewire is installed: `composer show livewire/livewire`
2. Check route is registered: `php artisan route:list | grep personnel`
3. Verify view exists: `resources/views/livewire/personnel.blade.php`

### No Data Displaying

1. Check database has users: `SELECT COUNT(*) FROM users WHERE role != 'admin';`
2. Check database has duty sessions: `SELECT COUNT(*) FROM duty_sessions;`
3. Verify relationships are configured correctly

### Styling Issues

1. Ensure Tailwind CSS is compiled: `npm run build`
2. Check Tailwind config includes Livewire views:
   ```js
   content: [
       './resources/views/**/*.blade.php',
       './app/Livewire/**/*.php',
   ]
   ```

### Slow Performance

1. Check database indexes on `users.id`, `duty_sessions.volunteer_id`
2. Reduce items per page
3. Implement caching for metrics
4. Use database query optimization

## API Integration

If you need to expose this data via API:

```php
// routes/api.php
Route::get('/personnel', function () {
    $component = new Personnel();
    return response()->json($component->render()->getData());
});
```

## Accessibility

The component includes:
- ARIA labels on all interactive elements
- Semantic HTML structure
- Keyboard navigation support
- Color contrast compliance
- Screen reader friendly

## Browser Support

- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support
- IE11: Not supported (uses modern CSS Grid)

## License

This component is part of the NSRC AMS system.
