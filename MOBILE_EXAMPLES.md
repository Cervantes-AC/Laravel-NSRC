# Mobile Responsiveness Examples

## Real-World Implementation Examples

### Example 1: Responsive Dashboard Card Grid

**Before (Not Mobile-Friendly):**
```blade
<div class="grid grid-cols-4 gap-4">
    <div class="bg-white p-6 rounded-lg">
        <h3 class="text-lg font-bold">Total Records</h3>
        <p class="text-3xl font-black">{{ $totalRecords }}</p>
    </div>
    <!-- More cards... -->
</div>
```

**After (Mobile-Friendly):**
```blade
<x-responsive-grid :cols="4" gap="4">
    <x-responsive-card title="Total Records">
        <p class="text-3xl font-black">{{ $totalRecords }}</p>
    </x-responsive-card>
    <!-- More cards... -->
</x-responsive-grid>
```

**Result:**
- Mobile: 1 column
- Tablet: 2 columns
- Desktop: 4 columns

---

### Example 2: Responsive Personnel Table

**Before (Overflows on Mobile):**
```blade
<table class="w-full">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Hours</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($personnel as $person)
            <tr>
                <td>{{ $person->name }}</td>
                <td>{{ $person->email }}</td>
                <td>{{ $person->status }}</td>
                <td>{{ $person->hours }}</td>
                <td>
                    <a href="{{ route('admin.personnel.edit', $person) }}">Edit</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

**After (Mobile-Friendly):**
```blade
<x-responsive-table 
    :headers="['Name', 'Email', 'Status', 'Hours', 'Actions']"
    :rows="$personnel->map(fn($p) => [
        $p->name,
        $p->email,
        $p->status,
        $p->hours,
        '<a href=\"' . route('admin.personnel.edit', $p) . '\">Edit</a>'
    ])->toArray()"
    :striped="true"
    :hoverable="true"
/>
```

**Result:**
- Mobile: Card layout with labels
- Desktop: Traditional table layout

---

### Example 3: Responsive Form

**Before (Not Mobile-Friendly):**
```blade
<form method="POST" action="{{ route('admin.personnel.store') }}">
    @csrf
    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
            <input type="text" name="first_name" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
            <input type="text" name="last_name" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
            <input type="tel" name="phone" class="w-full px-4 py-2 border rounded-lg">
        </div>
    </div>
    <div class="mt-6 flex gap-3">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save</button>
        <a href="{{ route('admin.personnel.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg">Cancel</a>
    </div>
</form>
```

**After (Mobile-Friendly):**
```blade
<x-responsive-form :columns="2" gap="gap-6">
    <div>
        <label class="form-label-responsive">First Name</label>
        <input type="text" name="first_name" class="form-input-responsive" placeholder="Enter first name">
    </div>
    <div>
        <label class="form-label-responsive">Last Name</label>
        <input type="text" name="last_name" class="form-input-responsive" placeholder="Enter last name">
    </div>
    <div>
        <label class="form-label-responsive">Email</label>
        <input type="email" name="email" class="form-input-responsive" placeholder="Enter email">
    </div>
    <div>
        <label class="form-label-responsive">Phone</label>
        <input type="tel" name="phone" class="form-input-responsive" placeholder="Enter phone">
    </div>
    <div class="col-span-1 sm:col-span-2 flex flex-col sm:flex-row gap-3">
        <x-responsive-button variant="primary" fullWidth="true">
            Save
        </x-responsive-button>
        <x-responsive-button variant="secondary" fullWidth="true">
            Cancel
        </x-responsive-button>
    </div>
</x-responsive-form>
```

**Result:**
- Mobile: 1 column, full-width buttons
- Tablet: 2 columns, side-by-side buttons
- Desktop: 2 columns, side-by-side buttons

---

### Example 4: Responsive Modal

**Before (May Overflow on Mobile):**
```blade
<div x-data="{ open: false }" class="relative z-50">
    <button @click="open = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
        Open Modal
    </button>
    
    <div x-show="open" class="fixed inset-0 bg-black/50 z-40" @click="open = false"></div>
    
    <div x-show="open" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 bg-white rounded-lg p-6 z-50">
        <h2 class="text-xl font-bold mb-4">Confirm Action</h2>
        <p class="text-gray-600 mb-6">Are you sure you want to proceed?</p>
        <div class="flex gap-3">
            <button @click="open = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg">Cancel</button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Confirm</button>
        </div>
    </div>
</div>
```

**After (Mobile-Friendly):**
```blade
<x-responsive-modal id="confirm-modal" title="Confirm Action" size="md">
    <x-slot name="trigger">
        <x-responsive-button variant="primary">
            Open Modal
        </x-responsive-button>
    </x-slot>
    
    <p class="text-gray-600 mb-6">Are you sure you want to proceed?</p>
    
    <div class="flex flex-col sm:flex-row gap-3">
        <x-responsive-button variant="secondary" fullWidth="true">
            Cancel
        </x-responsive-button>
        <x-responsive-button variant="primary" fullWidth="true">
            Confirm
        </x-responsive-button>
    </div>
