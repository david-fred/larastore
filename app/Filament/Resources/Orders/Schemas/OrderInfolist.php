<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order')
                ->columns(3)
                ->schema([
                    TextEntry::make('reference'),

                    TextEntry::make('status')
                        ->badge(),

                    TextEntry::make('total')
                        ->money('USD'),

                    TextEntry::make('user.name')
                        ->label('Customer'),

                    TextEntry::make('user.email')
                        ->label('Email'),

                    TextEntry::make('refunded_amount')
                        ->label('Refunded')
                        ->money('USD'),

                    TextEntry::make('paid_at')
                        ->dateTime()
                        ->placeholder('Not paid'),

                    TextEntry::make('shipped_at')
                        ->dateTime()
                        ->placeholder('Not shipped'),

                    TextEntry::make('created_at')
                        ->label('Placed')
                        ->dateTime(),
                ]),

            Section::make('Items')
                ->schema([
                    RepeatableEntry::make('items')
                        ->schema([
                            TextEntry::make('title'),
                            TextEntry::make('variant_label')
                                ->label('Variant')
                                ->placeholder('—'),
                            TextEntry::make('quantity'),
                            TextEntry::make('price')
                                ->money('USD'),
                        ])
                        ->columns(4),
                ]),

            Section::make('Refunds')
                ->schema([
                    RepeatableEntry::make('refunds')
                        ->schema([
                            TextEntry::make('amount')
                                ->money('USD'),
                            TextEntry::make('status')
                                ->badge(),
                            TextEntry::make('reason'),
                            TextEntry::make('created_at')
                                ->label('Issued')
                                ->dateTime(),
                        ])
                        ->columns(4),
                ]),

            Section::make('Stripe')
                ->columns(2)
                ->schema([
                    TextEntry::make('stripe_payment_intent_id')
                        ->label('Payment intent')
                        ->placeholder('Missing'),

                    TextEntry::make('stripe_session_id')
                        ->label('Checkout session')
                        ->placeholder('—'),
                ]),
        ]);
    }
}
