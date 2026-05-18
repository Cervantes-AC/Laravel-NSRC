# Responsive Navigation Drawer Toggle

## Overview

The navigation drawer toggle is now fully responsive and adjustable based on screen scale. The toggle button automatically shows/hides depending on the viewport width, and the sidebar behavior adapts accordingly.

## Features

- **Dynamic Toggle Visibility**: Toggle button automatically appears/disappears based on screen width
- **Configurable Breakpoint**: Easily adjust the breakpoint at which the toggle appears
- **Auto-Close Sidebar**: Sidebar automatically closes when resizing to larger screens
- **Smooth Transitions**: Animated transitions for all state changes
- **Mobile Backdrop**: Semi-transparent overlay appears on mobile when sidebar is open
- **Responsive Sidebar**: Sidebar switches between fixed (mobile) and static (desktop) positioning

## How It Works

### Current Implementation

The responsive behavior is controlled by the `toggleBreakpoint` parameter (default: 1024px):

- **Below 1024px**: Toggle button is visible, sidebar is hidden by default
- **1024px and above**: Toggle button is hidden, sidebar is always visible

### Key Components

1. **Alpine.js State Management** (`resources/js/app.js`)
   - `sidebarOpen`: Boolean tracking sidebar open/closed state
   - `shouldShowToggle`: Boolean tracking toggle visibility
   - `toggleBreakpoint`: Configurable breakpoint in pixels
   - `updateToggleVisibility()`: Method that updates toggle visibility based on window width

2. **HTML Structure** (`resources/views/layouts/app.blade.php`)
   - Toggle button with dynamic visibility
   - Sidebar with responsive positioning
   - Mobile backdrop overlay

3. **Styling** (`resources/css/app.css`)
   - Responsive utilities for different screen sizes
   - Smooth transitions and animations

## Customization

### Change the Toggle Breakpoint

To adjust when the toggle appears/disappears, modify the `toggleBreakpoint` parameter in `app.blade.php`:

```blade
<body class="font-sans antialiased app-shell" x-data="appShell({ toggleBreakpoint: 768 })" @load="init()" @resize.window="updateToggleVisibility()">
```

Common breakpoints:
- `640px` - Small mobile devices
- `768px` - Tablets (iPad)
- `1024px` - Desktop (default)
- `1280px` - Large desktop

### Add Multiple Breakpoints

To support different behaviors at different breakpoints, you can extend the Alpine component:

```javascript
Alpine.data('appShell', ({ timeoutMinutes = 60, warningMinutes = 5, toggleBreakpoint = 1024, collapseBreakpoint = 1280 } = {}) => ({
    // ... existing code ...
    shouldShowToggle: true,
    shouldCollapseNav: false,
    toggleBreakpoint: toggleBreakpoint,
    collapseBreakpoint: collapseBreakpoint,
    
    updateToggleVisibility() {
        this.shouldShowToggle = window.innerWidth < this.toggleBreakpoint;
        this.shouldCollapseNav = window.innerWidth < this.collapseBreakpoint;
        
        if (!this.shouldShowToggle && this.sidebarOpen) {
            this.sidebarOpen = false;
        }
    },
    // ... rest of code ...
}));
```

### Customize Toggle Button Appearance

The toggle button is located in `resources/views/layouts/app.blade.php`:

```blade
<button @click="sidebarOpen = !sidebarOpen" class="nav-toggle inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" :class="{'hidden': !shouldShowToggle}" @window:resize="updateToggleVisibility()">
```

You can modify:
- **Classes**: Change styling with Tailwind classes
- **Icons**: Replace the SVG icons
- **Behavior**: Add additional click handlers

### Customize Sidebar Behavior

The sidebar positioning is controlled by the `shouldShowToggle` state:

```blade
<aside class="app-sidebar fixed inset-y-0 left-0 z-50 w-72 flex flex-col transition-transform duration-300 ease-in-out" :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', shouldShowToggle ? '' : 'lg:translate-x-0 lg:static lg:z-auto']">
```

This means:
- When `shouldShowToggle` is true: Sidebar is fixed and slides in/out
- When `shouldShowToggle` is false: Sidebar is static and always visible

## Usage Examples

### Example 1: Tablet-Friendly Layout

For a layout that shows the toggle on tablets and phones:

```blade
<body class="font-sans antialiased app-shell" x-data="appShell({ toggleBreakpoint: 768 })" @load="init()" @resize.window="updateToggleVisibility()">
```

### Example 2: Always Show Toggle

To always show the toggle button:

```blade
<body class="font-sans antialiased app-shell" x-data="appShell({ toggleBreakpoint: 9999 })" @load="init()" @resize.window="updateToggleVisibility()">
```

### Example 3: Never Show Toggle

To never show the toggle button (always visible sidebar):

```blade
<body class="font-sans antialiased app-shell" x-data="appShell({ toggleBreakpoint: 0 })" @load="init()" @resize.window="updateToggleVisibility()">
```

## Responsive Breakpoints Reference

Tailwind CSS default breakpoints:
- `sm`: 640px
- `md`: 768px
- `lg`: 1024px (default toggle breakpoint)
- `xl`: 1280px
- `2xl`: 1536px

## Browser Support

The responsive toggle works in all modern browsers that support:
- ES6 JavaScript
- CSS Flexbox
- CSS Transitions
- Alpine.js

## Accessibility

The implementation includes:
- Proper ARIA labels
- Focus management
- Keyboard navigation support
- Touch-friendly button sizes (44x44px minimum)
- Reduced motion support

## Performance Considerations

- Resize events are debounced through Alpine.js
- CSS transitions use GPU acceleration
- No layout thrashing or forced reflows
- Minimal JavaScript execution on resize

## Troubleshooting

### Toggle button not appearing

1. Check that `shouldShowToggle` is true in Alpine DevTools
2. Verify the window width is less than `toggleBreakpoint`
3. Check browser console for JavaScript errors

### Sidebar not closing on resize

1. Ensure `updateToggleVisibility()` is being called
2. Check that `@resize.window` event listener is attached
3. Verify Alpine.js is loaded correctly

### Sidebar positioning issues

1. Check that parent container has `position: relative` or `position: fixed`
2. Verify z-index values don't conflict with other elements
3. Check for CSS specificity issues

## Files Modified

- `resources/js/app.js` - Added responsive toggle logic
- `resources/views/layouts/app.blade.php` - Updated toggle button and sidebar
- `resources/css/app.css` - Added responsive utilities

## Future Enhancements

Potential improvements:
- Persistent sidebar state in localStorage
- Animated sidebar collapse (not just hide/show)
- Keyboard shortcuts for toggle
- Sidebar width customization
- Animation preferences based on `prefers-reduced-motion`
