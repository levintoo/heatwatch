<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlertResource\Pages;
use App\Models\Alert;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

class AlertResource extends Resource
{
    protected static ?string $model = Alert::class;

    protected static ?string $slug = 'alerts';

    protected static ?string $navigationIcon = 'hugeicons-alert-02';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('severity')
                    ->required(),

                TextInput::make('message')
                    ->required(),

                Select::make('worker_id')
                    ->relationship('worker', 'name')
                    ->searchable(),

                Select::make('site_id')
                    ->relationship('site', 'name')
                    ->searchable(),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Alert $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Alert $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('severity'),

                TextColumn::make('message'),

                TextColumn::make('worker.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('site.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
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
            'index' => Pages\ListAlerts::route('/'),
            'create' => Pages\CreateAlert::route('/create'),
            'edit' => Pages\EditAlert::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['worker', 'site', 'user']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['worker.name', 'site.name', 'user.name'];
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

        if ($record->user) {
            $details['User'] = $record->user->name;
        }

        return $details;
    }
}
