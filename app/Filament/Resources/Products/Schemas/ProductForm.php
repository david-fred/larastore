<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->required()
                ->maxLength(255),
                TextInput::make('slug')
                    ->unique(ignoreRecord: true)
                    ->helperText('Leave empty to generate from the title.'),
                   // ->required(),
                Textarea::make('description')
                    ->rows(5)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('$'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                Toggle::make('active')
                ->default(true),
                   // ->required(),
                Repeater::make('gallery')
                    ->label('Product images')
                    ->schema([
                        FileUpload::make('path')
                            ->label('Image')
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(4096)
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public'),

                        Select::make('option_key')
                            ->label('Show for')
                            ->placeholder('All variants')
                            ->options(function (Get $get) {
                                $groups = [];

                                foreach ($get('/variation_types') ?? [] as $type) {
                                    if (empty($type['affects_images']) || blank($type['name'] ?? null)) {
                                        continue;
                                    }

                                    $options = [];

                                    foreach ($type['options'] ?? [] as $option) {
                                        if (blank($option['name'] ?? null)) {
                                            continue;
                                        }

                                        $name = trim($option['name']);

                                        // Saved options use their ID, new ones use their name until the next save
                                        $key = ! empty($option['id'])
                                            ? 'id:' . $option['id']
                                            : 'new:' . $name;

                                        $options[$key] = $name;
                                    }

                                    if ($options) {
                                        $groups[trim($type['name'])] = $options;
                                    }
                                }

                                return $groups;
                            }),
                    ])
                    ->columns(2)
                    ->reorderable()
                    ->maxItems(10)
                    ->defaultItems(0)
                    ->addActionLabel('Add image')
                    ->columnSpanFull(),
                Repeater::make('variation_types')
                    ->label('Variation types (e.g. Color, Size)')
                    ->schema([
                        Hidden::make('id'),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),
                        Toggle::make('affects_images')
                            ->label('Images change per option')
                            ->helperText('Turn on for colors. Leave off for storage, sizes, and other options that look the same.')
                            ->default(false)
                            ->live(),

                        Repeater::make('options')
                            ->label('Options')
                            ->schema([
                                Hidden::make('id'),

                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Add option')
                            ->columns(2),
                    ])
                    ->defaultItems(0)
                    ->collapsible()
                    ->addActionLabel('Add variation type')
                    ->columnSpanFull(),
            ]);
    }
}
