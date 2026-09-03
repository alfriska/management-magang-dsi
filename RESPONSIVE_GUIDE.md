# Responsive Design Guide

This project has been updated to be fully responsive.
The main layout (`Sidebar` and `Main Content`) now adapts automatically to mobile devices (sidebar becomes an off-canvas drawer).

## New Utility Classes

We have added Bootstrap-like utility classes to `resources/css/app.css` to make it easier to build responsive layouts if you prefer this style over raw Tailwind utilities.

### The Grid System

Use `.row` and `.col-*` classes to create responsive grids.

```html
<div class="row">
    <!-- Full width on mobile, half width on medium screens up -->
    <div class="col-12 col-md-6">
        <div class="card">Left Column</div>
    </div>

    <!-- Full width on mobile, half width on medium screens up -->
    <div class="col-12 col-md-6">
        <div class="card">Right Column</div>
    </div>
</div>
```

### Available Column Classes

-   `.col-12` (100% width)
-   `.col-6` (50% width)
-   `.col-4` (33.3% width)
-   `.col-3` (25% width)

**Responsive Variants:**

-   `.col-sm-*` (Tablets, >= 640px)
-   `.col-md-*` (Laptops, >= 768px)
-   `.col-lg-*` (Desktops, >= 1024px)

### Native CSS Components

We have also ensured components like `.stat-grid` use responsive grid layouts:

```css
.stat-grid {
    @apply grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4;
}
```

## Responsive Tables

Wrap your tables in `.table-container` to ensure they scroll horizontally on small screens instead of breaking the layout.

```html
<div class="table-container">
    <table>
        ...
    </table>
</div>
```

## Helper Classes

-   `.w-full-mobile`: Forces 100% width on all screens (useful for overriding).
-   `.menu-toggle`: Visible only on mobile/tablet to toggle the sidebar.
