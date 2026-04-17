@php
    $editing = isset($property);
    $selectedLocationId = (string) old('location_id', $property->location_id ?? '');
    $selectedLocation = collect($locationOptions)->firstWhere('id', (int) $selectedLocationId);
@endphp

<section class="admin-form-section">
    <div class="admin-form-section-head">
        <h2>Dados principais</h2>
        <p>Preencha os dados basicos do imovel para publicacao.</p>
    </div>

    <div class="form-grid">
        <label>
            Titulo
            <input type="text" name="title" value="{{ old('title', $property->title ?? '') }}" required>
        </label>
        <label>
            Codigo
            <input type="text" name="code" value="{{ old('code', $property->code ?? '') }}" required>
        </label>
        <label>
            Tipo
            <select name="property_type" required>
                <option value="">Selecione</option>
                @foreach ($propertyTypeOptions as $typeOption)
                    <option value="{{ $typeOption['value'] }}" @selected(old('property_type', $property->property_type ?? '') === $typeOption['value'])>
                        {{ $typeOption['label'] }}
                    </option>
                @endforeach
            </select>
        </label>
        <label>
            Finalidade
            <select name="purpose" required>
                <option value="">Selecione</option>
                @foreach ($purposeOptions as $purposeOption)
                    <option value="{{ $purposeOption['value'] }}" @selected(old('purpose', $property->purpose ?? ($purposeOptions[0]['value'] ?? '')) === $purposeOption['value'])>
                        {{ $purposeOption['label'] }}
                    </option>
                @endforeach
            </select>
        </label>
        <label>
            Categoria do menu
            <select name="menu_category">
                <option value="">Nenhuma</option>
                @foreach ($menuCategoryOptions as $menuOption)
                    <option value="{{ $menuOption['value'] }}" @selected(old('menu_category', $property->menu_category ?? '') === $menuOption['value'])>
                        {{ $menuOption['label'] }}
                    </option>
                @endforeach
            </select>
        </label>
        <label>
            Localizacao
            <select name="location_id" required data-property-location-select>
                <option value="">Selecione</option>
                @foreach ($locationOptions as $option)
                    <option
                        value="{{ $option['id'] }}"
                        data-city="{{ $option['city'] }}"
                        data-neighborhood="{{ $option['neighborhood'] }}"
                        @selected($selectedLocationId === (string) $option['id'])
                    >
                        {{ $option['label'] }}
                    </option>
                @endforeach
            </select>
        </label>
        <label>
            Estado
            <input type="text" name="state" value="{{ old('state', $property->state ?? 'SP') }}" maxlength="2" required>
        </label>
        <label>
            Cidade (automatico)
            <input
                type="text"
                value="{{ old('city', $selectedLocation['city'] ?? $property->city ?? '') }}"
                readonly
                data-property-city-preview
            >
        </label>
        <label>
            Bairro (automatico)
            <input
                type="text"
                value="{{ old('neighborhood', $selectedLocation['neighborhood'] ?? $property->neighborhood ?? '') }}"
                readonly
                data-property-neighborhood-preview
            >
        </label>
        <label>
            Endereco
            <input type="text" name="address" value="{{ old('address', $property->address ?? '') }}">
        </label>
        <label>
            Preco
            <input type="number" step="0.01" name="price" value="{{ old('price', $property->price ?? '') }}">
        </label>
        <label>
            Dormitorios
            <input type="number" name="bedrooms" min="0" value="{{ old('bedrooms', $property->bedrooms ?? 1) }}" required>
        </label>
        <label>
            Banheiros
            <input type="number" name="bathrooms" min="0" value="{{ old('bathrooms', $property->bathrooms ?? 1) }}" required>
        </label>
        <label>
            Vagas
            <input type="number" name="parking_spaces" min="0" value="{{ old('parking_spaces', $property->parking_spaces ?? 1) }}" required>
        </label>
        <label>
            Area (m2)
            <input type="number" name="area" min="10" value="{{ old('area', $property->area ?? '') }}">
        </label>
        <label>
            Latitude
            <input type="text" name="latitude" value="{{ old('latitude', $property->latitude ?? '') }}" placeholder="Ex.: -23.5505">
        </label>
        <label>
            Longitude
            <input type="text" name="longitude" value="{{ old('longitude', $property->longitude ?? '') }}" placeholder="Ex.: -46.6333">
        </label>
    </div>
</section>

<section class="admin-form-section">
    <div class="admin-form-section-head">
        <h2>Descricao e diferenciais</h2>
        <p>Escreva uma descricao clara e objetiva para ajudar na conversao.</p>
    </div>

    <label>
        Descricao
        <textarea name="description" rows="4" required>{{ old('description', $property->description ?? '') }}</textarea>
    </label>

    <label>
        Diferenciais (1 por linha)
        <textarea name="features_text" rows="4">{{ old('features_text', isset($property) ? implode(PHP_EOL, $property->feature_list) : '') }}</textarea>
    </label>
</section>

<section class="admin-form-section">
    <div class="admin-form-section-head">
        <h2>Imagens</h2>
        <p>Voce pode usar URL ou upload de imagens.</p>
    </div>

    <div class="form-grid">
        <label>
            URL da capa (opcional)
            <input type="url" name="featured_image_url" value="{{ old('featured_image_url', $property->featured_image ?? '') }}">
        </label>
        <label>
            Upload da capa
            <input type="file" name="featured_image_upload" accept="image/*">
        </label>
        <label>
            URLs da galeria (1 por linha)
            <textarea name="gallery_urls" rows="4">{{ old('gallery_urls') }}</textarea>
        </label>
        <label>
            Upload da galeria
            <input type="file" name="gallery_uploads[]" accept="image/*" multiple>
        </label>
    </div>

    @if ($editing && $property->images->isNotEmpty())
        <div class="admin-card admin-card-soft">
            <h3>Imagens atuais da galeria</h3>
            <p class="inline-hint">Marque as imagens que deseja remover.</p>
            <div class="image-check-grid">
                @foreach ($property->images as $image)
                    <label class="image-check-item">
                        <img src="{{ $image->path }}" alt="Imagem {{ $image->id }}">
                        <span>
                            <input type="checkbox" name="remove_images[]" value="{{ $image->id }}">
                            Remover
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif
</section>

<section class="admin-form-section">
    <div class="admin-form-section-head">
        <h2>Publicacao</h2>
        <p>Defina como o imovel aparece no site.</p>
    </div>

    <div class="checkbox-row">
        <label class="checkbox-line">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $property->is_featured ?? false))>
            Destacar na home
        </label>
        <label class="checkbox-line">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $property->is_published ?? true))>
            Publicar no site
        </label>
    </div>
</section>

<div class="admin-form-actions">
    <button type="submit" class="btn btn-primary">{{ $editing ? 'Salvar alteracoes' : 'Criar imovel' }}</button>
</div>