</x-responsive-modal>
```

**Result:**
- Mobile: Full-screen modal with stacked buttons
- Desktop: Centered modal with side-by-side buttons

---

### Example 5: Responsive Navigation Bar

**Before (May Not Adapt Well):**
```blade
<nav class="flex items-center justify-between px-6 py-4 bg-white border-b">
    <div class="text-xl font-bold">NSRC AMS</div>
    <div class="flex gap-6">
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
        <a href="{{ route('admin.personnel.index') }}" class="text-gray-600 hover:text-gray-900">Personnel</a>
        <a href="{{ route('admin.sessions.index') }}" class="text-gray-600 hover:text-gray-900">Sessions</a>
    </div>
</nav>
```

**After (Mobile-Friendly):**
```blade
<nav class="flex items-center justify-between px-responsive py-4 bg-white border-b">
    <div class="text-responsive-lg font-bold">NSRC AMS</div>
    <div class="nav-responsive">
        <a href="{{ route('dashboard') }}" class="text-responsive-base text-gray-600 hover:text-gray-900">Dashboard</a>
        <a href="{{ route('admin.personnel.index') }}" class="text-responsive-base text-gray-600 hover:text-gray-900">Personnel</a>
        <a href="{{ route('admin.sessions.index') }}" class="text-responsive-base text-gray-600 hover:text-gray-900">Sessions</a>
    </div>
</nav>
```

**Result:**
- Mobile: Responsive padding and text sizes
- Desktop: Proper spacing and readability

---

### Example 6: Responsive Hero Section

**Before (Not Mobile-Friendly):**
```blade
<div class="bg-gradient-to-r from-blue-600 to-blue-800 px-8 py-16">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white mb-4">Welcome to NSRC AMS</h1>
            <p class="text-xl text-blue-100 mb-8">Manage attendance and track volunteer hours</p>
            <button class="px-8 py-3 bg-white text-blue-600 font-bold rounded-lg">Get Started</button>
        </div>
        <img src="hero.jpg" alt="Hero" class="w-96 h-96">
    </div>
</div>
```

**After (Mobile-Friendly):**
```blade
<div class="bg-gradient-to-r from-blue-600 to-blue-800 p-responsive">
    <x-responsive-container size="lg">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-responsive">
            <div>
                <h1 class="text-responsive-3xl font-bold text-white mb-4">Welcome to NSRC AMS</h1>
                <p class="text-responsive-lg text-blue-100 mb-8">Manage attendance and track volunteer hours</p>
                <x-responsive-button variant="primary" size="lg">
                    Get Started
                </x-responsive-button>
            </div>
            <img src="hero.jpg" alt="Hero" class="w-full sm:w-96 h-auto">
        </div>
    </x-responsive-container>
