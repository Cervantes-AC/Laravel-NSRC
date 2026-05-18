# Navigation Toggle - Visual Guide

## Responsive Behavior Diagram

### Small Screens (< 1024px)

```
┌─────────────────────────────────────────┐
│ ☰  Operations Console                   │  ← Toggle visible
├─────────────────────────────────────────┤
│                                         │
│  Main Content Area                      │
│                                         │
│  (Sidebar is hidden by default)         │
│                                         │
└─────────────────────────────────────────┘

When toggle is clicked:

┌─────────────────────────────────────────┐
│ ✕  Operations Console                   │  ← Toggle shows X
├──────────────┬─────────────────────────┤
│ Navigation   │                         │
│ ─────────    │  Main Content Area      │
│ • Dashboard  │                         │
│ • Sessions   │  (Sidebar slides in)    │
│ • Personnel  │                         │
│ • Accounts   │                         │
│              │                         │
└──────────────┴─────────────────────────┘
```

### Large Screens (≥ 1024px)

```
┌──────────────┬─────────────────────────────────┐
│ Navigation   │ Operations Console              │  ← Toggle hidden
│ ─────────    ├─────────────────────────────────┤
│ • Dashboard  │                                 │
│ • Sessions   │  Main Content Area              │
│ • Personnel  │                                 │
│ • Accounts   │  (Sidebar always visible)       │
│              │                                 │
│              │                                 │
└──────────────┴─────────────────────────────────┘
```

## State Diagram

```
┌─────────────────────────────────────────────────────┐
│                  Window Resize Event                │
└────────────────────┬────────────────────────────────┘
                     │
                     ▼
        ┌────────────────────────────┐
        │ updateToggleVisibility()   │
        └────────────┬───────────────┘
                     │
                     ▼
        ┌────────────────────────────────────┐
        │ Check: window.innerWidth <         │
        │        toggleBreakpoint?           │
        └────┬──────────────────────────┬────┘
             │                          │
        YES  │                          │  NO
             ▼                          ▼
    ┌─────────────────┐      ┌──────────────────┐
    │ shouldShowToggle│      │ shouldShowToggle │
    │      = true     │      │      = false     │
    │                 │      │                  │
    │ • Toggle visible│      │ • Toggle hidden  │
    │ • Sidebar fixed │      │ • Sidebar static │
    │ • Mobile mode   │      │ • Desktop mode   │
    └─────────────────┘      └──────────────────┘
             │                          │
             └──────────┬───────────────┘
                        │
                        ▼
            ┌──────────────────────────┐
            │ If resizing to large     │
            │ screen and sidebar open: │
            │ Close sidebar            │
            └──────────────────────────┘
```

## Toggle Button States

### Hidden State (Large Screens)

```
┌─────────────────────────────────────────┐
│                                         │  ← No toggle button
│  Main Content Area                      │
│                                         │
└─────────────────────────────────────────┘
```

### Visible State - Closed (Small Screens)

```
┌─────────────────────────────────────────┐
│ ☰  Operations Console                   │  ← Toggle shows menu icon
├─────────────────────────────────────────┤
│                                         │
│  Main Content Area                      │
│                                         │
└─────────────────────────────────────────┘
```

### Visible State - Open (Small Screens)

```
┌─────────────────────────────────────────┐
│ ✕  Operations Console                   │  ← Toggle shows X icon
├──────────────┬─────────────────────────┤
│ Navigation   │                         │
│ ─────────    │  Main Content Area      │
│ • Dashboard  │                         │
│ • Sessions   │                         │
│ • Personnel  │                         │
│              │                         │
└──────────────┴─────────────────────────┘
```

## Breakpoint Reference

```
Screen Width Scale
├─ 0px ─────────────────────────────────────────────────────────────────────────┐
│                                                                                │
│  Mobile Phones                                                                │
│  ☰ Toggle visible                                                             │
│  Sidebar hidden by default                                                    │
│                                                                                │
├─ 640px ────────────────────────────────────────────────────────────────────────┤
│                                                                                │
│  Tablets (Portrait)                                                           │
│  ☰ Toggle visible (if breakpoint ≤ 640px)                                    │
│  Sidebar hidden by default                                                    │
│                                                                                │
├─ 768px ────────────────────────────────────────────────────────────────────────┤
│                                                                                │
│  Tablets (Landscape)                                                          │
│  ☰ Toggle visible (if breakpoint ≤ 768px)                                    │
│  Sidebar hidden by default                                                    │
│                                                                                │
├─ 1024px ───────────────────────────────────────────────────────────────────────┤
│                                                                                │
│  Desktop (Default Breakpoint)                                                 │
│  ☰ Toggle hidden (if breakpoint = 1024px)                                    │
│  Sidebar always visible                                                       │
│                                                                                │
├─ 1280px ───────────────────────────────────────────────────────────────────────┤
│                                                                                │
│  Large Desktop                                                                │
│  ☰ Toggle hidden                                                              │
│  Sidebar always visible                                                       │
│                                                                                │
└─ 1536px+ ──────────────────────────────────────────────────────────────────────┘

Extra Large Desktop
☰ Toggle hidden
Sidebar always visible
```

## Interaction Flow

### User Opens Sidebar on Mobile

```
User clicks toggle button
         │
         ▼
sidebarOpen = true
         │
         ▼
Sidebar slides in from left
         │
         ▼
Mobile backdrop appears
         │
         ▼
User can interact with sidebar
```

