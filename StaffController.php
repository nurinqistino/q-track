<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\User;
use App\Rules\KwspEmailDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(): View
    {
        $staff = User::where('role', User::ROLE_STAFF)->orWhere('role', User::ROLE_ADMIN)
            ->with('counters.service')
            ->orderBy('name')
            ->get();

        return view('admin.staff.index', compact('staff'));
    }

    public function create(): View
    {
        // Only show counters that are not assigned to any staff (no duplicate assignment)
        $counters = Counter::with('service')
            ->where('is_active', true)
            ->whereDoesntHave('users')
            ->orderBy('service_id')
            ->orderBy('number')
            ->get();

        return view('admin.staff.create', compact('counters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['staff_number' => trim((string) ($request->staff_number ?? '')) ?: null]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'staff_number' => ['nullable', 'string', 'max:20', Rule::unique('users', 'staff_number')->whereNotNull('staff_number')],
            'email' => ['required', 'email', 'unique:users,email', new KwspEmailDomain],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:admin,staff'],
            'counters' => ['nullable', 'array'],
            'counters.*' => ['exists:counters,id'],
        ], [
            'staff_number.unique' => 'This Staff Number is already in use. Each staff member must have a unique Staff Number.',
        ]);

        $staffNumber = isset($validated['staff_number']) ? trim((string) $validated['staff_number']) : '';
        $user = User::create([
            'name' => $validated['name'],
            'staff_number' => $staffNumber !== '' ? $staffNumber : null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        if (! empty($validated['counters'])) {
            $user->counters()->sync($validated['counters']);
            // One counter = one staff: ensure each counter is only assigned to this user
            foreach ($validated['counters'] as $counterId) {
                Counter::find($counterId)?->users()->sync([$user->id]);
            }
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff created.');
    }

    public function edit(User $staff): View
    {
        // Show counters that are unassigned OR assigned to this staff only (assigned to others disappear)
        $counters = Counter::with('service')
            ->where('is_active', true)
            ->where(function ($q) use ($staff) {
                $q->whereDoesntHave('users')
                    ->orWhereHas('users', fn ($q2) => $q2->where('users.id', $staff->id));
            })
            ->orderBy('service_id')
            ->orderBy('number')
            ->get();

        return view('admin.staff.edit', compact('staff', 'counters'));
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        $request->merge(['staff_number' => trim((string) ($request->staff_number ?? '')) ?: null]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'staff_number' => ['nullable', 'string', 'max:20', Rule::unique('users', 'staff_number')->ignore($staff->id)],
            'email' => ['required', 'email', 'unique:users,email,' . $staff->id, new KwspEmailDomain],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:admin,staff'],
            'counters' => ['nullable', 'array'],
            'counters.*' => ['exists:counters,id'],
        ], [
            'staff_number.unique' => 'This Staff Number is already in use. Each staff member must have a unique Staff Number.',
        ]);

        $staffNumber = isset($validated['staff_number']) ? trim((string) $validated['staff_number']) : '';
        $staff->name = $validated['name'];
        $staff->staff_number = $staffNumber !== '' ? $staffNumber : null;
        $staff->email = $validated['email'];
        $staff->phone = $validated['phone'] ?? null;
        $staff->role = $validated['role'];

        if (! empty($validated['password'])) {
            $staff->password = Hash::make($validated['password']);
        }

        $staff->save();

        $counterIds = $validated['counters'] ?? [];
        $staff->counters()->sync($counterIds);
        // One counter = one staff: ensure each counter is only assigned to this user
        foreach ($counterIds as $counterId) {
            Counter::find($counterId)?->users()->sync([$staff->id]);
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff updated.');
    }

    public function destroy(User $staff): RedirectResponse
    {
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff deleted.');
    }
}
