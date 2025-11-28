<?php

namespace App\Filament\Resources\Users\Tables;

use App\Services\WooCommerceService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('affiliate_tag')->label('Affiliate Tag')->searchable()->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                static::createCouponAction(),
                Action::make('delete_coupon')
                    ->label('Delete Coupon')
                    ->icon('heroicon-o-ticket')
                    ->color('danger')
                    ->action(function (array $data, $record) {
                        $wooCommerceService = new WooCommerceService();

                        $wooCommerceService->client->delete('coupons/' . $record->coupon_id);
                        $record->coupon_id = null;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->successNotificationTitle('Coupon deleted successfully.')
                    ->visible(fn($record) => $record->coupon_id),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    protected static function createCouponAction(): Action
    {
        return Action::make('createCoupon')
            ->label('Create Coupon')
            ->icon('heroicon-o-ticket')
            ->color('info')
            ->schema([
                Section::make('Coupon Details')
                    ->description('Create a WooCommerce coupon for this user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                /* TextInput::make('code')
                                    ->label('Coupon Code')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('SUMMER2024')
                                    ->helperText('Unique coupon code')
                                    ->live(onBlur: true), */

                                Select::make('discount_type')
                                    ->label('Discount Type')
                                    ->required()
                                    ->options([
                                        'percent' => 'Percentage Discount',
                                        'fixed_cart' => 'Fixed Cart Discount',
                                        'fixed_product' => 'Fixed Product Discount',
                                    ])
                                    ->default('percent')
                                    ->live(),

                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('10')
                                    ->helperText('Discount amount (percentage or fixed amount)')
                                    ->live(onBlur: true),

                                TextInput::make('usage_limit')
                                    ->label('Usage Limit')
                                    ->numeric()
                                    ->minValue(1)
                                    ->placeholder('Leave empty for unlimited'),

                                DatePicker::make('date_expires')
                                    ->label('Expiry Date')
                                    ->native(false)
                                    ->displayFormat('Y-m-d')
                                    ->helperText('Leave empty for no expiry'),

                                Select::make('individual_use')
                                    ->label('Individual Use')
                                    ->options([
                                        true => 'Yes - Cannot be used with other coupons',
                                        false => 'No - Can be combined with other coupons',
                                    ])
                                    ->default(false),
                            ]),
                    ]),
            ])
            ->action(function (array $data, $record) {
                try {
                    $wooCommerceService = new WooCommerceService();

                    $couponData = [
                        'code' => $record->affiliate_tag,
                        'discount_type' => $data['discount_type'],
                        'amount' => (string) $data['amount'],
                        'individual_use' => $data['individual_use'] ?? false,
                        'usage_limit' => $data['usage_limit'] ?? null,
                        'date_expires' => $data['date_expires'] ?? null,
                        'email_restrictions' => [$record->email],
                    ];

                    // Remove null values
                    $couponData = array_filter($couponData, fn($value) => $value !== null);

                    $coupon = $wooCommerceService->client->post('coupons', $couponData);

                    $record->coupon_id = $coupon->id;

                    $record->save();

                    Notification::make()
                        ->title('Coupon Created Successfully')
                        ->body("Coupon '{$record->affiliate_tag}' has been created for {$record->name}")
                        ->success()
                        ->duration(5000)
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Coupon Creation Failed')
                        ->body($e->getMessage())
                        ->danger()
                        ->duration(8000)
                        ->send();

                    throw $e;
                }
            })
            ->modalWidth('2xl')
            ->modalHeading(fn($record) => "Create Coupon for {$record->name}")
            ->modalSubmitActionLabel('Create Coupon')
            ->modalIcon('heroicon-o-ticket')
            ->requiresConfirmation(false)
            ->closeModalByClickingAway(false)
            ->visible(fn($record) => !$record->coupon_id);
    }
}
