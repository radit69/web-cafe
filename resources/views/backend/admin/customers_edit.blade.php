@extends('backend.admin.layout')

@section('title', 'Edit Customer')
@section('page_title', 'Edit Customer')

@section('content')
    <div class="content-card">
        <form action="{{ route('admin.customers.update', $customer) }}" method="post" style="max-width:560px;">
            @csrf
            @method('PUT')

            <div style="margin-bottom:14px;">
                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Nama Customer</label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                       style="width:100%;padding:10px 12px;border-radius:8px;border:none;background:#FBEAC3;font-size:13px;">
                @error('name')
                    <div style="color:#b93b3b;font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Email</label>
                <input type="email" name="email" value="{{ old('email', $customer->email) }}" required
                       style="width:100%;padding:10px 12px;border-radius:8px;border:none;background:#FBEAC3;font-size:13px;">
                @error('email')
                    <div style="color:#b93b3b;font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                       style="width:100%;padding:10px 12px;border-radius:8px;border:none;background:#FBEAC3;font-size:13px;">
                @error('phone')
                    <div style="color:#b93b3b;font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Password Baru</label>
                <input type="password" name="password"
                       style="width:100%;padding:10px 12px;border-radius:8px;border:none;background:#FBEAC3;font-size:13px;">
                <div style="font-size:11px;color:#7b6340;margin-top:4px;">Kosongkan jika tidak ingin mengganti.</div>
                @error('password')
                    <div style="color:#b93b3b;font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom:18px;">
                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:4px;">Status</label>
                <select name="is_active" required
                        style="width:200px;padding:10px 12px;border-radius:8px;border:none;background:#FBEAC3;font-size:13px;">
                    <option value="1" {{ old('is_active', $customer->is_active) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active', $customer->is_active) == 0 ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('is_active')
                    <div style="color:#b93b3b;font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-pill" style="padding:10px 22px;">Update</button>
            <a href="{{ route('admin.customers.index') }}" style="margin-left:8px;font-size:13px;color:#7b6340;text-decoration:none;">Batal</a>
        </form>
    </div>
@endsection
