@csrf

<div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $user->name ?? '' ) }}">
  @error('name')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror

</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email"
           class="form-control  @error('email') is-invalid @enderror"
           value="{{ old('email', $user->email ?? '' ) }}">
    @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

</div>

<div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" name="password"
           class="form-control @error('password') is-invalid @enderror">
    @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Role</label>
    <select name="role_id"
            class="form-select @error('role_id') is-invalid @enderror">
        <option value="">-- Pilih Role --</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}"
                @selected(old('role_id', $user->role_id ?? '') == $role->id)>
                {{ ucfirst($role->name) }}
            </option>
        @endforeach
    </select>
    @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<button class="btn btn-success">Simpan</button>
<a href="{{ route('admin.users') }}" class="btn btn-secondary">Kembali</a>
