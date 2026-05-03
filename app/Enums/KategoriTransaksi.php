<?php

namespace App\Enums;

class KategoriTransaksi
{
    // Kategori untuk pemasukan
    const PEMASUKAN = [
        'gaji'        => ['label' => 'Gaji',          'icon' => 'payments',          'color' => 'teal'],
        'freelance'   => ['label' => 'Freelance',      'icon' => 'laptop_mac',        'color' => 'teal'],
        'investasi'   => ['label' => 'Investasi',      'icon' => 'trending_up',       'color' => 'teal'],
        'bisnis'      => ['label' => 'Bisnis',         'icon' => 'storefront',        'color' => 'teal'],
        'hadiah'      => ['label' => 'Hadiah/Bonus',   'icon' => 'card_giftcard',     'color' => 'teal'],
        'lainnya_in'  => ['label' => 'Lainnya',        'icon' => 'more_horiz',        'color' => 'teal'],
    ];

    // Kategori untuk pengeluaran
    const PENGELUARAN = [
        'makanan'     => ['label' => 'Makanan & Minum','icon' => 'restaurant',        'color' => 'red'],
        'transportasi'=> ['label' => 'Transportasi',   'icon' => 'directions_car',    'color' => 'red'],
        'belanja'     => ['label' => 'Belanja',        'icon' => 'shopping_bag',      'color' => 'red'],
        'tagihan'     => ['label' => 'Tagihan',        'icon' => 'receipt',           'color' => 'red'],
        'kesehatan'   => ['label' => 'Kesehatan',      'icon' => 'health_and_safety', 'color' => 'red'],
        'hiburan'     => ['label' => 'Hiburan',        'icon' => 'movie',             'color' => 'red'],
        'pendidikan'  => ['label' => 'Pendidikan',     'icon' => 'school',            'color' => 'red'],
        'rumah'       => ['label' => 'Rumah & Sewa',   'icon' => 'home',              'color' => 'red'],
        'lainnya_out' => ['label' => 'Lainnya',        'icon' => 'more_horiz',        'color' => 'red'],
    ];

    // Semua kategori digabung (untuk lookup)
    public static function all(): array
    {
        return array_merge(self::PEMASUKAN, self::PENGELUARAN);
    }

    public static function label(string $key): string
    {
        return self::all()[$key]['label'] ?? ucfirst($key);
    }

    public static function icon(string $key): string
    {
        return self::all()[$key]['icon'] ?? 'label';
    }

    public static function forTipe(string $tipe): array
    {
        return $tipe === 'pemasukan' ? self::PEMASUKAN : self::PENGELUARAN;
    }
}
