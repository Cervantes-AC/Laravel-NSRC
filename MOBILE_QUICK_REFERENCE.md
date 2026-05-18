# Mobile Responsiveness Quick Reference

## Quick Start

### Use Responsive Components
```blade
<!-- Tables -->
<x-responsive-table :headers="$headers" :rows="$rows" />

<!-- Forms -->
<x-responsive-form :columns="2">
    <input class="form-input-responsive">
</x-responsive-form>

<!-- Buttons -->
<x-responsive-button variant="primary" size="md" fullWidth="true">
    Click Me
</x-responsive-button>

<!-- Modals -->
<x-responsive-modal title="Title" size="md">
    Content
</x-responsive-modal>

<!-- Grids -->
<x-responsive-grid :cols="3" gap="4">
    <x-responsive-card>Content</x-responsive-card>
</x-responsive-grid>

<!-- Cards -->
<x-responsive-card title="Title">Content</x-responsive-card>

<!-- Containers -->
<x-responsive-container size="lg">
    Content
</x-responsive-container>
```

---

## CSS Utility Classes

### Responsive Padding
```html
<div class="p-responsive">Content</div>      <!-- p-4 sm:p-6 lg:p-8 -->
<div class="px-responsive">Content</div>     <!-- px-4 sm:px-6 lg:px-8 -->
<div class="py-responsive">Content</div>     <!-- py-4 sm:py-6 lg:py-8 -->
```

### Responsive Text
```html
<h1 class="text-responsive-3xl">Title</h1>  <!-- text-2xl sm:text-3xl lg:text-4xl -->
<p class="text-responsive-base">Text</p>     <!-- text-sm sm:text-base -->
```

### Responsive Grids
```html
<div class="grid-responsive-2">...</div>     <!-- 1 col mobile, 2 col tablet -->
<div class="grid-responsive-3">...</div>     <!-- 1 col mobile, 2 col tablet, 3 col desktop -->
<div class="grid-responsive-4">...</div>     <!-- 1 col mobile, 2 col tablet, 4 col desktop -->
```

### Responsive Buttons
```html
<button class="btn-responsive">Click</button>        <!-- 44x44px min -->
<button class="btn-responsive-lg">Click</button>     <!-- 48x48px min -->
<button class="touch-target">Click</button>          <!-- 44x44px min -->
```

### Responsive Forms
```html
<input class="form-input-responsive">
<label class="form-label-responsive">Label</label>
```

### Responsive Cards
```html
<div class="card-responsive">Content</div>
```

### Responsive Containers
```html
<div class="container-responsive">Content</div>
```

---

## Breakpoints

| Breakpoint | Size | Use Case |
|-----------|------|----------|
| Mobile | < 640px | Phones (iPhone SE, Galaxy S21) |
| `sm:` | 640px+ | Tablets (iPad mini) |
| `md:` | 768px+ | Tablets (iPad) |
| `lg:` | 1024px+ | Desktops |
| `xl:` | 1280px+ | Large desktops |
| `2xl:` | 1536px+ | Extra large desktops |

---

## Common Patterns

### Responsive Grid (1 → 2 → 4 columns)
```html
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div>Item 1</div>
    <div>Item 2</div>
    <div>Item 3</div>
    <div>Item 4</div>
</div>
```

### Responsive Flex (Stack → Row)
```html
<div class="flex flex-col sm:flex-row gap-4">
    <div>Item 1</div>
    <div>Item 2</div>
</div>
```

### Responsive Text (Small → Large)
```html
<h1 class="text-2xl sm:text-3xl lg:text-4xl">Title</h1>
```

### Responsive Padding (Small → Large)
```html
<div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
    Content
</div>
```

### Hide/Show by Breakpoint
```html
<div class="hidden lg:block">Desktop only</div>
<div class="lg:hidden">Mobile only</div>
```

---

## Touch Target Sizing

**Minimum: 44x44px** (recommended by Apple and Google)

```html
<!-- Good -->
<button class="px-4 py-2.5 min-h-[44px] min-w-[44px]">Click</button>

<!-- Or use utility -->
<button class="btn-responsive">Click</button>
```

---

## Form Best Practices

### Mobile-Friendly Form
```blade
<x-responsive-form :columns="1">
    <div>
        <label class="form-label-responsive">Name</label>
        <input type="text" class="form-input-responsive" placeholder="Enter name">
    </div>
    <div>
        <label class="form-label-responsive">Email</label>
        <input type="email" class="form-input-responsive" placeholder="Enter email">
    </div>
    <div>
        <x-responsive-button variant="primary" fullWidth="true">
            Submit
        </x-responsive-button>
    </div>
</x-responsive-form>
```

