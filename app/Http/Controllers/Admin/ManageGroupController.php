<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManageGroupController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $groups = Group::query()->get()->filter(function ($group) use ($search) {
            if ($search === '') {
                return true;
            }

            $needle = strtolower($search);

            return str_contains(strtolower((string) ($group->name ?? '')), $needle);
        })->sortBy('name')->values();

        return view('admin.groups.index', compact('groups', 'search'));
    }

    public function create(): View
    {
        return view('admin.groups.form', [
            'group' => null,
            'action' => route('admin.groups.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Group::create($data);

        return redirect()->route('admin.groups.index')->with('success', 'Group created.');
    }

    public function edit(Group $group): View
    {
        return view('admin.groups.form', [
            'group' => $group,
            'action' => route('admin.groups.update', $group),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $group->update($data);

        return redirect()->route('admin.groups.index')->with('success', 'Group updated.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $group->delete();

        return back()->with('success', 'Group deleted.');
    }
}
