# Mobile Responsiveness Developer Checklist

## Pre-Implementation Checklist

### Setup
- [ ] Read `MOBILE_RESPONSIVENESS_GUIDE.md`
- [ ] Read `MOBILE_QUICK_REFERENCE.md`
- [ ] Review `MOBILE_EXAMPLES.md`
- [ ] Understand Tailwind CSS breakpoints
- [ ] Set up Chrome DevTools device emulation

### Environment
- [ ] Tailwind CSS 4 is installed ✓
- [ ] Alpine.js is available ✓
- [ ] Laravel Blade is configured ✓
- [ ] CSS utilities are loaded ✓

---

## Component Implementation Checklist

### When Using responsive-table
- [ ] Pass `:headers` array with column names
- [ ] Pass `:rows` array with row data
- [ ] Set `:striped="true"` for alternating colors
- [ ] Set `:hoverable="true"` for hover effects
- [ ] Test on mobile (should show as cards)
- [ ] Test on desktop (should show as table)
- [ ] Verify no horizontal scrolling on mobile

### When Using responsive-form
- [ ] Set `:columns="1"` for single column (mobile)
- [ ] Set `:columns="2"` for two columns (tablet+)
- [ ] Set `:columns="3"` for three columns (desktop+)
- [ ] Use `.form-input-responsive` for inputs
- [ ] Use `.form-label-responsive` for labels
- [ ] Stack buttons vertically on mobile
- [ ] Test form submission on mobile
- [ ] Verify touch targets are 44x44px min

### When Using responsive-button
- [ ] Choose appropriate `variant` (primary, secondary, danger, success, outline, ghost)
- [ ] Choose appropriate `size` (sm, md, lg)
- [ ] Set `fullWidth="true"` for mobile buttons
- [ ] Ensure minimum 44x44px touch target
- [ ] Test on mobile (should be tappable)
- [ ] Test on desktop (should look good)
- [ ] Verify focus state is visible

### When Using responsive-modal
- [ ] Set unique `id` for each modal
- [ ] Add descriptive `title`
- [ ] Choose appropriate `size` (sm, md, lg, xl, 2xl)
- [ ] Test on mobile (should be full-screen)
- [ ] Test on desktop (should be centered)
- [ ] Verify close button works
- [ ] Test keyboard escape key
- [ ] Verify backdrop click closes modal

### When Using responsive-grid
- [ ] Set `:cols="1"` for single column
- [ ] Set `:cols="2"` for two columns
- [ ] Set `:cols="3"` for three columns
- [ ] Set `:cols="4"` for four columns
- [ ] Set `:cols="6"` for six columns
- [ ] Choose appropriate `gap` (2, 3, 4, 6, 8)
- [ ] Test on mobile (should be 1 column)
- [ ] Test on tablet (should be 2 columns)
- [ ] Test on desktop (should be full columns)

### When Using responsive-card
- [ ] Add descriptive `title`
- [ ] Add optional `icon`
- [ ] Set `:hoverable="true"` for hover effect
- [ ] Customize `padding` if needed
- [ ] Test on mobile (should be readable)
- [ ] Test on desktop (should look good)
- [ ] Verify content is not cut off

### When Using responsive-container
- [ ] Choose appropriate `size` (sm, md, lg, xl, full)
- [ ] Verify content is centered
- [ ] Test on mobile (should have padding)
- [ ] Test on desktop (should be max-width)
- [ ] Verify no horizontal scrolling

---

## CSS Utility Checklist

### Responsive Padding
- [ ] Use `.p-responsive` for all-around padding
- [ ] Use `.px-responsive` for horizontal padding
- [ ] Use `.py-responsive` for vertical padding
- [ ] Test on mobile (should be smaller)
- [ ] Test on desktop (should be larger)

### Responsive Text
- [ ] Use `.text-responsive-*` for headings
- [ ] Use `.text-responsive-base` for body text
- [ ] Test on mobile (should be readable)
- [ ] Test on desktop (should be properly sized)
- [ ] Verify line-height is appropriate

### Responsive Grids
- [ ] Use `.grid-responsive-2` for 2-column grid
- [ ] Use `.grid-responsive-3` for 3-column grid
- [ ] Use `.grid-responsive-4` for 4-column grid
- [ ] Test on mobile (should be 1 column)
- [ ] Test on tablet (should be 2 columns)
- [ ] Test on desktop (should be full columns)

### Responsive Buttons
- [ ] Use `.btn-responsive` for standard buttons
- [ ] Use `.btn-responsive-lg` for large buttons
- [ ] Use `.touch-target` for any interactive element
- [ ] Verify minimum 44x44px size
- [ ] Test on mobile (should be tappable)
- [ ] Test on desktop (should look good)

