@extends('layouts.app')

@section('title', 'POS')

@section('content')

@if(session('errors'))
     <div class="alert alert-danger">
        {{ session('errors') }}
     </div>
@endif

<h4 class="mb-3">
    Tambah dan Edit
</h4>

<div class="row">

    {{-- ---------------- PRODUK ---------------- --}}
    <div class="col-md-6">
        <div class="card">
           <div class="card-body" style="max-height:70vh; overflow:auto">
                <div class="mb-3">
                    <form method="GET" action="{{ route('penjualan.create') }}">
                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Cari produk..."
                            onkeyup="this.form.submit()">
                    </form>
                </div>

                @foreach($products as $product)
                    <form method="POST" action="{{ route('itempenjualan.store') }}" class="row mb-2">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="col-7">
                            <button type="submit"
                                class="btn btn-outline-primary w-100 text-start p-2 {{ $sale->status === 'COMPLETED' ? 'disabled' : '' }}">
                                <div class="d-flex align-items-center gap-2">
                                    
                                         
                                    <div>
                                        <div class="fw-semibold">{{ $product->nama }}</div>
                                        <small class="text-muted">
                                            Rp {{ number_format($product->harga_jual) }}
                                        </small>
                                    </div>
                                </div>
                            </button>
                        </div>

                        <div class="col-3">
                            <input type="number" name="quantity" value="1" min="1"
                                   class="form-control">
                        </div>

                        <div class="col-2">
                            <button class="btn btn-primary w-100">+</button>
                        </div>
                    </form>
                @endforeach
            </div> 
        </div>
    </div>

    {{-- ===================== KERANJANG ===================== --}}
    <div class="col-md-6">
        <div class="card">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($sale->itemPenjualan as $item)
                        <tr>
                            <td>{{ $item->produk->nama ?? 'Produk Tidak Ditemukan' }}</td>
                            <td>Rp.{{ number_format($item->produk->harga_jual ?? 0) }}</td>

                            <td>
                                <form method="POST" action="{{ route('itempenjualan.update', $item->id) }}">
                                    @csrf @method('PUT')
                                    <input type="number"
                                           name="quantity"
                                           value="{{ $item->kuantitas }}"
                                           class="form-control form-control-sm">
                                </form>
                            </td>

                            <td>Rp {{ number_format($item->subtotal) }}</td>
                            <td>
                                @can('delete', $item)
                                <form method="POST" action="{{ route('itempenjualan.destroy', $item->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Keranjang kosong
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="card-footer">
                <strong>Rp {{ number_format($sale->total_pembayaran) }}</strong>

                {{-- FORM CHECKOUT --}}
                <form method="POST"
                      action="{{ route('penjualan.update', $sale->id) }}"
                      onsubmit="return confirm('Yakin ingin checkout?')" 
                      class="mt-2">
                    @csrf
                    @method('PUT')
                    <select name="payment_method" class="form-select mb-2" required>
                        <option value="">Pilih Pembayaran</option>
                        <option value="CASH" {{ $sale->metode_pembayaran == 'CASH' ? 'selected' : '' }}>Cash</option>
                        <option value="QRIS" {{ $sale->metode_pembayaran == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                    </select>

                    <button class="btn btn-success w-100 {{ $sale->itemPenjualan->count() == 0 || $sale->status === 'COMPLETED' ? 'disabled' : '' }}">
                        Checkout
                    </button>
                </form>

                {{-- FORM BATALKAN TRANSAKSI --}}
                @can('delete', $sale)
                <form action="{{ route('penjualan.destroy', $sale->id) }}"
                      method="POST"
                      onsubmit="return confirm('Yakin ingin membatalkan transaksi?')"
                      class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger w-100 {{ $sale->status === 'COMPLETED' ? 'disabled' : '' }}">
                        Batalkan Transaksi
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection