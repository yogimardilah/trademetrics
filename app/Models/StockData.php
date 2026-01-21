<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockData extends Model
{
    use HasFactory;

    protected $table = 'stock_data';

    protected $fillable = [
        'kode_saham',
        'nama_perusahaan',
        'remarks',
        'sebelumnya',
        'open_price',
        'tanggal_perdagangan_terakhir',
        'first_trade',
        'tertinggi',
        'terendah',
        'penutupan',
        'selisih',
        'volume',
        'nilai',
        'frekuensi',
        'index_individual',
        'offer',
        'offer_volume',
        'bid',
        'bid_volume',
        'listed_shares',
        'tradable_shares',
        'weight_for_index',
        'foreign_sell',
        'foreign_buy',
        'non_regular_volume',
        'non_regular_value',
        'non_regular_frequency',
    ];

    protected $casts = [
        'tanggal_perdagangan_terakhir' => 'date',
    ];
}
