<?php

namespace App\Http\Controllers;

use App\Models\ItemPenjualan;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemPenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produk,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $sale = Penjualan::where('user_id', Auth::id())
                    ->where('status', 'OPEN')
                    ->firstOrFail();

                $product = Produk::lockForUpdate()
                    ->findOrFail($request->product_id);

                // Cek stok
                if ($product->stok < $request->quantity) {
                    throw new \Exception('Produk stok tidak mencukupi');
                }

                // Kurangi stok
                $product->decrement('stok', $request->quantity);

                // + Update / insert item penjualan
                $item = ItemPenjualan::where('penjualan_id', $sale->id)
                     ->where('produk_id', $product->id)
                     ->lockForUpdate()
                     ->first();

                if ($item) {
                    // UPDATE
                    $item->kuantitas += $request->quantity;
                } else {
                    // CREATE - Cara manual tanpa mass assignment
                    $item = new ItemPenjualan();
                    $item->penjualan_id = $sale->id;
                    $item->produk_id = $product->id;
                    $item->kuantitas = $request->quantity;
                    $item->harga_satuan = $product->harga_jual;
                }

                // hitung subtotal SETELAH kuantitas fix
                $item->subtotal = $item->kuantitas * $item->harga_satuan;
                $item->save();

                // TOTAL PEMBAYARAN
                $sale->total_pembayaran = $sale->itemPenjualan()->sum('subtotal');
                $sale->save();
            });

            return back()->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemPenjualan $itempenjualan)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::transaction(function () use ($request, $itempenjualan) {
                // Ambil produk dengan ID langsung
                $produk = Produk::lockForUpdate()->find($itempenjualan->produk_id);
                
                if (!$produk) {
                    throw new \Exception('Produk tidak ditemukan');
                }

                $selisih = $request->quantity - $itempenjualan->kuantitas;

                // Jika qty bertambah, kurangi stok
                if ($selisih > 0) {
                    if ($produk->stok < $selisih) {
                        throw new \Exception('Stok tidak mencukupi');
                    }

                    $produk->decrement('stok', $selisih);
                }

                // Jika qty berkurang, kembalikan stok
                if ($selisih < 0) {
                    $produk->increment('stok', abs($selisih));
                }

                // Ambil penjualan dengan ID langsung
                $penjualan = Penjualan::find($itempenjualan->penjualan_id);

                // Update item
                $itempenjualan->kuantitas = $request->quantity;
                $itempenjualan->subtotal = $request->quantity * $itempenjualan->harga_satuan;
                $itempenjualan->save();

                // Update total penjualan jika ada
                if ($penjualan) {
                    $penjualan->total_pembayaran = $penjualan->itemPenjualan()->sum('subtotal');
                    $penjualan->save();
                }
            });

            return back()->with('success', 'Quantity berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemPenjualan $itempenjualan)
    {
        $this->authorize('delete', $itempenjualan);
        try {
            DB::transaction(function () use ($itempenjualan) {
                
                $produk = Produk::find($itempenjualan->produk_id);
                
                
                $sale = Penjualan::find($itempenjualan->penjualan_id);

                
                if ($produk) {
                    $produk->increment('stok', $itempenjualan->kuantitas);
                }

                
                $itempenjualan->delete();

                
                if ($sale) {
                    $sale->total_pembayaran = $sale->itemPenjualan()->sum('subtotal');
                    $sale->save();
                }
            });

            return back()->with('success', 'Item berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }
    }
}