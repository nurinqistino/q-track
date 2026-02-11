<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CounterController extends Controller
{
    public function index(): View
    {
        $counters = Counter::with('service')->orderBy('service_id')->orderBy('number')->get();

        return view('admin.counters.index', compact('counters'));
    }

    public function create(): View
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('admin.counters.create', compact('services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1', 'unique:counters,number'],
            'service_id' => ['required', 'exists:services,id'],
            'is_active' => ['boolean'],
        ], [
            'number.unique' => 'This counter number is already in use. Each counter can only be assigned to one service.',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        Counter::create($validated);

        return redirect()->route('admin.counters.index')->with('success', 'Counter created.');
    }

    public function edit(Counter $counter): View
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('admin.counters.edit', compact('counter', 'services'));
    }

    public function update(Request $request, Counter $counter): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1', 'unique:counters,number,' . $counter->id],
            'service_id' => ['required', 'exists:services,id'],
            'is_active' => ['boolean'],
        ], [
            'number.unique' => 'This counter number is already in use. Each counter can only be assigned to one service.',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $counter->update($validated);

        return redirect()->route('admin.counters.index')->with('success', 'Counter updated.');
    }

    public function destroy(Counter $counter): RedirectResponse
    {
        $counter->delete();

        return redirect()->route('admin.counters.index')->with('success', 'Counter deleted.');
    }
}