### Responsive Forms
- [ ] Use `.form-input-responsive` for inputs
- [ ] Use `.form-label-responsive` for labels
- [ ] Test on mobile (should be easy to fill)
- [ ] Test on desktop (should look good)
- [ ] Verify text size prevents iOS zoom

### Responsive Cards
- [ ] Use `.card-responsive` for cards
- [ ] Test on mobile (should be readable)
- [ ] Test on desktop (should look good)
- [ ] Verify hover effect works

### Responsive Containers
- [ ] Use `.container-responsive` for containers
- [ ] Test on mobile (should have padding)
- [ ] Test on desktop (should be max-width)
- [ ] Verify no horizontal scrolling

---

## Page Implementation Checklist

### Dashboard Pages
- [ ] Update metric tiles to use `.grid-responsive-4`
- [ ] Update all buttons to use `.btn-responsive`
- [ ] Update modals to use `<x-responsive-modal>`
- [ ] Test on mobile (375px)
- [ ] Test on tablet (768px)
- [ ] Test on desktop (1024px+)
- [ ] Verify no horizontal scrolling
- [ ] Verify all content is readable

### Table Pages
- [ ] Replace tables with `<x-responsive-table>`
- [ ] Update filter buttons to use `.btn-responsive`
- [ ] Update search inputs to use `.form-input-responsive`
- [ ] Test on mobile (should show as cards)
- [ ] Test on desktop (should show as table)
- [ ] Verify sorting works on mobile
- [ ] Verify filtering works on mobile

### Form Pages
- [ ] Wrap forms in `<x-responsive-form>`
- [ ] Update all inputs to use `.form-input-responsive`
- [ ] Update all labels to use `.form-label-responsive`
- [ ] Stack buttons vertically on mobile
- [ ] Test on mobile (should be easy to fill)
- [ ] Test on desktop (should look good)
- [ ] Verify form submission works

### Modal Pages
- [ ] Replace all modals with `<x-responsive-modal>`
- [ ] Test on mobile (should be full-screen)
- [ ] Test on desktop (should be centered)
- [ ] Verify close button works
- [ ] Verify keyboard escape works
- [ ] Verify backdrop click works

---

## Testing Checklist

### Device Testing
- [ ] iPhone SE (375px)
- [ ] iPhone 12/13 (390px)
- [ ] iPhone 14 Pro Max (430px)
- [ ] Samsung Galaxy S21 (360px)
- [ ] Samsung Galaxy S21 Ultra (515px)
- [ ] iPad (768px)
- [ ] iPad Pro (1024px)
- [ ] Desktop (1920px)

### Interaction Testing
- [ ] Tap targets are at least 44x44px
- [ ] Forms are easy to fill on mobile
- [ ] Navigation is accessible
- [ ] Modals don't overflow
- [ ] Tables are readable
- [ ] Images load properly
- [ ] No horizontal scrolling
- [ ] Smooth scrolling
- [ ] Touch responses are immediate

### Visual Testing
- [ ] Text is readable on mobile
- [ ] Images scale properly
- [ ] Spacing is appropriate
- [ ] Colors are visible
- [ ] Buttons are visible
- [ ] Forms are visible
- [ ] Tables are visible
- [ ] Modals are visible

### Performance Testing
- [ ] Page load time < 3s on 4G
- [ ] Smooth scrolling
- [ ] No layout shifts
- [ ] Animations are smooth
- [ ] Touch responses are immediate
- [ ] No console errors
- [ ] No console warnings

### Accessibility Testing
- [ ] Keyboard navigation works
- [ ] Tab order is logical
- [ ] Focus states are visible
- [ ] Color contrast is sufficient
- [ ] ARIA labels are present
- [ ] Screen reader works
- [ ] Touch targets are adequate

### Browser Testing
- [ ] iOS Safari 12+
- [ ] Chrome Android 90+
- [ ] Samsung Internet 14+
- [ ] Firefox Android 88+
- [ ] Chrome Desktop
- [ ] Firefox Desktop
- [ ] Safari Desktop
- [ ] Edge Desktop

---

## Code Quality Checklist

### HTML/Blade
- [ ] Use semantic HTML
- [ ] Use proper heading hierarchy
- [ ] Use ARIA labels where needed
- [ ] Use alt text for images
- [ ] Use proper form labels
- [ ] Use proper button types
- [ ] No inline styles
- [ ] No hardcoded colors

### CSS
- [ ] Use Tailwind utilities
- [ ] Use responsive classes
- [ ] No custom CSS unless necessary
- [ ] Follow mobile-first approach
- [ ] Use consistent spacing
- [ ] Use consistent colors
- [ ] No !important flags
- [ ] No unused classes

