<?php

namespace App\Filament\Resources\PesananResource\Pages;

use App\Filament\Resources\PesananResource;
use Filament\Resources\Pages\EditRecord;

class EditPesanan extends EditRecord
{
    protected static string $resource = PesananResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['status'] === 'Berhasil' || $data['status'] === 'Gagal') {
            $data['bukti_transfer'] = null; // Jika transaksi gagal, hapus bukti transfer
        }

        return $data;
    }
}
