<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemPenjualan extends Model
{
    use HasFactory;
    
    protected $table = 'Item_penjualan';

    // PERBAIKAN 1: 'kuantitas' bukan 'kualitas'
    protected $fillable = [
        'penjualan_id',
        'produk_id',
        'kuantitas', // ← INI YANG BENAR
        'harga_satuan',
        'subtotal'
    ];

    // PERBAIKAN 2: Relasi ke Produk, bukan Role
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id');
    }

    // PERBAIKAN 3: Relasi ke Penjualan adalah belongsTo, bukan hasMany
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id', 'id');
    }
}