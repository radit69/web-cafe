@extends('backend.admin.layout')

@section('title', 'Pengaturan Sistem')
@section('page_title', 'Pengaturan Sistem')

@section('content')
    <div class="content-card">
        <form method="POST" action="{{ route('admin.settings.save') }}">
            @csrf
            <div style="background:#efe0cd;border-radius:18px;padding:16px 18px 18px;margin-bottom:18px;">
                <div style="font-size:13px;font-weight:600;margin-bottom:10px;">
                    Hari Operasional &nbsp;
                    <span style="color:#354024;">Senin - Minggu</span>
                </div>
                <div style="display:flex;gap:16px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:180px;">
                        <div style="font-size:12px;margin-bottom:4px;">Jam Buka</div>
                        <input
                            type="time"
                            name="jam_buka"
                            value="{{ old('jam_buka', $jamBuka ?? '08:00') }}"
                            style="background:#efe0cd;border-radius:10px;padding:10px 12px;font-size:13px;border:1px solid rgba(0,0,0,0.06);width:100%;"
                        >
                    </div>
                    <div style="flex:1;min-width:180px;">
                        <div style="font-size:12px;margin-bottom:4px;">Jam Tutup</div>
                        <input
                            type="time"
                            name="jam_tutup"
                            value="{{ old('jam_tutup', $jamTutup ?? '21:00') }}"
                            style="background:#efe0cd;border-radius:10px;padding:10px 12px;font-size:13px;border:1px solid rgba(0,0,0,0.06);width:100%;"
                        >
                    </div>
                </div>
            </div>

            <div style="background:#efe0cd;border-radius:18px;padding:16px 18px 18px;margin-bottom:22px;">
                <div style="font-size:13px;font-weight:600;margin-bottom:10px;">Pajak / Fee</div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div>
                        <div style="font-size:12px;margin-bottom:4px;">Pajak (%)</div>
                        <input
                            type="number"
                            step="0.01"
                            name="pajak"
                            value="{{ old('pajak', $pajak ?? 10) }}"
                            style="background:#efe0cd;border-radius:10px;padding:10px 12px;font-size:13px;border:1px solid rgba(0,0,0,0.06);width:160px;"
                        >
                    </div>
                    <div>
                        <div style="font-size:12px;margin-bottom:4px;">Service (%)</div>
                        <input
                            type="number"
                            step="0.01"
                            name="service"
                            value="{{ old('service', $service ?? 5) }}"
                            style="background:#efe0cd;border-radius:10px;padding:10px 12px;font-size:13px;border:1px solid rgba(0,0,0,0.06);width:160px;"
                        >
                    </div>
                </div>
            </div>

            <div style="background:#efe0cd;border-radius:18px;padding:16px 18px 18px;margin-bottom:22px;">
                <div style="font-size:13px;font-weight:600;margin-bottom:10px;">Pelunasan Reservasi</div>
                <div>
                    <div style="font-size:12px;margin-bottom:4px;">Izinkan pelunasan H-?</div>
                    <input
                        type="number"
                        min="0"
                        name="pelunasan_h_min"
                        value="{{ old('pelunasan_h_min', $pelunasanHMin ?? 1) }}"
                        style="background:#efe0cd;border-radius:10px;padding:10px 12px;font-size:13px;border:1px solid rgba(0,0,0,0.06);width:160px;"
                    >
                    <div style="font-size:11px;color:#6c6255;margin-top:4px;">Contoh: 1 = H-1, 0 = hari-H saja</div>
                </div>
            </div>

            <button type="submit" class="btn-pill" style="width:100%;padding:12px 18px;font-size:14px;">
                Simpan Pengaturan
            </button>
        </form>
    </div>
@endsection