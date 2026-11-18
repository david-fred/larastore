<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $recordTitleAttribute = 'label';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('price')
                ->required()
                ->numeric()
                ->minValue(0)
                ->prefix('$'),

            TextInput::make('quantity')
                ->label('Stock')
                ->required()
                ->numeric()
                ->minValue(0),

            TextInput::make('sku')
                ->label('SKU')
                ->maxLength(100)
                ->unique(ignoreRecord: true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Variant'),

                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Stock')
                    ->sortable(),

                TextColumn::make('sku')
                    ->label('SKU'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
