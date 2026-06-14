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
            'active' => (clone $baseQuery)->where('is_active', true)->count(),
            'inactive' => (clone $baseQuery)->where('is_active', false)->count(),
            'new_this_month' => (clone $baseQuery)->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $customers = (clone $baseQuery)
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->string('q')->toString();

                $query->where(function ($inner) use ($keyword) {
                    $inner->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                }

                if ($request->status === 'inactive') {
                    $query->where('is_active', false);
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
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
        ]);

        $data['role'] = 'pelanggan';
        $data['password'] = Hash::make($data['password'] ?: Str::random(16));

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

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$customer->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
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

        $customer->update(['is_active' => ! $customer->is_active]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Status customer berhasil diperbarui.');
    }

    public function export(): StreamedResponse
    {
        $customers = User::where('role', 'pelanggan')->orderBy('name')->get();

        return response()->streamDownload(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nama', 'Email', 'Nomor Telepon', 'Status', 'Terdaftar', 'Login Terakhir']);

            foreach ($customers as $customer) {
                fputcsv($handle, [
                    $customer->name,
                    $customer->email,
                    $customer->phone,
                    $customer->is_active ? 'Aktif' : 'Nonaktif',
                    optional($customer->created_at)->format('Y-m-d H:i:s'),
                    optional($customer->last_login_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'customers.csv');
    }
}
