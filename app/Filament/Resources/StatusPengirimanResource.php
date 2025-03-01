<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatusPengirimanResource\Pages;
use App\Filament\Resources\StatusPengirimanResource\RelationManagers;
use App\Models\StatusPengiriman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StatusPengirimanResource extends Resource
{
    protected static ?string $model = StatusPengiriman::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'Status Pengiriman';
    protected static ?int $navigationSort = 6;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pesanan_id')
                    ->required()
                    ->numeric()
                    ->disabled(), // Agar admin tidak bisa mengubah pesanan_id secara langsung

                Forms\Components\Select::make('status')
                    ->label('Status Pengiriman')
                    ->options([
                        'Diproses' => 'Diproses',
                        'Dikirim' => 'Dikirim',
                        'Sampai di Gudang' => 'Sampai di Gudang',
                        'Dalam Pengantaran' => 'Dalam Pengantaran',
                        'Diterima' => 'Diterima',
                        'Gagal' => 'Gagal',
                    ])
                    ->default('Diproses')
                    ->required()
                    ->native(false) // Menggunakan dropdown yang lebih interaktif
                    ->searchable(),

                Forms\Components\Textarea::make('catatan')
                    ->label('Catatan Pengiriman')
                    ->columnSpanFull(),

                Forms\Components\DateTimePicker::make('tanggal_dikirim')
                    ->label('Tanggal Dikirim')
                    ->required(),

                Forms\Components\TextInput::make('nomor_resi')
                    ->label('Nomor Resi')
                    ->maxLength(255)
                    ->required()
                    ->default(null),

                Forms\Components\TextInput::make('ekspedisi')
                    ->label('Ekspedisi')
                    ->maxLength(255)
                    ->required()
                    ->default(null),

                Forms\Components\DateTimePicker::make('waktu_update')
                    ->label('Waktu Update')
                    ->default(now())
                    ->disabled(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pesanan_id')
                    ->label('ID Pesanan')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status Pengiriman')
                    ->badge() // ✅ Gunakan badge() sebagai pengganti BadgeColumn
                    ->color(fn ($state) => match ($state) {
                        'Diproses' => 'gray',
                        'Dikirim' => 'blue',
                        'Sampai di Gudang' => 'purple',
                        'Dalam Pengantaran' => 'yellow',
                        'Diterima' => 'green',
                        'Gagal' => 'red',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_dikirim')
                    ->label('Tanggal Dikirim')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nomor_resi')
                    ->label('Nomor Resi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('ekspedisi')
                    ->label('Ekspedisi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('waktu_update')
                    ->label('Waktu Update')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListStatusPengirimen::route('/'),
            'create' => Pages\CreateStatusPengiriman::route('/create'),
            'edit' => Pages\EditStatusPengiriman::route('/{record}/edit'),
        ];
    }
}