</div>
```

**Result:**
- Mobile: Stacked layout, responsive text and padding
- Desktop: Side-by-side layout with proper spacing

---

### Example 7: Responsive Metrics Dashboard

**Before (Not Mobile-Friendly):**
```blade
<div class="grid grid-cols-6 gap-4">
    <div class="bg-white p-6 rounded-lg border">
        <p class="text-xs font-bold text-gray-500 uppercase">Total Records</p>
        <p class="text-3xl font-black text-gray-900 mt-2">{{ $totalRecords }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg border">
        <p class="text-xs font-bold text-gray-500 uppercase">Active Users</p>
        <p class="text-3xl font-black text-gray-900 mt-2">{{ $activeUsers }}</p>
    </div>
    <!-- More metrics... -->
</div>
```

**After (Mobile-Friendly):**
```blade
<x-responsive-grid :cols="6" gap="4">
    <x-responsive-card>
        <p class="text-xs font-bold text-gray-500 uppercase">Total Records</p>
        <p class="text-3xl font-black text-gray-900 mt-2">{{ $totalRecords }}</p>
    </x-responsive-card>
    <x-responsive-card>
        <p class="text-xs font-bold text-gray-500 uppercase">Active Users</p>
        <p class="text-3xl font-black text-gray-900 mt-2">{{ $activeUsers }}</p>
    </x-responsive-card>
    <!-- More metrics... -->
</x-responsive-grid>
```

**Result:**
- Mobile: 1 column
- Tablet: 2 columns
- Desktop: 6 columns

---

### Example 8: Responsive Filter Bar

**Before (Overflows on Mobile):**
```blade
<div class="flex gap-3 mb-6">
    <select class="px-4 py-2 border rounded-lg">
        <option>All Statuses</option>
        <option>Complete</option>
        <option>Pending</option>
    </select>
    <select class="px-4 py-2 border rounded-lg">
        <option>All Sectors</option>
        <option>Sector A</option>
        <option>Sector B</option>
    </select>
    <input type="text" placeholder="Search..." class="px-4 py-2 border rounded-lg flex-1">
    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Search</button>
    <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg">Clear</button>
</div>
```

**After (Mobile-Friendly):**
```blade
<div class="flex flex-col sm:flex-row gap-responsive mb-6">
    <select class="form-input-responsive">
        <option>All Statuses</option>
        <option>Complete</option>
        <option>Pending</option>
    </select>
    <select class="form-input-responsive">
        <option>All Sectors</option>
        <option>Sector A</option>
        <option>Sector B</option>
    </select>
    <input type="text" placeholder="Search..." class="form-input-responsive flex-1">
    <div class="flex gap-3">
        <x-responsive-button variant="primary" fullWidth="true">
            Search
        </x-responsive-button>
        <x-responsive-button variant="secondary" fullWidth="true">
            Clear
        </x-responsive-button>
    </div>
</div>
```

**Result:**
- Mobile: Stacked layout, full-width inputs and buttons
- Desktop: Horizontal layout with proper spacing

---

### Example 9: Responsive Action Buttons

**Before (Buttons Too Small):**
```blade
<div class="flex gap-2">
    <a href="{{ route('admin.personnel.edit', $person) }}" class="px-2 py-1 text-xs bg-blue-600 text-white rounded">Edit</a>
    <button onclick="delete({{ $person->id }})" class="px-2 py-1 text-xs bg-red-600 text-white rounded">Delete</button>
</div>
```

**After (Touch-Friendly):**
```blade
<div class="flex flex-col sm:flex-row gap-2">
    <x-responsive-button 
        variant="primary" 
        size="sm"
        href="{{ route('admin.personnel.edit', $person) }}"
        fullWidth="true"
    >
        Edit
    </x-responsive-button>
    <x-responsive-button 
        variant="danger" 
        size="sm"
        onclick="delete({{ $person->id }})"
        fullWidth="true"
    >
        Delete
    </x-responsive-button>
</div>
```

**Result:**
- Mobile: Full-width buttons, 44x44px minimum
- Desktop: Side-by-side buttons

---

### Example 10: Responsive Page Layout

**Before (Not Mobile-Friendly):**
```blade
<div class="max-w-7xl mx-auto px-8 py-12">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-4xl font-bold">Personnel Management</h1>
        <a href="{{ route('admin.personnel.create') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg">Add Personnel</a>
    </div>
    
    <div class="grid grid-cols-4 gap-4 mb-8">
        <!-- Metric cards -->
    </div>
    
    <div class="bg-white rounded-lg border p-6">
        <!-- Table -->
    </div>
</div>
```

**After (Mobile-Friendly):**
```blade
<x-responsive-container size="lg">
    <div class="section-responsive">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-responsive mb-8">
            <h1 class="header-responsive">Personnel Management</h1>
            <x-responsive-button 
                variant="primary" 
                size="lg"
                href="{{ route('admin.personnel.create') }}"
            >
                Add Personnel
            </x-responsive-button>
        </div>
        
        <x-responsive-grid :cols="4" gap="4" class="mb-8">
            <!-- Metric cards -->
        </x-responsive-grid>
        
        <x-responsive-card>
            <!-- Table -->
        </x-responsive-card>
    </div>
</x-responsive-container>
```

**Result:**
- Mobile: Responsive padding, stacked layout, full-width buttons
- Desktop: Proper spacing and layout

---

## Testing These Examples

### On Mobile (375px - 390px)
1. All text should be readable
2. No horizontal scrolling
3. Buttons should be tappable (44x44px min)
4. Forms should be easy to fill
5. Tables should display as cards

### On Tablet (768px)
1. Layout should adapt to tablet size
2. Tables should show in table format
3. Grids should show 2 columns
4. Spacing should be appropriate

### On Desktop (1024px+)
1. Full layout should display
2. Grids should show full columns
3. Tables should display normally
4. Spacing should be generous

---

## Performance Considerations

1. **Images**: Use responsive sizes
   ```html
   <img src="image.jpg" class="w-full h-auto" alt="Description">
   ```

2. **Lazy Loading**: Load images on demand
   ```html
   <img src="image.jpg" loading="lazy" alt="Description">
   ```

3. **CSS**: Tailwind CSS handles optimization

4. **JavaScript**: Minimize on mobile
   - Use Alpine.js for lightweight interactivity
   - Avoid heavy libraries

---

## Accessibility

All examples include:
- ✅ Proper semantic HTML
- ✅ ARIA labels where needed
- ✅ Keyboard navigation
- ✅ Focus states
- ✅ Color contrast
- ✅ Touch targets (44x44px min)

---

## Next Steps

1. Review these examples
2. Apply patterns to your pages
3. Test on real devices
4. Monitor user feedback
5. Iterate based on feedback
