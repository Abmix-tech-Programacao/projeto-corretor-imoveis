@php
    $editing = isset($property);
    $selectedLocationId = (string) old('location_id', $property->location_id ?? '');
    $selectedCityLocationId = (string) old('city_location_id', '');
    $selectedBrokerUserId = (string) old('broker_user_id', $property->broker_user_id ?? auth()->id());
    $cityOptions = collect($locationTree ?? [])->filter(fn (array $node): bool => ((int) ($node['depth'] ?? 0)) === 0)->values();
    $priceOnRequestChecked = old('price_on_request', isset($property) ? ! filled($property->price) : false);
@endphp

<section class="admin-form-section">
    <div class="admin-form-section-head">
        <h2>Dados principais</h2>
        <p>Preencha os dados básicos do imóvel para publicação.</p>
    </div>

    <div class="form-grid">
        <label>
            Título
            <input type="text" name="title" value="{{ old('title', $property->title ?? '') }}" required>
        </label>
        <label>
            Código
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
            Corretor responsavel
            <select name="broker_user_id" required>
                <option value="">Selecione</option>
                @foreach ($brokerUsers as $brokerUser)
                    <option value="{{ $brokerUser->id }}" @selected($selectedBrokerUserId === (string) $brokerUser->id)>
                        {{ $brokerUser->name }}
                    </option>
                @endforeach
            </select>
        </label>
        <label>
            Cidade
            <select name="city_location_id" data-property-city-select data-selected-city-id="{{ $selectedCityLocationId }}" required>
                <option value="">Selecione</option>
                @foreach ($cityOptions as $cityOption)
                    <option value="{{ $cityOption['id'] }}">{{ $cityOption['name'] }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Bairro / Região
            <select name="location_id" data-property-neighborhood-select data-selected-location-id="{{ $selectedLocationId }}">
                <option value="">Selecione</option>
            </select>
            <small class="inline-hint">Mostra apenas bairros e regiões da cidade selecionada.</small>
            <input type="hidden" data-property-location-tree='@json($locationTree ?? [])'>
        </label>
        <label>
            Estado
            <input type="text" name="state" value="{{ old('state', $property->state ?? 'SP') }}" maxlength="2" required>
        </label>
        <label>
            Cidade (automática)
            <input
                type="text"
                name="city"
                value="{{ old('city', $property->city ?? '') }}"
                readonly
                data-property-city-preview
            >
        </label>
        <label>
            Bairro
            <input
                type="text"
                name="neighborhood"
                value="{{ old('neighborhood', $property->neighborhood ?? '') }}"
                data-property-neighborhood-preview
            >
            <small class="inline-hint">Preenchido automaticamente pela localização, mas você pode editar.</small>
        </label>
        <label>
            Endereço
            <input type="text" name="address" value="{{ old('address', $property->address ?? '') }}">
        </label>
        <label>
            Preço
            <input type="number" step="0.01" name="price" value="{{ old('price', $property->price ?? '') }}" data-price-field @disabled($priceOnRequestChecked)>
        </label>
        <label class="checkbox-line">
            <input type="checkbox" name="price_on_request" value="1" data-price-on-request @checked($priceOnRequestChecked)>
            Exibir como "Preço sob consulta"
        </label>
        <label>
            Dormitórios
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
            Área (m2)
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
        <h2>Descrição e diferenciais</h2>
        <p>Escreva uma descrição clara e objetiva para ajudar na conversão.</p>
    </div>

    <label>
        Descrição
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
        <p>Você pode usar URL ou upload de imagens.</p>
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
        <h2>Publicação</h2>
        <p>Defina como o imóvel aparece no site.</p>
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
    <button type="submit" class="btn btn-primary">{{ $editing ? 'Salvar alterações' : 'Criar imóvel' }}</button>
</div>
