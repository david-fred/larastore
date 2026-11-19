<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Actions\Orders\MarkOrderShipped;
use App\Actions\Orders\RefundOrder;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Validation\ValidationException;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markShipped')
                ->label('Mark as shipped')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Order $record) => in_array($record->status, ['paid', 'partially_refunded'], true)
                    && $record->shipped_at === null)
                ->action(function (Order $record) {
                    try {
                        app(MarkOrderShipped::class)->handle($record);

                        Notification::make()
                            ->title('Marked as shipped')
                            ->success()
                            ->send();
                    } catch (ValidationException $e) {
                        Notification::make()
                            ->title(collect($e->errors())->flatten()->first())
                            ->danger()
                            ->send();
                    }

                    $this->record->refresh();
                }),

            Action::make('refund')
                ->label('Refund')
                ->color('danger')
                ->visible(fn (Order $record) => $record->canBeRefunded())
                ->schema([
                    TextInput::make('amount')
                        ->label('Amount')
                        ->numeric()
                        ->prefix('$')
                        ->required()
                        ->default(fn (Order $record) => $record->refundableAmount()),

                    Textarea::make('reason')
                        ->label('Reason (internal)')
                        ->required()
                        ->maxLength(500),
                ])
                ->requiresConfirmation()
                ->action(function (Order $record, array $data) {
                    try {
                        app(RefundOrder::class)->handle(
                            $record,
                            (float) $data['amount'],
                            $data['reason'],
                            auth()->user(),
                        );

                        Notification::make()
                            ->title('Refund issued')
                            ->success()
                            ->send();
                    } catch (ValidationException $e) {
                        Notification::make()
                            ->title(collect($e->errors())->flatten()->first())
                            ->danger()
                            ->send();
                    }

                    $this->record->refresh();
                }),
        ];
    }
}
