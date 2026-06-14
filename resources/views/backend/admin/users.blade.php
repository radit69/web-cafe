@extends('backend.admin.layout')

@section('title', 'Manajemen User')
@section('page_title', 'Manajemen User')

@section('content')
    <div class="top-actions">
        <a href="{{ route('admin.users.create') }}" class="btn-pill">Tambah User</a>
    </div>

    @if (session('success'))
        <div style="margin-bottom:14px;padding:10px 14px;border-radius:10px;background:rgba(139,195,74,0.16);font-size:13px;color:#487b1f;">
            {{ session('success') }}
        </div>
    @endif

    <div class="content-card">
        <form method="GET" action="{{ route('admin.users.index') }}" style="margin-bottom:14px;">
            <input
                type="text"
                name="search"
                value="{{ $search ?? '' }}"
                placeholder="Cari user..."
                style="width:100%;padding:10px 14px;border-radius:999px;border:none;font-size:13px;background:#efe0cd;"
            >
        </form>

        <div style="border-radius:18px;overflow:hidden;background:#efe0cd;">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Id User (Email)</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="width:110px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role ?? '-') }}</td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user) }}" title="Edit" style="margin-right:6px;text-decoration:none;">✏️</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="post" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus" style="border:none;background:none;cursor:pointer;">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:16px;font-size:13px;color:#7b6340;">
                                Belum ada user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection



