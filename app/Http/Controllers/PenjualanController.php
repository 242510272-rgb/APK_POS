<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request)
    {
        $user = Auth::user();
        $keyword = $request->input('search');

        $sales = Penjualan::query()
            ->with(['user', 'itemPenjualan'])
            ->when($user->role->name === 'kasir', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('penjualan.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $sale = Penjualan::with(['itemPenjualan.produk'])
            ->firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'status' => 'OPEN'
                ],
                [
                    'total_pembayaran' => 0,
                    'metode_pembayaran' => 'CASH'
                ]
            );

        $keyword = $request->input('search');

        if($keyword) {
            $products = Produk::when($keyword, function ($query) use ($keyword) {
                $query->where('nama', 'like', '%' . $keyword . '%');
            })
            ->orderBy('nama')
            ->get();
        } else {
            $products = Produk::orderBy('nama')->get();
        }

        $mode = 'create';

        return view('penjualan.pos', compact('sale', 'products', 'mode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:CASH,QRIS',
            'total_pembayaran' => 'required|numeric|min:1'
        ]);

        $sale = Penjualan::where('user_id', Auth::id())
            ->where('status', 'OPEN')
            ->firstOrFail();

        DB::transaction(function () use ($request, $sale) {
            $sale->update([
                'status' => 'COMPLETED',
                'metode_pembayaran' => $request->payment_method,
                'total_pembayaran' => $request->total_pembayaran
            ]);
        });

        return redirect()->route('penjualan.show', $sale)
            ->with('success', 'Transaksi berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Penjualan $penjualan)
    {
        $sale = $penjualan->load(['itemPenjualan.produk', 'user']);

        return view('penjualan.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penjualan $penjualan)
    {
        $sale = $penjualan;

        abort_if($sale->status === 'COMPLETED', 403);

        $sale->load('itemPenjualan.produk');
        $products = Produk::orderBy('nama')->get();
        $mode = 'edit';

        return view('penjualan.pos', compact('sale', 'products', 'mode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        $request->validate([
            'payment_method' => 'required|in:CASH,QRIS'
        ]);

        if ($penjualan->status !== 'OPEN') {
            return back()->with('error', 'Transaksi sudah diproses');
        }

        if ($penjualan->itemPenjualan()->count() === 0) {
            return back()->with('error', 'Keranjang masih kosong');
        }

        DB::transaction(function () use ($penjualan, $request) {
            $total = $penjualan->itemPenjualan()->sum('subtotal');

            $penjualan->update([
                'metode_pembayaran' => $request->payment_method,
                'total_pembayaran'  => $total,
                'status'            => 'COMPLETED'
            ]);
        });

        return redirect()->route('penjualan.show', $penjualan)
            ->with('success', 'Transaksi berhasil diselesaikan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penjualan $penjualan)
    {
        $this->authorize('delete', $penjualan);
        // Jika status COMPLETED, redirect ke halaman detail dengan pesan error
        if ($penjualan->status === 'COMPLETED') {
            return redirect()->route('penjualan.show', $penjualan)->with('error', 'Penjualan dengan status COMPLETED tidak dapat dihapus!');
        }
        
        // ! Pastikan hanya transaksi OPEN
        if ($penjualan->status !== 'OPEN') {
            return redirect()->route('penjualan.index')->with('error', 'Transaksi sudah selesai tidak bisa dibatalkan');
        }

        DB::transaction(function () use ($penjualan) {
            foreach ($penjualan->itemPenjualan as $item) {
                // 🔼 kembalikan stok - PASTIKAN INTEGER
                if ($item->produk) {
                    // Konversi ke integer untuk memastikan numeric
                    $jumlah = (int) $item->jumlah;
                    if ($jumlah > 0) {
                        $item->produk->increment('stok', $jumlah);
                    }
                }
            }

            // ❌ hapus item
            $penjualan->itemPenjualan()->delete();

            // ❌ hapus penjualan
            $penjualan->delete();
        });

        return redirect()
            ->route('penjualan.index')
            ->with('success', 'Transaksi berhasil dibatalkan');
    }
}