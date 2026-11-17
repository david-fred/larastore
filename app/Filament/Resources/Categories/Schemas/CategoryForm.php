<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required()
                ->searchable()
                ->preload(),
                TextInput::make('name')
                    ->required()
                ->maxLength(255),
                TextInput::make('slug')
                    ->unique(ignoreRecord: true)
                ->helperText('Leave empty to generate from the name.'),
                Toggle::make('active')
                    ->default(true),
            ]);
    }
}
