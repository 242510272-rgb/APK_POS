{{-- resources/views/produk/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Detail Produk</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Foto Produk -->
                        <div class="col-md-4 text-center">
                            @if($produk->foto)
                                <img src="{{ asset('storage/' . $produk->foto) }}" 
                                     class="img-fluid rounded mb-3" 
                                     alt="{{ $produk->nama }}"
                                     style="max-height: 250px;">
                            @else
                                <div class="text-center py-4 bg-light rounded">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="mt-2 text-muted">Tidak ada foto</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Informasi Produk -->
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Nama Produk</th>
                                    <td>{{ $produk->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Harga Beli</th>
                                    <td>Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Harga Jual</th>
                                    <td>Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Stok</th>
                                    <td>
                                        <span class="badge bg-{{ $produk->stok > 0 ? 'success' : 'danger' }}">
                                            {{ $produk->stok }} unit
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ditambahkan Oleh</th>
                                    <td>{{ $produk->user->name ?? 'Tidak diketahui' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Ditambahkan</th>
                                    <td>{{ $produk->created_at->translatedFormat('d F Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diupdate</th>
                                    <td>{{ $produk->updated_at->translatedFormat('d F Y H:i') }}</td>
                                </tr>
                            </table>
                            
                            <!-- Margin Keuntungan -->
                            <div class="alert alert-info mt-3">
                                <h6 class="alert-heading">Informasi Keuntungan</h6>
                                <hr>
                                <div class="row">
                                    <div class="col">
                                        <small>Margin</small>
                                        <h5>Rp {{ number_format($produk->harga_jual - $produk->harga_beli, 0, ',', '.') }}</h5>
                                    </div>
                                    <div class="col">
                                        <small>Persentase</small>
                                        <h5>
                                            @if($produk->harga_beli > 0)
                                                {{ number_format((($produk->harga_jual - $produk->harga_beli) / $produk->harga_beli) * 100, 2) }}%
                                            @else
                                                0%
                                            @endif
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('produk.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    @can('update', $produk)
                        <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection