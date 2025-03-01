<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan;
use App\Models\PesananItem;
use App\Models\StatusPengiriman;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Produk Terjual', PesananItem::sum('jumlah'))
                ->description('Total produk yang telah terjual'),

            Stat::make('Produk Berhasil Terkirim', StatusPengiriman::where('status', 'Diterima')->count())
                ->description('Total produk yang berhasil dikirim')
                ->color('success'),

            Stat::make('Produk Gagal Dikirim', StatusPengiriman::where('status', 'Gagal')->count())
                ->description('Total produk yang gagal terkirim')
                ->color('danger'),

            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format(
                Pesanan::whereHas('statusPengiriman', function ($query) {
                    $query->where('status', 'Diterima') // Cek pesanan yang berhasil diterima
                            ->whereDate('waktu_update', Carbon::today()); // Cek diterima hari ini
                })->sum('grand_total'),
                0, ',', '.'
            ))
                ->description('Pendapatan dari pesanan yang sudah diterima oleh pelanggan hari ini')
                ->color('primary'),
        ];
    }
}
