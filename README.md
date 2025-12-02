# Filament Mobile Table Cards

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mobile-cards/filament-mobile-table.svg?style=flat-square)](https://packagist.org/packages/mobile-cards/filament-mobile-table)
[![Total Downloads](https://img.shields.io/packagist/dt/mobile-cards/filament-mobile-table.svg?style=flat-square)](https://packagist.org/packages/mobile-cards/filament-mobile-table)

Transform your Filament tables into beautiful, responsive card layouts on mobile devices. Automatically converts table rows to cards with featured fields, custom colors, and multiple layout options.

## Features

- 🎨 **Featured Fields** - Highlight important data with gradient colors (20+ Tailwind colors)
- 🎭 **Multiple Layouts** - Default, Compact, and Minimal card styles
- 🌈 **Full Color Palette** - Supports all Tailwind CSS colors
- 📱 **Responsive Grid** - Configurable 1-3 column layouts for tablets
- 🌓 **Dark Mode** - Full dark mode support out of the box
- ⚡ **Zero Config** - Works immediately with one method call
- 🔧 **Filament v3 & v4** - Compatible with both major versions
- ♿ **Accessible** - Maintains all Filament table features (sorting, actions, bulk actions)

## Screenshots

_Desktop View (Normal Table)_
![Desktop](screenshots/desktop.png)

_Mobile View (Card Layout)_
![Mobile](screenshots/mobile.png)

_Featured Field with Custom Color_
![Featured](screenshots/featured.png)

## Requirements

- PHP 8.1 or higher
- Laravel 10.x or 11.x
- Filament 3.x or 4.x

## Installation

Install the package via composer:
```bash
composer require mobile-cards/filament-mobile-table
```

### Automatic Setup

The package will automatically register itself via Laravel's package discovery.

### Manual Setup (Optional)

If auto-discovery is disabled, add the service provider to `config/app.php`:
```php
'providers' => [
    // ...
    MobileCards\FilamentMobileTable\FilamentMobileTableServiceProvider::class,
],
```

### Admin Panel Provider (Not Required)

**No additional configuration needed!** The plugin automatically registers its assets. However, if you want to manually register assets in your `AdminPanelProvider`, you can:
```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... other config
        ->plugins([
            // Plugin automatically loads, no registration needed
        ]);
}
```

## Usage

### Basic Implementation

The simplest way to enable mobile cards:
```php
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

public static function table(Table $table): Table
{
    return $table
        ->mobileCards()  // Enable mobile cards
        ->columns([
            TextColumn::make('name'),
            TextColumn::make('email'),
            TextColumn::make('status'),
        ]);
}
```

That's it! Your table will now display as cards on mobile devices (screens < 1024px).

### Featured Field

Highlight an important field with a colored gradient background:
```php
public static function table(Table $table): Table
{
    return $table
        ->mobileCards()
        ->mobileCardFeatured('price', 'emerald')  // Column name, color
        ->columns([
            TextColumn::make('product_name'),
            TextColumn::make('price')->money('TRY'),
            TextColumn::make('stock'),
        ]);
}
```

**Default color:** `blue` (if no color specified)

### Available Colors

All Tailwind CSS colors are supported:

`red`, `orange`, `amber`, `yellow`, `lime`, `green`, `emerald`, `teal`, `cyan`, `sky`, `blue`, `indigo`, `violet`, `purple`, `fuchsia`, `pink`, `rose`, `slate`, `gray`, `zinc`, `neutral`, `stone`

### Layout Options

Choose from 3 different card layouts:
```php
public static function table(Table $table): Table
{
    return $table
        ->mobileCards([
            'layout' => 'compact',  // default, compact, minimal
            'columns' => 2,         // Tablet columns: 1, 2, or 3
        ])
        ->columns([...]);
}
```

**Layout Types:**

1. **default** - Standard card with labels and values side-by-side
2. **compact** - Smaller padding and font sizes for more content density
3. **minimal** - Clean layout without field labels

### Tablet Column Grid

Control how many columns appear on tablet devices (640px - 1024px):
```php
->mobileCards([
    'columns' => 1,  // Single column (default: 2)
])

->mobileCards([
    'columns' => 3,  // Three columns for larger tablets
])
```

### Complete Example
```php
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

public static function table(Table $table): Table
{
    return $table
        ->mobileCards([
            'layout' => 'default',
            'columns' => 2,
        ])
        ->mobileCardFeatured('total_price', 'green')
        ->columns([
            TextColumn::make('order_number')
                ->label('Order #')
                ->searchable(),
                
            TextColumn::make('customer.name')
                ->label('Customer'),
                
            TextColumn::make('total_price')
                ->money('TRY')
                ->sortable(),
                
            BadgeColumn::make('status')
                ->colors([
                    'success' => 'completed',
                    'warning' => 'pending',
                    'danger' => 'cancelled',
                ]),
                
            TextColumn::make('created_at')
                ->dateTime('d/m/Y H:i'),
        ])
        ->filters([...])
        ->actions([...])
        ->bulkActions([...]);
}
```

## How It Works

### Responsive Behavior

- **Desktop (≥ 1024px):** Normal Filament table
- **Tablet (640px - 1023px):** Card grid (configurable columns)
- **Mobile (< 640px):** Single column card layout

### Feature Preservation

All Filament table features work in card mode:
- ✅ Sorting
- ✅ Searching
- ✅ Filtering
- ✅ Actions (View, Edit, Delete)
- ✅ Bulk Actions
- ✅ Pagination
- ✅ Record selection

### CSS Classes

The plugin adds these classes for custom styling:
```css
.fi-mobile-card-table           /* Main table wrapper */
.fi-mobile-layout-{name}        /* Layout modifier */
[data-featured="true"]          /* Featured field */
[data-featured-color="{color}"] /* Color attribute */
```

## Customization

### Override Styles

Create a custom CSS file and register it in your panel provider:
```php
use Filament\Support\Assets\Css;

public function panel(Panel $panel): Panel
{
    return $panel
        ->assets([
            Css::make('custom-mobile-cards', resource_path('css/custom-mobile-cards.css')),
        ]);
}
```

Example custom CSS:
```css
@media (max-width: 1024px) {
    .fi-mobile-card-table tr {
        border-radius: 16px !important; /* More rounded corners */
    }
    
    .fi-mobile-card-table td[data-featured="true"] {
        padding: 2rem !important; /* More padding on featured */
    }
}
```

## Troubleshooting

### Cards not showing on mobile

1. Clear your browser cache
2. Run: `php artisan filament:cache-components`
3. Check browser console for JavaScript errors

### Styles not applying

1. Clear Laravel cache: `php artisan cache:clear`
2. Clear view cache: `php artisan view:clear`
3. Rebuild assets: `npm run build`

### Featured field not highlighted

Check that the column name matches exactly (case-sensitive):
```php
->mobileCardFeatured('price')  // Column must be named 'price'
->columns([
    TextColumn::make('price'),  // ✅ Matches
])
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security issues, please email security@example.com.

## Credits

- [Süleyman Ardıç](https://github.com/slym758)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