---

## Table Best Practices

### Mobile-Friendly Table
```blade
<x-responsive-table 
    :headers="['Name', 'Email', 'Status']"
    :rows="$data"
    :striped="true"
    :hoverable="true"
/>
```

---

## Modal Best Practices

### Mobile-Friendly Modal
```blade
<x-responsive-modal id="my-modal" title="Modal Title" size="md">
    <x-slot name="trigger">
        <x-responsive-button variant="primary">
            Open Modal
        </x-responsive-button>
    </x-slot>
    
    <p>Modal content here</p>
    
    <div class="flex gap-3 mt-6">
        <x-responsive-button variant="secondary" fullWidth="true">
            Cancel
        </x-responsive-button>
        <x-responsive-button variant="primary" fullWidth="true">
            Confirm
        </x-responsive-button>
    </div>
</x-responsive-modal>
```

---

## Testing on Mobile

### Chrome DevTools
1. Open DevTools (F12)
2. Click device toggle (Ctrl+Shift+M)
3. Select device or custom size
4. Test interactions

### Real Devices
1. Test on iPhone (375px, 390px, 430px)
2. Test on Android (360px, 390px, 412px)
3. Test on iPad (768px, 1024px)
4. Test touch interactions
5. Test performance on 4G

---

## Common Issues

| Issue | Solution |
|-------|----------|
| Text too small | Use `.text-responsive-*` |
| Buttons too small | Use `.btn-responsive` |
| Tables overflow | Use `<x-responsive-table>` |
| Forms hard to use | Use `<x-responsive-form>` |
| Modals overflow | Use `<x-responsive-modal>` |
| Content too wide | Use `.container-responsive` |
| Spacing wrong | Use `.p-responsive`, `.px-responsive`, `.py-responsive` |

---

## Performance Tips

1. **Use responsive images**
   ```html
   <img src="image.jpg" class="w-full h-auto" alt="Description">
   ```

2. **Lazy load images**
   ```html
   <img src="image.jpg" loading="lazy" alt="Description">
   ```

3. **Minimize CSS**
   - Tailwind CSS handles this automatically

4. **Minimize JavaScript**
   - Use Alpine.js for lightweight interactivity
   - Avoid heavy libraries

5. **Optimize fonts**
   - Use system fonts when possible
   - Limit font weights

---

## Accessibility

All components include:
- ✅ Keyboard navigation
- ✅ ARIA labels
- ✅ Focus states
- ✅ Color contrast
- ✅ Touch targets (44x44px min)

---

## Resources

- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Mobile-First CSS](https://www.mobileapproaches.com/)
- [Touch Target Sizing](https://www.nngroup.com/articles/touch-target-size/)
- [Web Accessibility](https://www.w3.org/WAI/WCAG21/quickref/)

---

## Component Props

### responsive-table
```blade
<x-responsive-table 
    :headers="[]"           <!-- Array of header names -->
    :rows="[]"              <!-- Array of row data -->
    :striped="true"         <!-- Alternate row colors -->
    :hoverable="true"       <!-- Hover effect -->
/>
```

### responsive-form
```blade
<x-responsive-form 
    :columns="1"            <!-- 1, 2, or 3 columns -->
    gap="gap-6"             <!-- Gap between items -->
>
```

### responsive-button
```blade
<x-responsive-button 
    variant="primary"       <!-- primary, secondary, danger, success, outline, ghost -->
    size="md"               <!-- sm, md, lg -->
    fullWidth="false"       <!-- Full width button -->
    icon="<svg>..."         <!-- Optional icon -->
>
```

### responsive-modal
```blade
<x-responsive-modal 
    id="modal-id"           <!-- Unique ID -->
    title="Title"           <!-- Modal title -->
    size="md"               <!-- sm, md, lg, xl, 2xl -->
>
```

### responsive-grid
```blade
<x-responsive-grid 
    :cols="1"               <!-- 1, 2, 3, 4, or 6 columns -->
    gap="4"                 <!-- 2, 3, 4, 6, or 8 -->
>
```

### responsive-card
```blade
<x-responsive-card 
    title="Title"           <!-- Card title -->
    icon="<svg>..."         <!-- Optional icon -->
    :hoverable="true"       <!-- Hover effect -->
    padding="p-4 sm:p-6"    <!-- Custom padding -->
>
```

### responsive-container
```blade
<x-responsive-container 
    size="lg"               <!-- sm, md, lg, xl, full -->
>
```

---

## Need Help?

1. Check `MOBILE_RESPONSIVENESS_GUIDE.md` for detailed info
2. Review component examples in `resources/views/components/`
3. Test with Chrome DevTools device emulation
4. Check browser console for errors
