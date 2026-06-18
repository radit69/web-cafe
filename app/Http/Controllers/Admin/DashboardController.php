<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Reservation;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $dailyTransactionsCount = Sale::whereDate('created_at', $today)->count();
        $dailyRevenue = Sale::whereDate('created_at', $today)->sum('total');
        $userCount = User::count();
        $menuCount = Menu::where('status', '!=', 'nonaktif')->count();
        $reservationCount = Reservation::count();

        $weekLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $weekTotals = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $weekTotals[] = Sale::whereDate('created_at', $date)->sum('total');
        }

        $monthLabels = [];
        $monthTotals = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabels[] = $date->translatedFormat('M');
            $monthTotals[] = Sale::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total');
        }

        return view('backend.admin.dashboard', compact(
            'dailyTransactionsCount', 'dailyRevenue', 'userCount', 'menuCount',
            'weekLabels', 'weekTotals', 'monthLabels', 'monthTotals',
            'reservationCount'
        ));
    }

    public function sales()
    {
        return view('backend.admin.sales');
    }

    public function salesExport()
    {
        return "Export laporan penjualan (contoh).";
    }

    public function salesExportPdf()
    {
        $from = request('from', today()->subDays(30)->toDateString());
        $to = request('to', today()->toDateString());

        $sales = Sale::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->orderBy('created_at')->get();

        $totalRevenue = $sales->sum('total');
        $totalOrders = $sales->count();

        $html = view('backend.admin.pdf_sales', compact('sales', 'from', 'to', 'totalRevenue', 'totalOrders'))->render();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return $dompdf->stream('laporan-penjualan-' . $from . '-sampai-' . $to . '.pdf');
    }

    public function settings()
    {
        $pajak = Setting::getValue('pajak', '10');
        $service = Setting::getValue('service', '5');
        $jamBuka = Setting::getValue('jam_buka', '08:00');
        $jamTutup = Setting::getValue('jam_tutup', '21:00');
        $pelunasanHMin = Setting::getValue('pelunasan_h_min', '1');

        return view('backend.admin.settings', compact('pajak', 'service', 'jamBuka', 'jamTutup', 'pelunasanHMin'));
    }

    public function settingsSave(Request $request)
    {
        $data = $request->validate([
            'jam_buka' => ['required', 'date_format:H:i'],
            'jam_tutup' => ['required', 'date_format:H:i'],
            'pajak' => ['required', 'numeric', 'min:0', 'max:100'],
            'service' => ['required', 'numeric', 'min:0', 'max:100'],
            'pelunasan_h_min' => ['required', 'integer', 'min:0', 'max:30'],
        ]);

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        return back()->with('status', 'Pengaturan berhasil disimpan.');
    }
}
