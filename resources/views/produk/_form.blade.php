<!-- Tambahkan pembungkus tag <form> di paling atas -->
<form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
    
    @csrf  <!-- Cukup tulis @csrf sekali saja di sini -->

    @if (!empty($produk->foto))
        <div class="mb-2">
            <label>Foto Saat Ini</label><br>
            <img src="{{ asset('storage/'. $produk->foto) }}" width="150" class="img-thumbnail">
        </div>
    @endif

    <div class="row">
        <div class="col">
            <div>
                <label>Gambar</label>
                <input type="file" 
                       name="foto" 
                       onchange="previewImage(this)"
                       class="form-control @error('foto') is-invalid @enderror">
                @error('foto')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="mb-2">
            <label></label><br>
            <img id="preview" class="img-thumbnail mt-2" style="display:none" width="150">
        </div>
    </div>

    <!-- Nama Produk -->
    <div>
        <label>Nama Produk</label><br>
        <input type="text" name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $produk->nama ?? '') }}">
        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Harga Beli -->
    <div>
        <label>Harga Beli</label><br>
        <input type="number" name="purchase_price"
               class="form-control @error('purchase_price') is-invalid @enderror"
               value="{{ old('purchase_price', $produk->harga_beli ?? '') }}">
        @error('purchase_price')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Harga Jual -->
    <div>
        <label>Harga Jual</label><br>
        <input type="number" name="selling_price"
               class="form-control @error('selling_price') is-invalid @enderror"
               value="{{ old('selling_price', $produk->harga_jual ?? '') }}">
        @error('selling_price')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Stok -->
    <div>
        <label>Stok</label><br>
        <input type="number" name="stock"
               class="form-control @error('stock') is-invalid @enderror"
               value="{{ old('stock', $produk->stok ?? '') }}">
        @error('stock')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <button class="btn btn-success mt-3" type="submit">Simpan</button>
    <a href="{{ route('produk.index') }}" class="btn btn-secondary mt-3">Kembali</a>

</form> <!-- Tutup tag </form> di bawah tombol -->

<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    const file = input.files[0];

    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
}
</script>
