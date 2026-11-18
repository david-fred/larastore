<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Actions\Products\BulkUpdateVariants;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulkEdit')
                        ->label('Bulk edit')
                        ->schema([
                            TextInput::make('price')
                                ->label('Set price')
                                ->numeric()
                                ->minValue(0)
                                ->prefix('$')
                                ->helperText('Leave blank to keep each variant\'s price.'),

                            Select::make('stock_mode')
                                ->label('Stock')
                                ->options([
                                    'set' => 'Set to',
                                    'add' => 'Adjust by (use minus to subtract)',
                                ])
                                ->default('set'),

                            TextInput::make('stock')
                                ->numeric()
                                ->integer()
                                ->helperText('Leave blank to keep each variant\'s stock.'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $price = filled($data['price'] ?? null) ? (float) $data['price'] : null;
                            $stock = filled($data['stock'] ?? null) ? (int) $data['stock'] : null;

                            $updated = app(BulkUpdateVariants::class)->handle(
                                $records,
                                $price,
                                $data['stock_mode'] ?? 'set',
                                $stock,
                            );

                            Notification::make()
                                ->title($updated > 0 ? "Updated {$updated} variants" : 'No changes entered')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
