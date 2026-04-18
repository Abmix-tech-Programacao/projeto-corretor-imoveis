<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Support\FilterCatalog;
use App\Support\LocationHierarchy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $properties = Property::query()->with('location')->latest()->paginate(20);

        return view('admin.properties.index', [
            'properties' => $properties,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $purposeOptions = FilterCatalog::purposes();
        $menuCategoryOptions = FilterCatalog::menuCategories();

        return view('admin.properties.create', [
            'locationOptions' => LocationHierarchy::optionsForSelect(),
            'propertyTypeOptions' => FilterCatalog::propertyTypes(),
            'purposeOptions' => $purposeOptions->isNotEmpty()
                ? $purposeOptions
                : collect([['value' => 'venda', 'label' => 'Venda'], ['value' => 'aluguel', 'label' => 'Aluguel']]),
            'menuCategoryOptions' => $menuCategoryOptions->isNotEmpty()
                ? $menuCategoryOptions
                : collect([
                    ['value' => 'lancamento', 'label' => 'Lançamentos'],
                    ['value' => 'breve-lancamento', 'label' => 'Breve Lançamento'],
                    ['value' => 'imovel-pronto', 'label' => 'Imóvel Pronto'],
                    ['value' => 'para-alugar', 'label' => 'Para Alugar'],
                ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $data = $this->preparePayload($request->validated(), $request->boolean('is_featured'), $request->boolean('is_published'));

        if ($request->hasFile('featured_image_upload')) {
            $data['featured_image'] = $this->storeUploadedImage($request->file('featured_image_upload'), 'properties/cover');
        } elseif (! empty($data['featured_image_url'])) {
            $data['featured_image'] = $data['featured_image_url'];
        }

        unset($data['featured_image_url']);

        $property = Property::create($data);

        $this->syncGallery($property, $request, false);

        return redirect()->route('admin.properties.index')->with('success', 'Imóvel criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property): RedirectResponse
    {
        return redirect()->route('admin.properties.edit', $property);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property): View
    {
        $property->load('images');
        $purposeOptions = FilterCatalog::purposes();
        $menuCategoryOptions = FilterCatalog::menuCategories();

        return view('admin.properties.edit', [
            'property' => $property,
            'locationOptions' => LocationHierarchy::optionsForSelect(),
            'propertyTypeOptions' => FilterCatalog::propertyTypes(),
            'purposeOptions' => $purposeOptions->isNotEmpty()
                ? $purposeOptions
                : collect([['value' => 'venda', 'label' => 'Venda'], ['value' => 'aluguel', 'label' => 'Aluguel']]),
            'menuCategoryOptions' => $menuCategoryOptions->isNotEmpty()
                ? $menuCategoryOptions
                : collect([
                    ['value' => 'lancamento', 'label' => 'Lançamentos'],
                    ['value' => 'breve-lancamento', 'label' => 'Breve Lançamento'],
                    ['value' => 'imovel-pronto', 'label' => 'Imóvel Pronto'],
                    ['value' => 'para-alugar', 'label' => 'Para Alugar'],
                ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $data = $this->preparePayload($request->validated(), $request->boolean('is_featured'), $request->boolean('is_published'));

        if ($request->hasFile('featured_image_upload')) {
            $this->deleteIfInternalImage($property->featured_image);
            $data['featured_image'] = $this->storeUploadedImage($request->file('featured_image_upload'), 'properties/cover');
        } elseif (! empty($data['featured_image_url'])) {
            $data['featured_image'] = $data['featured_image_url'];
        }

        unset($data['featured_image_url']);

        $property->update($data);

        $this->removeSelectedImages($request->input('remove_images', []), $property->id);
        $this->syncGallery($property, $request, true);

        return redirect()->route('admin.properties.edit', $property)->with('success', 'Imóvel atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property): RedirectResponse
    {
        $property->load('images');

        $this->deleteIfInternalImage($property->featured_image);

        foreach ($property->images as $image) {
            $this->deleteIfInternalImage($image->path);
        }

        $property->delete();

        return redirect()->route('admin.properties.index')->with('success', 'Imóvel removido.');
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function preparePayload(array $validated, bool $isFeatured, bool $isPublished): array
    {
        $features = $this->normalizeFeatureText($validated['features_text'] ?? null);
        $locationId = isset($validated['location_id']) ? (int) $validated['location_id'] : null;
        $locationContext = LocationHierarchy::resolvePropertyContext($locationId);
        $manualCity = trim((string) ($validated['city'] ?? ''));
        $manualNeighborhood = trim((string) ($validated['neighborhood'] ?? ''));

        return [
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']).'-'.Str::slug($validated['code']),
            'code' => Str::upper($validated['code']),
            'property_type' => $validated['property_type'],
            'purpose' => $validated['purpose'],
            'menu_category' => $validated['menu_category'] ?? null,
            'city' => $manualCity !== '' ? $manualCity : ($locationContext['city'] ?? null),
            'state' => Str::upper($validated['state']),
            'location_id' => $locationId,
            'neighborhood' => $manualNeighborhood !== '' ? $manualNeighborhood : ($locationContext['neighborhood'] ?? null),
            'address' => $validated['address'] ?? null,
            'price' => $validated['price'] ?? null,
            'bedrooms' => $validated['bedrooms'],
            'bathrooms' => $validated['bathrooms'],
            'parking_spaces' => $validated['parking_spaces'],
            'area' => $validated['area'] ?? null,
            'description' => $validated['description'],
            'features' => $features,
            'featured_image_url' => $validated['featured_image_url'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'is_featured' => $isFeatured,
            'is_published' => $isPublished,
        ];
    }

    private function normalizeFeatureText(?string $featuresText): ?string
    {
        if (! $featuresText) {
            return null;
        }

        $features = collect(preg_split('/\r\n|\r|\n/', $featuresText) ?: [])
            ->map(fn (?string $line): string => trim((string) $line))
            ->filter()
            ->values()
            ->all();

        return $features ? implode('|', $features) : null;
    }

    private function storeUploadedImage(\Illuminate\Http\UploadedFile $file, string $dir): string
    {
        $path = $file->store($dir, 'public');

        return Storage::url($path);
    }

    private function deleteIfInternalImage(?string $path): void
    {
        if (! $path || ! Str::startsWith($path, '/storage/')) {
            return;
        }

        $storagePath = Str::after($path, '/storage/');
        Storage::disk('public')->delete($storagePath);
    }

    /**
     * @param array<int, mixed> $imageIds
     */
    private function removeSelectedImages(array $imageIds, int $propertyId): void
    {
        if ($imageIds === []) {
            return;
        }

        $images = PropertyImage::query()
            ->where('property_id', $propertyId)
            ->whereIn('id', $imageIds)
            ->get();

        foreach ($images as $image) {
            $this->deleteIfInternalImage($image->path);
            $image->delete();
        }
    }

    private function syncGallery(Property $property, \Illuminate\Http\Request $request, bool $append): void
    {
        $positionBase = $append ? ((int) $property->images()->max('position') + 1) : 0;

        foreach ($request->file('gallery_uploads', []) as $index => $imageFile) {
            $property->images()->create([
                'path' => $this->storeUploadedImage($imageFile, 'properties/gallery'),
                'position' => $positionBase + $index,
            ]);
        }

        $galleryUrls = collect(preg_split('/\r\n|\r|\n/', (string) $request->input('gallery_urls')) ?: [])
            ->map(fn (?string $line): string => trim((string) $line))
            ->filter(fn (string $line): bool => filter_var($line, FILTER_VALIDATE_URL) !== false)
            ->values();

        foreach ($galleryUrls as $index => $url) {
            $property->images()->create([
                'path' => $url,
                'position' => $positionBase + $index + 100,
            ]);
        }
    }
}
