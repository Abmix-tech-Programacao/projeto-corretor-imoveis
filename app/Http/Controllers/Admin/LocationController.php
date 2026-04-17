<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Support\LocationHierarchy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.locations.index', [
            'rows' => LocationHierarchy::adminRows(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.locations.create', [
            'parentOptions' => LocationHierarchy::optionsForSelect(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $parentId = $validated['parent_id'] ?? null;
        $parent = $parentId ? Location::query()->find($parentId) : null;
        $depth = $parent ? ($parent->depth + 1) : 0;

        if ($depth > 3) {
            return back()->withErrors(['parent_id' => 'Permitido até 4 níveis (cidade > região > bairro > subbairro).'])->withInput();
        }

        Location::create([
            'name' => $validated['name'],
            'slug' => $this->nextSlug($validated['name']),
            'parent_id' => $parent?->id,
            'depth' => $depth,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.locations.index')->with('success', 'Localização criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location): RedirectResponse
    {
        return redirect()->route('admin.locations.edit', $location);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location): View
    {
        return view('admin.locations.edit', [
            'location' => $location,
            'parentOptions' => LocationHierarchy::optionsForSelect($location->id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $validated = $request->validated();
        $parentId = $validated['parent_id'] ?? null;

        if (! LocationHierarchy::canUseParent($location->id, $parentId)) {
            return back()->withErrors(['parent_id' => 'Não é permitido usar um filho como pai.'])->withInput();
        }

        $parent = $parentId ? Location::query()->find($parentId) : null;
        $depth = $parent ? ($parent->depth + 1) : 0;

        if ($depth > 3) {
            return back()->withErrors(['parent_id' => 'Permitido até 4 níveis (cidade > região > bairro > subbairro).'])->withInput();
        }

        $location->update([
            'name' => $validated['name'],
            'slug' => $this->nextSlug($validated['name'], $location->id),
            'parent_id' => $parent?->id,
            'depth' => $depth,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        $this->syncDescendantsDepth($location);

        return redirect()->route('admin.locations.index')->with('success', 'Localização atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location): RedirectResponse
    {
        if ($location->children()->exists()) {
            return back()->withErrors(['location' => 'Remova ou mova as sublocalizações antes de excluir.']);
        }

        if ($location->properties()->exists()) {
            return back()->withErrors(['location' => 'Existem imóveis vinculados a essa localização.']);
        }

        $location->delete();

        return redirect()->route('admin.locations.index')->with('success', 'Localização removida com sucesso.');
    }

    private function nextSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (Location::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function syncDescendantsDepth(Location $location): void
    {
        $children = $location->children()->get();

        foreach ($children as $child) {
            $newDepth = $location->depth + 1;
            if ($child->depth !== $newDepth) {
                $child->update(['depth' => $newDepth]);
            }

            $this->syncDescendantsDepth($child);
        }
    }
}
