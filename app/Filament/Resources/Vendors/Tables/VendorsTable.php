<?php

namespace App\Filament\Resources\Vendors\Tables;

use App\Actions\Vendors\ApproveVendor;
use App\Actions\Vendors\SuspendVendor;
use App\Models\Vendor;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('store_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Vendor::STATUS_APPROVED => 'success',
                        Vendor::STATUS_SUSPENDED => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),

                TextColumn::make('commission_rate')
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('approved_at')
                    ->dateTime()
                    ->placeholder('Not approved'),

                TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Vendor::STATUS_PENDING => 'Pending',
                        Vendor::STATUS_APPROVED => 'Approved',
                        Vendor::STATUS_SUSPENDED => 'Suspended',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->visible(fn (Vendor $record) => $record->status !== Vendor::STATUS_APPROVED)
                    ->requiresConfirmation()
                    ->action(function (Vendor $record) {
                        app(ApproveVendor::class)->handle($record);

                        Notification::make()
                            ->title("{$record->store_name} approved")
                            ->success()
                            ->send();
                    }),

                Action::make('suspend')
                    ->label('Suspend')
                    ->color('danger')
                    ->visible(fn (Vendor $record) => $record->status === Vendor::STATUS_APPROVED)
                    ->requiresConfirmation()
                    ->action(function (Vendor $record) {
                        app(SuspendVendor::class)->handle($record);

                        Notification::make()
                            ->title("{$record->store_name} suspended")
                            ->warning()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