### JavaScript
- [ ] Use Alpine.js for interactivity
- [ ] Minimize JavaScript
- [ ] No console errors
- [ ] No console warnings
- [ ] Proper error handling
- [ ] Proper event handling
- [ ] No memory leaks
- [ ] Proper cleanup

---

## Documentation Checklist

### Code Comments
- [ ] Add comments for complex logic
- [ ] Add comments for non-obvious code
- [ ] Add comments for workarounds
- [ ] Add comments for browser-specific code
- [ ] Keep comments up-to-date

### Component Documentation
- [ ] Document component props
- [ ] Document component usage
- [ ] Document component examples
- [ ] Document component limitations
- [ ] Document component accessibility

### Page Documentation
- [ ] Document page purpose
- [ ] Document page structure
- [ ] Document page dependencies
- [ ] Document page accessibility
- [ ] Document page performance

---

## Deployment Checklist

### Pre-Deployment
- [ ] All tests pass
- [ ] No console errors
- [ ] No console warnings
- [ ] Performance is acceptable
- [ ] Accessibility is acceptable
- [ ] Code is reviewed
- [ ] Documentation is complete

### Deployment
- [ ] Build assets (npm run build)
- [ ] Clear cache (php artisan cache:clear)
- [ ] Run migrations if needed
- [ ] Deploy to staging
- [ ] Test on staging
- [ ] Deploy to production
- [ ] Monitor for errors

### Post-Deployment
- [ ] Monitor error logs
- [ ] Monitor performance
- [ ] Monitor user feedback
- [ ] Monitor analytics
- [ ] Be ready to rollback
- [ ] Document any issues
- [ ] Plan improvements

---

## Common Mistakes to Avoid

### ❌ Don't
- [ ] Don't use fixed widths (use responsive classes)
- [ ] Don't use hardcoded breakpoints (use Tailwind breakpoints)
- [ ] Don't use inline styles (use Tailwind utilities)
- [ ] Don't forget touch targets (minimum 44x44px)
- [ ] Don't forget accessibility (ARIA labels, keyboard nav)
- [ ] Don't forget testing (test on real devices)
- [ ] Don't forget performance (optimize images, minimize JS)
- [ ] Don't forget documentation (document your code)

### ✅ Do
- [ ] Do use responsive classes
- [ ] Do use Tailwind breakpoints
- [ ] Do use Tailwind utilities
- [ ] Do ensure 44x44px touch targets
- [ ] Do add accessibility features
- [ ] Do test on real devices
- [ ] Do optimize performance
- [ ] Do document your code

---

## Quick Reference

### Responsive Breakpoints
```
Mobile:  < 640px
Tablet:  640px - 1024px
Desktop: 1024px+
```

### Touch Target Size
```
Minimum: 44x44px (Apple & Google standard)
Recommended: 48x48px
```

### Component Usage
```blade
<x-responsive-table :headers="$h" :rows="$r" />
<x-responsive-form :columns="2">...</x-responsive-form>
<x-responsive-button variant="primary">Click</x-responsive-button>
<x-responsive-modal title="Title">...</x-responsive-modal>
<x-responsive-grid :cols="3">...</x-responsive-grid>
<x-responsive-card title="Title">...</x-responsive-card>
<x-responsive-container size="lg">...</x-responsive-container>
```

### CSS Utilities
```html
.p-responsive          <!-- Responsive padding -->
.text-responsive-3xl   <!-- Responsive text -->
.grid-responsive-3     <!-- Responsive grid -->
.btn-responsive        <!-- Responsive button -->
.form-input-responsive <!-- Responsive input -->
.card-responsive       <!-- Responsive card -->
.container-responsive  <!-- Responsive container -->
```

---

## Resources

- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Mobile-First CSS](https://www.mobileapproaches.com/)
- [Touch Target Sizing](https://www.nngroup.com/articles/touch-target-size/)
- [Web Accessibility](https://www.w3.org/WAI/WCAG21/quickref/)
- [Chrome DevTools](https://developer.chrome.com/docs/devtools/)

---

## Support

For questions or issues:

1. Check the documentation files
2. Review the component examples
3. Test with Chrome DevTools
4. Check browser console
5. Test on real devices

---

## Sign-Off

- [ ] I have read all documentation
- [ ] I understand the components
- [ ] I understand the utilities
- [ ] I understand the testing requirements
- [ ] I am ready to implement
- [ ] I will follow the checklist
- [ ] I will test thoroughly
- [ ] I will document my work

**Developer Name:** ________________
**Date:** ________________
**Project:** NSRC AMS
**Version:** 1.0

---

**Last Updated:** May 18, 2026
**Status:** Ready for Implementation
