@extends('backend.admin.layout')

@section('title', 'Tambah Menu')
@section('page_title', 'Tambah Menu')

@section('content')
    <div class="content-card">
        <form action="{{ route('admin.menus.store') }}" method="post" enctype="multipart/form-data" style="max-width:520px;">
            @csrf

            <div style="margin-bottom:14px;">
                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Nama Menu</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       style="width:100%;padding:10px 12px;border-radius:10px;border:none;background:#FBEAC3;font-size:13px;"
                       placeholder="Contoh: Hot Latte">
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Deskripsi</label>
                <textarea name="description" rows="3" style="width:100%;padding:10px 12px;border-radius:10px;border:none;background:#FBEAC3;font-size:13px;resize:vertical;"
                    placeholder="Deskripsi menu...">{{ old('description') }}</textarea>
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Kategori</label>
                <select name="category"
                        style="width:220px;padding:10px 12px;border-radius:10px;border:none;background:#FBEAC3;font-size:13px;">
                    <option value="Coffee" {{ old('category') === 'Coffee' ? 'selected' : '' }}>Coffee</option>
                    <option value="Non-Coffee" {{ old('category') === 'Non-Coffee' ? 'selected' : '' }}>Non-Coffee</option>
                    <option value="Main Course" {{ old('category') === 'Main Course' ? 'selected' : '' }}>Main Course</option>
                    <option value="Dessert" {{ old('category') === 'Dessert' ? 'selected' : '' }}>Dessert</option>
                </select>
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Foto Menu</label>
                <input type="file" name="image" accept="image/*"
                       style="width:100%;padding:10px 12px;border-radius:10px;border:none;background:#FBEAC3;font-size:13px;">
                @error('image')
                    <div style="color:#b93b3b;font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:14px;">
                <div style="flex:1;min-width:160px;">
                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Harga Jual (Rp)</label>
                    <input type="number" name="price" value="{{ old('price') }}"
                           style="width:100%;padding:10px 12px;border-radius:10px;border:none;background:#FBEAC3;font-size:13px;"
                           placeholder="35000">
                </div>
            </div>

            <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:18px;">
                <div style="flex:1;min-width:160px;">
                    <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Stok Awal</label>
                    <input type="number" name="stock" value="{{ old('stock') }}"
                           style="width:100%;padding:10px 12px;border-radius:10px;border:none;background:#FBEAC3;font-size:13px;"
                           placeholder="100">
                </div>
                <div style="flex:1;min-width:160px;">
                    <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Status</label>
                    <select name="status"
                            style="width:100%;padding:10px 12px;border-radius:10px;border:none;background:#FBEAC3;font-size:13px;">
                        <option value="tersedia" {{ old('status', 'tersedia') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="habis" {{ old('status') === 'habis' ? 'selected' : '' }}>Habis</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-pill" style="padding:10px 22px;">Simpan Menu</button>
            <a href="{{ route('admin.menus.index') }}"
               style="margin-left:8px;font-size:13px;color:#7b6340;text-decoration:none;">Batal</a>
        </form>

        @if ($errors->any())
            <div style="margin-top:18px;font-size:12px;color:#b93b3b;">
                <ul style="padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection



