# Changelog

All notable changes to `filament-mobile-table` will be documented in this file.

## 1.0.1 - 2024-12-03

### Changed
- Now requires Filament v4.x only
- Removed Filament v3 support for better maintainability

### Fixed
- Compatibility issues with method calls

## 1.0.0 - 2024-12-03

### Added
- Initial release
- Mobile card view for Filament tables
- Featured field support with gradient colors
- 20+ Tailwind color options (red, orange, amber, yellow, lime, green, emerald, teal, cyan, sky, blue, indigo, violet, purple, fuchsia, pink, rose, slate, gray, zinc, neutral, stone)
- Three layout options: default, compact, minimal
- Responsive tablet grid (1-3 columns)
- Full dark mode support
- Automatic responsive behavior (< 1024px)
- Preserves all table features (sorting, actions, bulk actions, pagination)
- Hover animations on cards
- Smooth transitions

### Features
- `->mobileCards()` - Enable mobile card view
- `->mobileCards(['layout' => 'compact', 'columns' => 2])` - Configure layout and columns
- `->mobileCardFeatured('column', 'color')` - Highlight featured field