### User Closes Sidebar on Mobile

```
User clicks toggle button OR clicks backdrop
         │
         ▼
sidebarOpen = false
         │
         ▼
Sidebar slides out to left
         │
         ▼
Mobile backdrop disappears
         │
         ▼
Main content area fully visible
```

### User Resizes Window from Mobile to Desktop

```
Window width becomes ≥ 1024px
         │
         ▼
updateToggleVisibility() called
         │
         ▼
shouldShowToggle = false
         │
         ▼
Toggle button hidden
         │
         ▼
If sidebar was open:
  sidebarOpen = false
         │
         ▼
Sidebar auto-closes
         │
         ▼
Sidebar switches to static positioning
         │
         ▼
Sidebar always visible
```

## CSS Class Application

### Small Screens (shouldShowToggle = true)

```
Toggle Button:
  class="nav-toggle ... :class="{'hidden': !shouldShowToggle}"
  Result: visible (not hidden)

Sidebar:
  :class="[
    sidebarOpen ? 'translate-x-0' : '-translate-x-full',
    shouldShowToggle ? '' : 'lg:translate-x-0 lg:static lg:z-auto'
  ]"
  Result: fixed positioning, slides in/out

Mobile Backdrop:
  x-show="sidebarOpen && shouldShowToggle"
  Result: visible when sidebar is open
```

### Large Screens (shouldShowToggle = false)

```
Toggle Button:
  class="nav-toggle ... :class="{'hidden': !shouldShowToggle}"
  Result: hidden

Sidebar:
  :class="[
    sidebarOpen ? 'translate-x-0' : '-translate-x-full',
    shouldShowToggle ? '' : 'lg:translate-x-0 lg:static lg:z-auto'
  ]"
  Result: static positioning, always visible

Mobile Backdrop:
  x-show="sidebarOpen && shouldShowToggle"
  Result: hidden (even if sidebarOpen is true)
```

## Animation Timeline

### Opening Sidebar (300ms)

```
0ms:    Sidebar at -translate-x-full (off-screen)
        Backdrop at opacity-0

150ms:  Sidebar halfway in
        Backdrop halfway visible

300ms:  Sidebar at translate-x-0 (on-screen)
        Backdrop at opacity-100
```

### Closing Sidebar (300ms)

```
0ms:    Sidebar at translate-x-0 (on-screen)
        Backdrop at opacity-100

150ms:  Sidebar halfway out
        Backdrop halfway transparent

300ms:  Sidebar at -translate-x-full (off-screen)
        Backdrop at opacity-0
```

## Responsive Sidebar Width

```
Mobile (< 640px):
┌──────────────┐
│ Navigation   │  ← w-64 (256px)
│ (Sidebar)    │
└──────────────┘

Tablet (640px - 1024px):
┌──────────────┐
│ Navigation   │  ← w-72 (288px)
│ (Sidebar)    │
└──────────────┘

Desktop (> 1024px):
┌──────────────┐
│ Navigation   │  ← w-80 (320px)
│ (Sidebar)    │
└──────────────┘
```

## Z-Index Stacking

```
Layer 5: Mobile Backdrop (z-40)
         └─ Appears when sidebar is open on mobile

Layer 4: Sidebar (z-50)
         └─ Fixed on mobile, static on desktop

Layer 3: Top Bar (z-30)
         └─ Sticky header with toggle button

Layer 2: Main Content (z-auto)
         └─ Page content

Layer 1: Background (z-auto)
         └─ Page background
```

## Touch Target Sizes

```
Toggle Button:
┌─────────────────┐
│                 │
│      ☰          │  ← 44x44px minimum
│                 │
└─────────────────┘

Sidebar Menu Items:
┌─────────────────────────────────┐
│                                 │
│  • Dashboard                    │  ← 44px minimum height
│                                 │
├─────────────────────────────────┤
│                                 │
│  • Sessions                     │  ← 44px minimum height
│                                 │
└─────────────────────────────────┘
```

## Accessibility Features

```
Toggle Button:
  ✓ Focus ring (focus:ring-2 focus:ring-blue-500)
  ✓ Hover state (hover:text-slate-800 hover:bg-slate-100)
  ✓ Keyboard accessible
  ✓ ARIA labels

Sidebar:
  ✓ Semantic HTML (<aside>)
  ✓ Proper heading hierarchy
  ✓ Keyboard navigation
  ✓ Focus management

Mobile Backdrop:
  ✓ Clickable to close sidebar
  ✓ Keyboard support (Escape key)
  ✓ Proper z-index layering
```

## Performance Metrics

```
Resize Event Handling:
  ✓ Debounced through Alpine.js
  ✓ No layout thrashing
  ✓ GPU-accelerated transitions

CSS Transitions:
  ✓ 300ms duration
  ✓ ease-in-out timing
  ✓ Hardware acceleration

JavaScript Execution:
  ✓ Minimal on resize
  ✓ No memory leaks
  ✓ Efficient state updates
```

## Summary

The responsive navigation toggle provides:
- ✅ Automatic visibility based on screen width
- ✅ Smooth animations and transitions
- ✅ Mobile-optimized interaction
- ✅ Desktop-optimized layout
- ✅ Accessible to all users
- ✅ High performance
- ✅ Easy to customize

**Key concept:** The toggle button and sidebar behavior automatically adapt based on the `toggleBreakpoint` value, providing an optimal experience for all screen sizes.
