<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\KwspEmailDomain;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show the staff registration form (same page as login, register tab).
     */
    public function create(): View
    {
        return view('auth.login', ['activeTab' => 'register']);
    }

    /**
     * Handle an incoming registration request. Staff role only; @kwsp.gov.my email.
     */
    public function store(Request $request): RedirectResponse
    {
        $sn = $request->filled('staff_number') ? trim((string) $request->staff_number) : '';
        $request->merge(['staff_number' => $sn !== '' ? $sn : null]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'staff_number' => ['nullable', 'string', 'max:20', Rule::unique('users', 'staff_number')->whereNotNull('staff_number')],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', new KwspEmailDomain],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'staff_number.unique' => 'This Staff Number is already in use. Each staff member must have a unique Staff Number.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'staff_number' => $request->staff_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_STAFF,
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('success', 'Registration successful. You can now log in.');
    }
}
