<?php

namespace MobileCards\FilamentMobileTable;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Table;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMobileTableServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-mobile-table';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('mobile-cards-styles', __DIR__.'/../resources/css/mobile-cards.css'),
            Js::make('mobile-cards-scripts', __DIR__.'/../resources/js/mobile-cards.js'),
        ], package: 'mobile-cards/filament-mobile-table');

        Table::macro('mobileCards', function (array $options = []) {
            $layout = $options['layout'] ?? 'default';
            $columns = $options['columns'] ?? 2;

            $currentAttrs = $this->getExtraAttributes();

            $this->extraAttributes(array_merge($currentAttrs, [
                'class' => "fi-mobile-card-table fi-mobile-layout-{$layout}",
                'data-mobile-columns' => $columns,
            ]));

            return $this;
        });

        Table::macro('mobileCardFeatured', function (string $column, ?string $color = 'blue') {
            $currentAttrs = $this->getExtraAttributes();

            $this->extraAttributes(array_merge($currentAttrs, [
                'data-featured-column' => $column,
                'data-featured-color' => $color,
            ]));

            return $this;
        });

        Table::macro('mobileCardBadges', function (array $fields, array $colors = []) {
            $currentAttrs = $this->getExtraAttributes();

            $this->extraAttributes(array_merge($currentAttrs, [
                'data-badge-fields' => implode('|', $fields),
                'data-badge-colors' => collect($colors)->map(fn ($v, $k) => "{$k}:{$v}")->implode('|'),
            ]));

            return $this;
        });
    }
}
