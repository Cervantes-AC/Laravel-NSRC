# Personnel Component - Laravel Livewire Implementation

## Overview
The Personnel component has been completely rebuilt as a Laravel Livewire component with advanced metrics calculation, compliance issue detection, and a modern UI matching the React design you provided.

## Features Implemented

### 1. **Metrics Calculation**
- **Regular Hours**: Time logged up to 8 hours per day
- **Overtime Hours**: Time logged beyond 8 hours per day
- **Undertime Hours**: Sessions with less than 1 hour duration
- **Session Count**: Total number of duty sessions per person
- **Invalid Record Count**: Number of sessions with compliance issues

### 2. **Compliance Issue Detection**
The component automatically detects and flags the following issues:
- **MISSING_TIMEOUT**: No time-out recorded (session still open)
- **ZERO_DURATION**: Time-out is not after time-in
- **FUTURE_DATE**: Session date is in the future
- **DUPLICATE**: (Framework ready for implementation)
- **OVERLAP**: (Framework ready for implementation)

### 3. **Advanced Filtering**
- **Search**: By full name, serial number, or email
- **Compliance Filter**: 
  - All: Show all personnel
  - Clean: Show only personnel with no issues
  - Issues Only: Show only personnel with compliance issues

### 4. **Sorting Options**
- By Name (alphabetical)
- By Sessions (count)
- By Hours (total regular + overtime)
- By Issues (count)
- By Role
- Toggle between ascending/descending

### 5. **View Modes**
- **List View**: Detailed card layout with full metrics
- **Grid View**: Compact card layout (3 columns on desktop)

### 6. **Pagination**
- Configurable items per page (10 for list, 12 for grid)
- Smart pagination controls with ellipsis
- Page navigation buttons

### 7. **KPI Summary Dashboard**
- Total Staff count
- Clean Records count
- With Issues count
- Total Hours across all personnel

## File Structure

### Backend
```
app/Livewire/Personnel.php
```
- Livewire component class
- Metrics calculation logic
- Issue detection logic
- Filtering and sorting logic

### Frontend
```
resources/views/livewire/personnel.blade.php
```
- Blade template with Tailwind CSS
- Responsive design (mobile, tablet, desktop)
- Interactive UI components
- KPI cards and metrics display

## Component Properties

### Public Properties
```php
public string $search = '';                    // Search query
public string $sortBy = 'name';               // Sort field
public string $sortDirection = 'asc';         // Sort direction
public int $perPage = 10;                     // Items per page
public string $complianceFilter = 'all';      // Compliance filter
public string $viewMode = 'list';             // View mode (list/grid)
public bool $showFormula = false;             // Show formula modal
public ?string $selectedPersonnelName = null; // Selected person for history
```

## Public Methods

### Filtering & Sorting
```php
toggleSort(string $field)           // Toggle sort by field
updatingSearch()                    // Reset page on search
updatingComplianceFilter()          // Reset page on filter change
updatingViewMode()                  // Reset page on view mode change
```

### UI Actions
```php
toggleFormula()                     // Toggle formula modal
viewHistory(string $name)           // View person's history
closeHistory()                      // Close history modal
```

### Pagination
```php
nextPage()                          // Go to next page
previousPage()                      // Go to previous page
gotoPage(int $page)                 // Go to specific page
```

## Data Structure

### Enriched Personnel Data
Each person in the list includes:
```php
[
    'id' => int,
    'fullName' => string,
    'volunteerId' => string,
    'email' => string,
    'serialNumber' => string,
    'role' => string,
    'avatar' => ?string,
    'sessionCount' => int,
    'totalRegularMinutes' => int,
    'totalOvertimeMinutes' => int,
    'totalUndertimeMinutes' => int,
    'invalidRecordCount' => int,
    'issues' => array,
    'lastActive' => ?string,
]
```

## Styling

### Color Scheme
- **Admin**: Orange
- **Officer**: Red
- **Member**: Slate
- **Volunteer**: Blue

### Issue Type Colors
- **MISSING_TIMEOUT**: Red
- **DUPLICATE**: Orange
- **OVERLAP**: Purple
- **ZERO_DURATION**: Red
- **FUTURE_DATE**: Blue
- **UNKNOWN**: Slate

## Usage

### In Routes
```php
Route::get('/personnel', Personnel::class)->name('personnel.index');
```

### In Blade Template
```blade
<livewire:personnel />
```

## Performance Considerations

1. **Metrics Calculation**: Performed on-demand during render
2. **Eager Loading**: DutySession relationships are loaded with users
3. **Pagination**: Reduces DOM elements for better performance
4. **Filtering**: Applied in-memory after data retrieval

## Future Enhancements

1. **Export Functionality**: Export personnel data to CSV/Excel
2. **History Modal**: View detailed attendance history for each person
3. **Formula Modal**: Display hour calculation rules
4. **Batch Actions**: Bulk operations on selected personnel
5. **Advanced Reporting**: Generate compliance reports
6. **Real-time Updates**: WebSocket integration for live updates

## Testing

To test the component:

1. Ensure you have personnel records in the database
2. Ensure you have duty sessions linked to personnel
3. Navigate to the personnel page
4. Test filtering, sorting, and pagination
5. Verify metrics calculations are correct

## Troubleshooting

### No personnel showing
- Check that users exist in the database with role != 'admin'
- Verify DutySession relationships are properly configured

### Incorrect metrics
- Verify DutySession time_in and time_out are properly set
- Check that duration_minutes is calculated correctly

### Styling issues
- Ensure Tailwind CSS is properly compiled
- Check that all CSS classes are included in your Tailwind config

## Notes

- The component excludes admin users from the personnel list
- All times are displayed in the application's timezone
- Metrics are calculated fresh on each render (can be optimized with caching)
- The component is fully responsive and mobile-friendly
