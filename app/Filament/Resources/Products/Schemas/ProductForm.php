<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
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
                FileUpload::make('images')
                    ->label('Product images')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->maxFiles(6)
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->columnSpanFull(),
                Repeater::make('variation_types')
                    ->label('Variation types (e.g. Color, Size)')
                    ->schema([
                        Hidden::make('id'),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

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
