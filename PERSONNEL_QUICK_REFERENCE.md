# Personnel Component - Quick Reference

## 🚀 Quick Start (30 seconds)

### 1. Add Route
```php
// routes/web.php
Route::get('/personnel', \App\Livewire\Personnel::class)->name('personnel.index');
```

### 2. Add Navigation Link
```blade
<a href="{{ route('personnel.index') }}">Personnel</a>
```

### 3. Visit
```
http://yourapp.local/personnel
```

## 📋 Component Properties

| Property | Type | Default | Purpose |
|----------|------|---------|---------|
| `$search` | string | '' | Search query |
| `$sortBy` | string | 'name' | Sort field |
| `$sortDirection` | string | 'asc' | Sort direction |
| `$perPage` | int | 10 | Items per page |
| `$complianceFilter` | string | 'all' | Filter type |
| `$viewMode` | string | 'list' | View mode |
| `$showFormula` | bool | false | Show formula modal |
| `$selectedPersonnelName` | ?string | null | Selected person |

## 🎯 Public Methods

```php
// Sorting
toggleSort('name')              // Toggle sort by field

// Filtering
updatingSearch()                // Reset page on search
updatingComplianceFilter()      // Reset page on filter
updatingViewMode()              // Reset page on view change

// UI Actions
toggleFormula()                 // Toggle formula modal
viewHistory('name')             // View person's history
closeHistory()                  // Close history modal

// Pagination
nextPage()                      // Go to next page
previousPage()                  // Go to previous page
gotoPage(5)                     // Go to page 5
```

## 🎨 Blade Usage

### Search
```blade
<input wire:model.debounce.300ms="search" placeholder="Search...">
```

### Compliance Filter
```blade
<button wire:click="$set('complianceFilter', 'all')">All</button>
<button wire:click="$set('complianceFilter', 'compliance_only')">Clean</button>
<button wire:click="$set('complianceFilter', 'issues_only')">Issues</button>
```

### Sort
```blade
<button wire:click="toggleSort('name')">Name</button>
<button wire:click="toggleSort('sessions')">Sessions</button>
<button wire:click="toggleSort('hours')">Hours</button>
<button wire:click="toggleSort('issues')">Issues</button>
```

### View Mode
```blade
<button wire:click="$set('viewMode', 'list')">List</button>
<button wire:click="$set('viewMode', 'grid')">Grid</button>
```

### Pagination
```blade
<button wire:click="previousPage">Previous</button>
<button wire:click="gotoPage({{ $page }})">{{ $page }}</button>
<button wire:click="nextPage">Next</button>
```

## 📊 Metrics Formulas

| Metric | Formula | Example |
|--------|---------|---------|
| Regular | MIN(hours, 8) | 6h logged = 6h regular |
| Overtime | MAX(0, hours - 8) | 10h logged = 2h overtime |
| Undertime | hours < 1 ? hours : 0 | 30m logged = 30m undertime |

## 🚨 Issue Types

| Type | Condition | Color |
|------|-----------|-------|
| MISSING_TIMEOUT | No time_out recorded | Red |
| ZERO_DURATION | time_out ≤ time_in | Red |
| FUTURE_DATE | date > today | Blue |
| DUPLICATE | (Framework ready) | Orange |
| OVERLAP | (Framework ready) | Purple |

## 🎨 Role Colors

| Role | Background | Text | Border |
|------|-----------|------|--------|
| admin | bg-orange-50 | text-orange-800 | border-orange-200 |
| officer | bg-red-50 | text-red-800 | border-red-200 |
| member | bg-slate-50 | text-slate-700 | border-slate-200 |
| volunteer | bg-blue-50 | text-blue-700 | border-blue-200 |

## 📱 Responsive Breakpoints

| Device | Width | Columns |
|--------|-------|---------|
| Mobile | < 640px | 1 |
| Tablet | 640-1024px | 2 |
| Desktop | > 1024px | 3-4 |

## 🔧 Common Customizations

### Change Items Per Page
```php
public int $perPage = 25; // In Personnel.php
```

### Add Custom Sort
```php
// In render() method
match ($this->sortBy) {
    'custom' => $cmp = $a['field'] <=> $b['field'],
}
```

### Change Colors
```blade
@php
$roleColors = [
    'admin' => ['bg' => 'bg-purple-50', ...],
];
@endphp
```

### Add Issue Detection
```php
// In deriveIssues() method
if (/* condition */) {
    $issues[] = [
        'date' => $session->date->format('Y-m-d'),
        'type' => 'CUSTOM_TYPE',
        'description' => 'Description',
    ];
}
```

## 🐛 Quick Troubleshooting

| Issue | Solution |
|-------|----------|
| Component not showing | Check route registered, Livewire installed |
| No data | Verify users exist, check relationships |
| Styling broken | Run `npm run build`, clear cache |
| Slow performance | Add database indexes, implement caching |
| Search not working | Check search input has `wire:model` |
| Sorting not working | Verify `toggleSort()` is called |

## 📚 Documentation Files

- **PERSONNEL_COMPONENT_GUIDE.md** - Full feature documentation
- **PERSONNEL_INTEGRATION.md** - Integration and customization guide
- **IMPLEMENTATION_SUMMARY.md** - Implementation overview
- **PERSONNEL_QUICK_REFERENCE.md** - This file

## 🎯 Key Files

| File | Purpose |
|------|---------|
| `app/Livewire/Personnel.php` | Component logic |
| `resources/views/livewire/personnel.blade.php` | UI template |
| `app/Models/User.php` | User model (verify relationships) |
| `app/Models/DutySession.php` | DutySession model (verify relationships) |

## 💡 Tips & Tricks

1. **Debounce Search**: Search is debounced 300ms to reduce queries
2. **Smart Pagination**: Shows ellipsis for large page counts
3. **Responsive Grid**: Automatically adjusts columns based on screen size
4. **Color Coding**: Issues are color-coded for quick identification
5. **Real-time Filtering**: All filters update instantly with Livewire

## 🔐 Security

- Component excludes admin users from personnel list
- All user input is sanitized by Livewire
- Relationships are properly configured
- No sensitive data is exposed

## ⚡ Performance Tips

1. Add database indexes:
   ```sql
   CREATE INDEX idx_users_id ON users(id);
   CREATE INDEX idx_duty_sessions_volunteer_id ON duty_sessions(volunteer_id);
   ```

2. Implement caching:
   ```php
   Cache::remember("user_metrics_{$user->id}", 3600, function () {
       // Metrics calculation
   });
   ```

3. Use eager loading:
   ```php
   User::with('dutySessions')->get();
   ```

## 📞 Support Resources

- Laravel Livewire: https://livewire.laravel.com
- Tailwind CSS: https://tailwindcss.com
- Laravel Documentation: https://laravel.com/docs
- Component Code Comments: See Personnel.php

## ✅ Verification Checklist

- [ ] Livewire installed (`composer show livewire/livewire`)
- [ ] Route registered in `routes/web.php`
- [ ] Navigation link added
- [ ] Database has users with role != 'admin'
- [ ] Database has duty sessions
- [ ] Tailwind CSS compiled (`npm run build`)
- [ ] Livewire scripts in layout
- [ ] Component accessible at `/personnel`

## 🎉 You're Ready!

Your Personnel component is ready to use. Visit `/personnel` to see it in action!
