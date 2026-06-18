<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();

        return view('backend.admin.users', compact('users', 'search'));
    }

    public function create()
    {
        return view('backend.admin.users_create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'max:255', Rule::unique('users', 'email')],
            'password'  => ['required', 'string', 'min:6'],
            'role'      => ['required', 'in:admin,kasir,pelanggan'],
            'is_active' => ['required', 'boolean'],
        ], [
            'email.unique' => 'Id User / email ini sudah digunakan. Pakai email atau kode lain.',
        ]);

        $data['password'] = bcrypt($data['password']);

        try {
            User::create($data);
        } catch (QueryException $exception) {
            if ($this->isDuplicateEmailException($exception)) {
                return back()
                    ->withInput($request->except('password'))
                    ->withErrors(['email' => 'Id User / email ini sudah digunakan. Pakai email atau kode lain.']);
            }

            throw $exception;
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('backend.admin.users_edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'      => ['required', 'in:admin,kasir,pelanggan'],
            'is_active' => ['required', 'boolean'],
            'password'  => ['nullable', 'string', 'min:6'],
        ], [
            'email.unique' => 'Id User / email ini sudah digunakan. Pakai email atau kode lain.',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        try {
            $user->update($data);
        } catch (QueryException $exception) {
            if ($this->isDuplicateEmailException($exception)) {
                return back()
                    ->withInput($request->except('password'))
                    ->withErrors(['email' => 'Id User / email ini sudah digunakan. Pakai email atau kode lain.']);
            }

            throw $exception;
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    private function isDuplicateEmailException(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $errorCode = (string) ($exception->errorInfo[1] ?? '');
        $message = $exception->getMessage();

        return $sqlState === '23000'
            && $errorCode === '1062'
            && str_contains($message, 'users_email_unique');
    }
}


