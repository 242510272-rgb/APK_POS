@extends('layouts.app')

@section('title', 'Detail Penjualan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Transaksi Penjualan</h5>
                    <div>
                        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        @if($sale->status !== 'COMPLETED')
                            <a href="{{ route('penjualan.edit', $sale) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    {{-- Alert Messages --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Informasi Transaksi --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-receipt"></i> Informasi Transaksi</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">ID Transaksi</th>
                                            <td>: <strong>#{{ $sale->id }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Transaksi</th>
                                            <td>: {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                : 
                                                @if($sale->status === 'COMPLETED')
                                                    <span class="badge bg-success">COMPLETED</span>
                                                @elseif($sale->status === 'OPEN')
                                                    <span class="badge bg-warning">OPEN</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ strtoupper($sale->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Metode Pembayaran</th>
                                            <td>: {{ $sale->metode_pembayaran ?? 'CASH' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Pembayaran</th>
                                            <td>: <strong>Rp {{ number_format($sale->total_pembayaran, 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-user"></i> Informasi Kasir</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Nama Kasir</th>
                                            <td>: {{ $sale->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>: {{ $sale->user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Role</th>
                                            <td>: {{ $sale->user->role->name ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Item Produk --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-shopping-cart"></i> Daftar Produk yang Dibeli</h6>
                                </div>
                                <div class="card-body">
                                    @if($sale->itemPenjualan->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Nama Produk</th>
                                                        <th class="text-end">Harga Satuan</th>
                                                        <th class="text-center">Jumlah</th>
                                                        <th class="text-end">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($sale->itemPenjualan as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->produk->nama ?? 'Produk Tidak Ditemukan' }}</td>
                                                        <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                                        <td class="text-center">{{ $item->kuantitas }}</td> <!-- PERBAIKAN DI SINI -->
                                                        <td class="text-end">
                                                            <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <th colspan="4" class="text-end">TOTAL</th>
                                                        <th class="text-end">
                                                            @php
                                                                $total = $sale->itemPenjualan->sum('subtotal');
                                                            @endphp
                                                            <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
                                                        </th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Tidak ada item produk dalam transaksi ini.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="row mt-4">
                        <div class="col-md-12 text-end">
                            @if($sale->status === 'OPEN')
                                <form action="{{ route('penjualan.destroy', $sale) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah anda yakin akan menghapus penjualan ini?')">
                                        <i class="fas fa-trash"></i> Hapus Penjualan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('error'))
<script>
    $(document).ready(function() {
        alert('{{ session('error') }}');
    });
</script>
@endif
@endsection