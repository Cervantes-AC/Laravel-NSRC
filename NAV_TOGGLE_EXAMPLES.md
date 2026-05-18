# Navigation Toggle - Code Examples

## Basic Implementation

### Current Default (1024px breakpoint)

```blade
<!-- resources/views/layouts/app.blade.php -->
<body class="font-sans antialiased app-shell" 
      x-data="appShell({ toggleBreakpoint: 1024 })" 
      @load="init()" 
      @resize.window="updateToggleVisibility()">
    <!-- Content -->
</body>
```

## Customization Examples

### Example 1: Tablet-Friendly Layout (768px)

Show toggle on tablets and phones, hide on desktop:

```blade
<body class="font-sans antialiased app-shell" 
      x-data="appShell({ toggleBreakpoint: 768 })" 
      @load="init()" 
      @resize.window="updateToggleVisibility()">
```

**Behavior:**
- Screens < 768px: Toggle visible, sidebar hidden
- Screens ≥ 768px: Toggle hidden, sidebar always visible

### Example 2: Mobile-Only Toggle (640px)

Show toggle only on phones:

```blade
<body class="font-sans antialiased app-shell" 
      x-data="appShell({ toggleBreakpoint: 640 })" 
      @load="init()" 
      @resize.window="updateToggleVisibility()">
```

**Behavior:**
- Screens < 640px: Toggle visible, sidebar hidden
- Screens ≥ 640px: Toggle hidden, sidebar always visible

### Example 3: Always Show Toggle

Keep toggle visible on all screen sizes:

```blade
<body class="font-sans antialiased app-shell" 
      x-data="appShell({ toggleBreakpoint: 9999 })" 
      @load="init()" 
      @resize.window="updateToggleVisibility()">
```

**Behavior:**
- All screens: Toggle always visible
- Sidebar toggles between hidden and visible

### Example 4: Never Show Toggle

Hide toggle on all screens (always visible sidebar):

```blade
<body class="font-sans antialiased app-shell" 
      x-data="appShell({ toggleBreakpoint: 0 })" 
      @load="init()" 
      @resize.window="updateToggleVisibility()">
```

**Behavior:**
- All screens: Toggle always hidden
- Sidebar always visible

### Example 5: Large Desktop Breakpoint (1280px)

Show toggle on smaller screens, hide on large desktops:

```blade
<body class="font-sans antialiased app-shell" 
      x-data="appShell({ toggleBreakpoint: 1280 })" 
      @load="init()" 
      @resize.window="updateToggleVisibility()">
```

**Behavior:**
- Screens < 1280px: Toggle visible, sidebar hidden
- Screens ≥ 1280px: Toggle hidden, sidebar always visible

## Advanced Customization

### Example 6: Multiple Breakpoints

Extend Alpine component for different behaviors at different breakpoints:

```javascript
// resources/js/app.js
Alpine.data('appShell', ({ 
    timeoutMinutes = 60, 
    warningMinutes = 5, 
    toggleBreakpoint = 1024,
    collapseBreakpoint = 1280 
} = {}) => ({
    sidebarOpen: false,
    sessionWarningVisible: false,
    sessionWarningCountdown: warningMinutes * 60,
    sessionWarningTimer: null,
    shouldShowToggle: true,
    shouldCollapseSidebar: false,
    toggleBreakpoint: toggleBreakpoint,
    collapseBreakpoint: collapseBreakpoint,
    
    init() {
        this.updateToggleVisibility();
        window.addEventListener('resize', () => this.updateToggleVisibility());
    },
    
    updateToggleVisibility() {
        // Show toggle if window width < toggleBreakpoint
        this.shouldShowToggle = window.innerWidth < this.toggleBreakpoint;
        
        // Collapse sidebar if window width < collapseBreakpoint
        this.shouldCollapseSidebar = window.innerWidth < this.collapseBreakpoint;
        
        // Auto-close sidebar when resizing to larger screens
        if (!this.shouldShowToggle && this.sidebarOpen) {
            this.sidebarOpen = false;
        }
    },
    
    // ... rest of methods ...
}));
```

Usage:

```blade
<body x-data="appShell({ toggleBreakpoint: 1024, collapseBreakpoint: 1280 })" 
      @load="init()" 
      @resize.window="updateToggleVisibility()">
```

### Example 7: Custom Toggle Button Styling

Customize the toggle button appearance:

```blade
<!-- Default styling -->
<button @click="sidebarOpen = !sidebarOpen" 
        class="nav-toggle inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" 
        :class="{'hidden': !shouldShowToggle}">
    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
        <path :class="{'hidden': sidebarOpen, 'inline-flex': !sidebarOpen}" 
              class="inline-flex" 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M4 6h16M4 12h16M4 18h16" />
        <path :class="{'hidden': !sidebarOpen, 'inline-flex': sidebarOpen}" 
              class="hidden" 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>

<!-- Custom styling with different colors -->
<button @click="sidebarOpen = !sidebarOpen" 
        class="nav-toggle inline-flex items-center justify-center p-2 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500" 
        :class="{'hidden': !shouldShowToggle}">
    <!-- SVG icon -->
</button>

<!-- Custom styling with different size -->
<button @click="sidebarOpen = !sidebarOpen" 
        class="nav-toggle inline-flex items-center justify-center p-3 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" 
        :class="{'hidden': !shouldShowToggle}">
    <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24">
        <!-- SVG icon -->
    </svg>
</button>
```

### Example 8: Sidebar with Collapse Animation

Add a collapse animation instead of just hide/show:

```blade
<!-- Sidebar with collapse animation -->
<aside class="app-sidebar fixed inset-y-0 left-0 z-50 w-72 flex flex-col transition-all duration-300 ease-in-out" 
       :class="[
           sidebarOpen ? 'translate-x-0' : '-translate-x-full',
           shouldShowToggle ? '' : 'lg:translate-x-0 lg:static lg:z-auto',
           shouldCollapseSidebar ? 'w-20' : 'w-72'
       ]">
    @include('layouts.navigation')
</aside>
```

### Example 9: Persistent Sidebar State

Save sidebar state to localStorage:

```javascript
// resources/js/app.js
Alpine.data('appShell', ({ toggleBreakpoint = 1024 } = {}) => ({
    sidebarOpen: localStorage.getItem('sidebarOpen') === 'true',
    shouldShowToggle: true,
    toggleBreakpoint: toggleBreakpoint,
    
    init() {
        this.updateToggleVisibility();
        window.addEventListener('resize', () => this.updateToggleVisibility());
    },
    
    updateToggleVisibility() {
        this.shouldShowToggle = window.innerWidth < this.toggleBreakpoint;
        if (!this.shouldShowToggle && this.sidebarOpen) {
            this.sidebarOpen = false;
        }
    },
    
    // Watch for sidebar state changes
    watch: {
        sidebarOpen(value) {
            localStorage.setItem('sidebarOpen', value);
        }
    },
    
    // ... rest of methods ...
}));
```

### Example 10: Keyboard Shortcut for Toggle

Add keyboard shortcut to toggle sidebar:

```blade
<body class="font-sans antialiased app-shell" 
      x-data="appShell({ toggleBreakpoint: 1024 })" 
      @load="init()" 
      @resize.window="updateToggleVisibility()"
      @keydown.escape="sidebarOpen = false"
      @keydown.ctrl.m="sidebarOpen = !sidebarOpen">
    <!-- Content -->
</body>
```

**Keyboard shortcuts:**
- `Escape`: Close sidebar
- `Ctrl+M`: Toggle sidebar

## Responsive Sidebar Width

### Example 11: Responsive Sidebar Width

Adjust sidebar width based on screen size:

