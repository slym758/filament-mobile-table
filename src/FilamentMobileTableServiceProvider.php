<?php

namespace MobileCards\FilamentMobileTable;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Table;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMobileTableServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-mobile-table';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews()
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('mobile-cards-styles', __DIR__.'/../resources/css/mobile-cards.css'),
        ], 'mobile-cards/filament-mobile-table');

        Table::macro('mobileCards', function (array $options = []) {
            $layout = $options['layout'] ?? 'default';
            $columns = $options['columns'] ?? 2;

            $attributes = [
                'class' => "fi-mobile-card-table fi-mobile-layout-{$layout}",
                'data-mobile-columns' => $columns,
                'x-init' => "
                    \$nextTick(() => {
                        const table = \$el;
                        const headers = Array.from(table.querySelectorAll('thead th'));
                        const rows = table.querySelectorAll('tbody tr');
                        const featuredColumn = table.getAttribute('data-featured-column');

                        rows.forEach(row => {
                            const cells = Array.from(row.querySelectorAll('td'));

                            cells.forEach((cell, cellIndex) => {
                                if (cellIndex < headers.length) {
                                    const headerText = headers[cellIndex].textContent.trim();
                                    cell.setAttribute('data-label', headerText);

                                    if (featuredColumn && headerText === featuredColumn) {
                                        cell.setAttribute('data-featured', 'true');

                                        const color = table.getAttribute('data-featured-color');
                                        if (color) {
                                            cell.setAttribute('data-featured-color', color);
                                        }
                                    }
                                }
                            });
                        });
                    });
                "
            ];

            // v3 ve v4 uyumluluğu
            if (method_exists($this, 'extraAttributes')) {
                // Filament v4
                $this->extraAttributes($attributes);
            } else {
                // Filament v3
                $this->extraTableAttributes($attributes);
            }

            return $this;
        });

        Table::macro('mobileCardFeatured', function (string $column, ?string $color = 'blue') {
            // v3 ve v4 uyumluluğu
            if (method_exists($this, 'extraAttributes')) {
                $currentAttrs = $this->getExtraAttributes();
            } else {
                $currentAttrs = $this->getExtraTableAttributes();
            }

            $currentXInit = $currentAttrs['x-init'] ?? '';

            $newAttrs = array_merge($currentAttrs, [
                'data-featured-column' => $column,
                'data-featured-color' => $color,
                'x-init' => $currentXInit,
            ]);

            if (method_exists($this, 'extraAttributes')) {
                $this->extraAttributes($newAttrs);
            } else {
                $this->extraTableAttributes($newAttrs);
            }

            return $this;
        });
    }
}
