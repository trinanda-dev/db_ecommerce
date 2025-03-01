<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvitationCodeResource\Pages;
use App\Filament\Resources\InvitationCodeResource\RelationManagers;
use App\Models\InvitationCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webbingbrasil\FilamentCopyActions\Tables\CopyableTextColumn;

class InvitationCodeResource extends Resource
{
    protected static ?string $model = InvitationCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Kode Undangan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode Undangan')
                    ->required()
                    ->unique()
                    ->maxLength(50),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif'),
                Forms\Components\Toggle::make('is_used')
                    ->label('Digunakan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            CopyableTextColumn::make('code') // Mengganti TextColumn dengan CopyableTextColumn
                ->label('Kode Undangan')
                ->searchable(),
            Tables\Columns\ToggleColumn::make('is_active')
                ->label('Aktif'),
            Tables\Columns\ToggleColumn::make('is_used')
                ->label('Digunakan'),
        ])
        ->headerActions([
            Tables\Actions\Action::make('generateCode')
                ->label('Generate Code')
                ->icon('heroicon-o-plus')
                ->action(function () {
                    do {
                        $code = InvitationCode::generateCode();
                    } while (InvitationCode::where('code', $code)->exists());

                    InvitationCode::create([
                        'code' => $code,
                        'is_active' => true,
                    ]);

                    Notification::make()
                        ->title('Kode undangan berhasil dibuat')
                        ->body('Kode undangan: ' . $code)
                        ->success()
                        ->send();
                }),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvitationCodes::route('/'),
            'create' => Pages\CreateInvitationCode::route('/create'),
            'edit' => Pages\EditInvitationCode::route('/{record}/edit'),
        ];
    }
}