```blade
<aside class="app-sidebar fixed inset-y-0 left-0 z-50 flex flex-col transition-all duration-300 ease-in-out" 
       :class="[
           sidebarOpen ? 'translate-x-0' : '-translate-x-full',
           shouldShowToggle ? '' : 'lg:translate-x-0 lg:static lg:z-auto',
           'w-64 sm:w-72 lg:w-80'
       ]">
    @include('layouts.navigation')
</aside>
```

## Testing Examples

### Example 12: Test Different Breakpoints

```html
<!-- Test breakpoint 640px -->
<body x-data="appShell({ toggleBreakpoint: 640 })" @load="init()" @resize.window="updateToggleVisibility()">

<!-- Test breakpoint 768px -->
<body x-data="appShell({ toggleBreakpoint: 768 })" @load="init()" @resize.window="updateToggleVisibility()">

<!-- Test breakpoint 1024px (default) -->
<body x-data="appShell({ toggleBreakpoint: 1024 })" @load="init()" @resize.window="updateToggleVisibility()">

<!-- Test breakpoint 1280px -->
<body x-data="appShell({ toggleBreakpoint: 1280 })" @load="init()" @resize.window="updateToggleVisibility()">
```

## CSS Customization

### Example 13: Custom Sidebar Styling

```css
/* resources/css/app.css */

/* Custom sidebar colors */
.app-sidebar {
    @apply bg-gradient-to-b from-slate-900 to-slate-950 text-white shadow-2xl;
}

/* Custom toggle button */
.nav-toggle {
    @apply transition-all duration-200 ease-in-out;
}

.nav-toggle:hover {
    @apply scale-110;
}

/* Custom backdrop */
.sidebar-backdrop {
    @apply fixed inset-0 z-40 bg-black/40 backdrop-blur-sm;
}

/* Custom sidebar animation */
.app-sidebar {
    @apply transition-all duration-300 ease-in-out;
}
```

## Complete Example

### Example 14: Full Implementation with All Features

```blade
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased app-shell" 
          x-data="appShell({ toggleBreakpoint: 1024 })" 
          @load="init()" 
          @resize.window="updateToggleVisibility()"
          @keydown.escape="sidebarOpen = false">
        
        <div class="min-h-screen flex">
            <!-- Mobile backdrop -->
            <div x-show="sidebarOpen && shouldShowToggle" 
                 @click="sidebarOpen = false" 
                 class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm">
            </div>

            <!-- Sidebar -->
            <aside class="app-sidebar fixed inset-y-0 left-0 z-50 w-72 flex flex-col transition-transform duration-300 ease-in-out" 
                   :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', shouldShowToggle ? '' : 'lg:translate-x-0 lg:static lg:z-auto']">
                @include('layouts.navigation')
            </aside>

            <!-- Main content -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top bar -->
                <header class="app-topbar sticky top-0 z-30">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <!-- Toggle button -->
                            <button @click="sidebarOpen = !sidebarOpen" 
                                    class="nav-toggle inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                    :class="{'hidden': !shouldShowToggle}">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': sidebarOpen, 'inline-flex': !sidebarOpen}" 
                                          class="inline-flex" 
                                          stroke-linecap="round" 
                                          stroke-linejoin="round" 
                                          stroke-width="2" 
                                          d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !sidebarOpen, 'inline-flex': sidebarOpen}" 
                                          class="hidden" 
                                          stroke-linecap="round" 
                                          stroke-linejoin="round" 
                                          stroke-width="2" 
                                          d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </header>

                <!-- Page content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
```

## Summary

These examples show how to:
- ✅ Change the toggle breakpoint
- ✅ Customize button styling
- ✅ Add multiple breakpoints
- ✅ Persist sidebar state
- ✅ Add keyboard shortcuts
- ✅ Customize animations
- ✅ Adjust sidebar width responsively

Choose the example that best fits your needs and customize as required!
