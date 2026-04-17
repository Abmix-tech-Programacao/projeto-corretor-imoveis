<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFilterOptionRequest;
use App\Http\Requests\UpdateFilterOptionRequest;
use App\Models\FilterOption;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FilterOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $group = request('group');
        $groups = FilterOption::groups();

        $options = FilterOption::query()
            ->when($group, fn ($query, string $selectedGroup) => $query->where('group_key', $selectedGroup))
            ->orderBy('group_key')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->paginate(40)
            ->withQueryString();

        return view('admin.filter-options.index', [
            'options' => $options,
            'groups' => $groups,
            'selectedGroup' => $group,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.filter-options.create', [
            'groups' => FilterOption::groups(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFilterOptionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $value = trim((string) ($validated['value'] ?? ''));
        if ($value === '') {
            $value = trim((string) $validated['label']);
        }

        $this->ensureUniquePair($validated['group_key'], $value);

        FilterOption::create([
            'group_key' => $validated['group_key'],
            'label' => $validated['label'],
            'value' => $value,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.filter-options.index')->with('success', 'Opção de filtro criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FilterOption $filterOption): RedirectResponse
    {
        return redirect()->route('admin.filter-options.edit', $filterOption);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FilterOption $filterOption): View
    {
        return view('admin.filter-options.edit', [
            'option' => $filterOption,
            'groups' => FilterOption::groups(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilterOptionRequest $request, FilterOption $filterOption): RedirectResponse
    {
        $validated = $request->validated();
        $value = trim((string) ($validated['value'] ?? ''));
        if ($value === '') {
            $value = trim((string) $validated['label']);
        }

        $this->ensureUniquePair($validated['group_key'], $value, $filterOption->id);

        if (in_array(
            $filterOption->group_key,
            [FilterOption::GROUP_PROPERTY_TYPE, FilterOption::GROUP_PURPOSE, FilterOption::GROUP_MENU_CATEGORY],
            true
        )) {
            $column = match ($filterOption->group_key) {
                FilterOption::GROUP_PROPERTY_TYPE => 'property_type',
                FilterOption::GROUP_PURPOSE => 'purpose',
                default => 'menu_category',
            };
            $isLinkedType = Property::query()->where($column, $filterOption->value)->exists();
            $typeChanged = $filterOption->value !== $value;
            $groupChanged = $validated['group_key'] !== $filterOption->group_key;

            if ($isLinkedType && ($typeChanged || $groupChanged)) {
                throw ValidationException::withMessages([
                    'value' => 'Não é possível alterar o valor pois existem imóveis vinculados a esta opção.',
                ]);
            }
        }

        $filterOption->update([
            'group_key' => $validated['group_key'],
            'label' => $validated['label'],
            'value' => $value,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.filter-options.index')->with('success', 'Opção de filtro atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FilterOption $filterOption): RedirectResponse
    {
        if (in_array(
            $filterOption->group_key,
            [FilterOption::GROUP_PROPERTY_TYPE, FilterOption::GROUP_PURPOSE, FilterOption::GROUP_MENU_CATEGORY],
            true
        )) {
            $column = match ($filterOption->group_key) {
                FilterOption::GROUP_PROPERTY_TYPE => 'property_type',
                FilterOption::GROUP_PURPOSE => 'purpose',
                default => 'menu_category',
            };
            if (Property::query()->where($column, $filterOption->value)->exists()) {
                return back()->withErrors(['filter_option' => 'Existem imóveis usando esta opção.']);
            }
        }

        $filterOption->delete();

        return redirect()->route('admin.filter-options.index')->with('success', 'Opção de filtro removida.');
    }

    private function ensureUniquePair(string $groupKey, string $value, ?int $ignoreId = null): void
    {
        $exists = FilterOption::query()
            ->where('group_key', $groupKey)
            ->where('value', $value)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'value' => 'Já existe uma opção com este valor neste grupo.',
            ]);
        }
    }
}
