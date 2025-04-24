<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CheckInResource\Pages;
use App\Models\CheckIn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CheckInResource extends Resource
{
    protected static ?string $model = CheckIn::class;

    protected static ?string $slug = 'check-ins';

    protected static ?string $navigationIcon = 'hugeicons-contracts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('worker_id')
                    ->relationship('worker', 'name')
                    ->searchable()
                    ->required(),

                DateTimePicker::make('checkin'),

                DateTimePicker::make('checkout'),

                Select::make('site_id')
                    ->relationship('site', 'name')
                    ->searchable()
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?CheckIn $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?CheckIn $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('worker.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('checkin')
                    ->dateTime(),

                TextColumn::make('checkout')
                    ->dateTime(),

                TextColumn::make('site.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCheckIns::route('/'),
            'create' => Pages\CreateCheckIn::route('/create'),
            'edit' => Pages\EditCheckIn::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['worker', 'site']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['worker.name', 'site.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->worker) {
            $details['Worker'] = $record->worker->name;
        }

        if ($record->site) {
            $details['Site'] = $record->site->name;
        }

        return $details;
    }
}
