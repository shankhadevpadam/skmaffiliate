<?php

namespace App\Filament\Resources\Campaigns\Tables;

use App\Jobs\SendCampaignJob;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('template.name')
                    ->label('Template')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'danger',
                        'sent' => 'success',
                        'sending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('send')
                    ->label('Send Campaign')
                    ->icon(Heroicon::PaperAirplane)
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Send Campaign')
                    ->modalDescription('Are you sure you want to send this campaign to all subscribers? This action will send emails to all subscribers belonging to this campaign\'s user.')
                    ->modalSubmitActionLabel('Send')
                    ->action(function ($record) {
                        // Dispatch the job to send the campaign
                        SendCampaignJob::dispatch($record);

                        // Update campaign status to sending
                        $record->update(['status' => 'sending']);

                        // Show success notification
                        Notification::make()
                            ->title('Campaign queued for sending')
                            ->body('The campaign is being sent to all subscribers.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status !== 'sent'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
