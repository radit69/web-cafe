<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = User::query()->where('role', 'pelanggan');

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('is_aktif', true)->count(),
            'inactive' => (clone $baseQuery)->where('is_aktif', false)->count(),
            'new_this_month' => (clone $baseQuery)->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $customers = (clone $baseQuery)
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->string('q')->toString();

                $query->where(function ($inner) use ($keyword) {
                    $inner->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('no_hp', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'active') {
                    $query->where('is_aktif', true);
                }

                if ($request->status === 'inactive') {
                    $query->where('is_aktif', false);
                }
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('backend.admin.customers', compact('customers', 'stats'));
    }

    public function create()
    {
        return view('backend.admin.customers_create');
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
        ]);

        $data = [
            'nama' => $requestData['name'],
            'email' => $requestData['email'],
            'no_hp' => $requestData['phone'] ?? null,
            'password' => Hash::make($requestData['password'] ?: Str::random(16)),
            'role' => 'pelanggan',
            'is_aktif' => $requestData['is_active'],
        ];

        User::create($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil ditambahkan.');
    }

    public function edit(User $customer)
    {
        abort_unless($customer->role === 'pelanggan', 404);

        return view('backend.admin.customers_edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        abort_unless($customer->role === 'pelanggan', 404);

        $requestData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$customer->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
        ]);

        $data = [
            'nama' => $requestData['name'],
            'email' => $requestData['email'],
            'no_hp' => $requestData['phone'] ?? null,
            'is_aktif' => $requestData['is_active'],
        ];

        if (! empty($requestData['password'])) {
            $data['password'] = Hash::make($requestData['password']);
        }

        $customer->update($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(User $customer)
    {
        abort_unless($customer->role === 'pelanggan', 404);

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil dihapus.');
    }

    public function toggle(User $customer)
    {
        abort_unless($customer->role === 'pelanggan', 404);

        $customer->update(['is_aktif' => ! $customer->is_aktif]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Status customer berhasil diperbarui.');
    }

    public function export(): StreamedResponse
    {
        $customers = User::where('role', 'pelanggan')->orderBy('nama')->get();

        return response()->streamDownload(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nama', 'Email', 'Nomor Telepon', 'Status', 'Terdaftar', 'Login Terakhir']);

            foreach ($customers as $customer) {
                fputcsv($handle, [
                    $customer->nama,
                    $customer->email,
                    $customer->no_hp,
                    $customer->is_aktif ? 'Aktif' : 'Nonaktif',
                    optional($customer->created_at)->format('Y-m-d H:i:s'),
                    optional($customer->login_terakhir)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'customers.csv');
    }
}
