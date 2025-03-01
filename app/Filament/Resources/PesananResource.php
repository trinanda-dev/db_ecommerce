<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesananResource\Pages;
use App\Models\Pesanan;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pesanan';
    protected static ?int $navigationSort = 5;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->options([
                        'Menunggu Validasi Admin' => 'Menunggu Validasi Admin',
                        'Menunggu Pembayaran' => 'Menunggu Pembayaran',
                        'Berhasil' => 'Berhasil',
                        'Gagal' => 'Gagal',
                    ])
                    ->required(),

                TextInput::make('ongkos_kirim')
                    ->numeric()
                    ->minValue(0)
                    ->label('Ongkos Kirim')
                    ->required(),

                TextInput::make('bukti_transfer')
                    ->disabled()
                    ->label('Bukti Transfer (URL)'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('pengguna.nama')->label('Nama Pelanggan'),
                TextColumn::make('total_harga')->label('Total Harga')->money('IDR', true),
                TextColumn::make('ongkos_kirim')->label('Ongkos Kirim')->money('IDR', true)->sortable(),
                TextColumn::make('grand_total')->label('Grand Total')->money('IDR', true)->sortable(),
                TextColumn::make('status')->label('Status')->sortable()
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Menunggu Validasi Admin' => 'danger',
                        'Menunggu Pembayaran' => 'primary',
                        'Berhasil' => 'success',
                        'Gagal' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('alamat.kecamatan')->label('Kecamatan'),
                TextColumn::make('alamat.kota')->label('Kota'),
                TextColumn::make('alamat.provinsi')->label('Provinsi'),
                TextColumn::make('bukti_transfer')->label('Bukti Transfer')
                    ->formatStateUsing(fn($state) => $state
                        ? '<a href="'.asset('storage/'.$state).'" target="_blank">Lihat</a>'
                        : '-')
                    ->html(),
            ])
            ->actions([
                Action::make('Validasi Ongkir')
                    ->visible(fn($record) => $record->status === 'Menunggu Validasi Admin')
                    ->form([
                        TextInput::make('ongkos_kirim')
                            ->label('Masukkan Ongkos Kirim')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'ongkos_kirim' => $data['ongkos_kirim'],
                            'status' => 'Menunggu Pembayaran',
                        ]);
                    })
                    ->button()
                    ->color('success'),

                Action::make('Validasi Pembayaran')
                    ->visible(fn($record) => $record->status === 'Menunggu Pembayaran' && $record->bukti_transfer)
                    ->action(function ($record) {
                        $record->update(['status' => 'Berhasil']);

                        // Pastikan ada status pengiriman, jika tidak buat baru
                        $record->statusPengiriman()->updateOrCreate(
                            ['pesanan_id' => $record->id], // Cek apakah status_pengiriman sudah ada
                            ['status' => 'Diproses']
                        );
                    })
                    ->button()
                    ->color('success'),

                Action::make('Tolak Pembayaran')
                    ->visible(fn($record) => $record->status === 'Menunggu Pembayaran')
                    ->form([
                        TextInput::make('catatan')->label('Alasan Penolakan')->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'Gagal',
                            'catatan' => $data['catatan'],
                        ]);
                    })
                    ->button()
                    ->color('danger'),
            ]);
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesanans::route('/'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}
