const toggleButton = document.querySelector('[data-menu-toggle]');
const nav = document.querySelector('[data-main-nav]');

if (toggleButton && nav) {
    toggleButton.addEventListener('click', () => {
        nav.classList.toggle('is-open');
    });
}

document.querySelectorAll('[data-gallery]').forEach((galleryElement) => {
    const mainImage = galleryElement.querySelector('[data-gallery-main]');
    const thumbItems = galleryElement.querySelectorAll('[data-gallery-item]');

    if (!mainImage || thumbItems.length === 0) {
        return;
    }

    thumbItems.forEach((button) => {
        button.addEventListener('click', () => {
            mainImage.src = button.dataset.galleryItem || mainImage.src;
            thumbItems.forEach((thumb) => thumb.classList.remove('is-active'));
            button.classList.add('is-active');
        });
    });
});

const findNodeBySlug = (nodes, slug) => {
    if (!Array.isArray(nodes) || !slug) {
        return null;
    }

    return nodes.find((node) => node && node.slug === slug) || null;
};

const fillLocationSelect = (select, nodes, placeholder, selectedSlug) => {
    if (!select) {
        return;
    }

    const previousValue = selectedSlug || select.value || '';
    select.innerHTML = '';

    const placeholderOption = document.createElement('option');
    placeholderOption.value = '';
    placeholderOption.textContent = placeholder;
    select.appendChild(placeholderOption);

    if (!Array.isArray(nodes)) {
        select.value = '';
        return;
    }

    nodes.forEach((node) => {
        if (!node || !node.slug || !node.name) {
            return;
        }

        const option = document.createElement('option');
        option.value = node.slug;
        option.textContent = node.name;
        select.appendChild(option);
    });

    if (previousValue && findNodeBySlug(nodes, previousValue)) {
        select.value = previousValue;
    } else {
        select.value = '';
    }
};

document.querySelectorAll('[data-location-filter]').forEach((filterElement) => {
    const treeJson = filterElement.getAttribute('data-location-tree') || '[]';
    let locationTree = [];

    try {
        locationTree = JSON.parse(treeJson);
    } catch (error) {
        locationTree = [];
    }

    if (!Array.isArray(locationTree) || locationTree.length === 0) {
        locationTree = [];
    }

    const selects = [0, 1, 2, 3].map((level) =>
        filterElement.querySelector(`[data-location-select="${level}"]`)
    );
    const wrappers = [0, 1, 2, 3].map((level) =>
        filterElement.querySelector(`[data-location-wrapper="${level}"]`)
    );
    const selectedValues = selects.map((select) => (select ? select.dataset.selected || '' : ''));
    const placeholders = selects.map((select, index) =>
        select ? select.dataset.placeholder || `Nivel ${index + 1}` : `Nivel ${index + 1}`
    );

    const childrenForLevel = (level) => {
        let nodes = locationTree;

        for (let index = 0; index <= level; index += 1) {
            const select = selects[index];
            const selectedSlug = select ? select.value : '';

            if (!selectedSlug) {
                return [];
            }

            const node = findNodeBySlug(nodes, selectedSlug);
            if (!node) {
                return [];
            }

            nodes = Array.isArray(node.children) ? node.children : [];
        }

        return nodes;
    };

    fillLocationSelect(selects[0], locationTree, placeholders[0], selectedValues[0]);

    for (let level = 1; level < selects.length; level += 1) {
        const nodes = childrenForLevel(level - 1);
        fillLocationSelect(selects[level], nodes, placeholders[level], selectedValues[level]);

        if (wrappers[level]) {
            wrappers[level].hidden = nodes.length === 0;
        }
    }

    selects.forEach((select, changedIndex) => {
        if (!select) {
            return;
        }

        select.addEventListener('change', () => {
            for (let level = changedIndex + 1; level < selects.length; level += 1) {
                const nodes = childrenForLevel(level - 1);
                fillLocationSelect(selects[level], nodes, placeholders[level], '');

                if (wrappers[level]) {
                    wrappers[level].hidden = nodes.length === 0;
                }
            }
        });
    });
});

const propertyCitySelect = document.querySelector('[data-property-city-select]');
const propertyNeighborhoodSelect = document.querySelector('[data-property-neighborhood-select]');
const propertyLocationIdInput = document.querySelector('[data-property-location-id]');
const propertyCityPreview = document.querySelector('[data-property-city-preview]');
const propertyNeighborhoodPreview = document.querySelector('[data-property-neighborhood-preview]');

if (
    propertyCitySelect &&
    propertyNeighborhoodSelect &&
    propertyLocationIdInput &&
    propertyCityPreview &&
    propertyNeighborhoodPreview
) {
    const treeJson = propertyLocationIdInput.dataset.propertyLocationTree || '[]';
    let locationTree = [];

    try {
        locationTree = JSON.parse(treeJson);
    } catch (error) {
        locationTree = [];
    }

    if (!Array.isArray(locationTree) || locationTree.length === 0) {
        return;
    }

    let lastAutoNeighborhood = propertyNeighborhoodPreview.value || '';

    const findPathById = (nodes, targetId, path = []) => {
        if (!Array.isArray(nodes) || !targetId) {
            return [];
        }

        for (const node of nodes) {
            if (!node || !node.id) {
                continue;
            }

            const nextPath = [...path, node];
            if (Number(node.id) === Number(targetId)) {
                return nextPath;
            }

            const childPath = findPathById(node.children || [], targetId, nextPath);
            if (childPath.length > 0) {
                return childPath;
            }
        }

        return [];
    };

    const collectDescendants = (nodes, level = 1) => {
        if (!Array.isArray(nodes)) {
            return [];
        }

        const items = [];
        nodes.forEach((node) => {
            if (!node || !node.id || !node.name) {
                return;
            }

            items.push({
                id: Number(node.id),
                name: node.name,
                label: `${'- '.repeat(Math.max(0, level - 1))}${node.name}`,
            });

            items.push(...collectDescendants(node.children || [], level + 1));
        });

        return items;
    };

    const findCityNode = (cityId) =>
        locationTree.find((node) => Number(node?.id) === Number(cityId)) || null;

    const fillNeighborhoodOptions = (cityNode, selectedId = null) => {
        propertyNeighborhoodSelect.innerHTML = '';

        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = 'Selecione';
        propertyNeighborhoodSelect.appendChild(placeholderOption);

        const neighborhoods = cityNode ? collectDescendants(cityNode.children || []) : [];
        neighborhoods.forEach((item) => {
            const option = document.createElement('option');
            option.value = String(item.id);
            option.textContent = item.label;
            propertyNeighborhoodSelect.appendChild(option);
        });

        propertyNeighborhoodSelect.disabled = neighborhoods.length === 0;

        if (selectedId && neighborhoods.some((item) => Number(item.id) === Number(selectedId))) {
            propertyNeighborhoodSelect.value = String(selectedId);
        } else {
            propertyNeighborhoodSelect.value = '';
        }
    };

    const syncPropertyLocationPreview = () => {
        const cityNode = findCityNode(propertyCitySelect.value);
        const selectedNeighborhoodOption =
            propertyNeighborhoodSelect.options[propertyNeighborhoodSelect.selectedIndex] || null;
        const city = cityNode ? cityNode.name : '';
        const neighborhood =
            selectedNeighborhoodOption && selectedNeighborhoodOption.value
                ? selectedNeighborhoodOption.textContent.replace(/^-+\s*/, '').trim()
                : city;

        propertyCityPreview.value = city;
        propertyLocationIdInput.value = selectedNeighborhoodOption?.value || propertyCitySelect.value || '';

        const currentNeighborhood = (propertyNeighborhoodPreview.value || '').trim();
        if (currentNeighborhood === '' || currentNeighborhood === lastAutoNeighborhood) {
            propertyNeighborhoodPreview.value = neighborhood;
        }

        lastAutoNeighborhood = neighborhood;
    };

    const selectedLocationId = Number(
        propertyCitySelect.dataset.selectedLocationId || propertyLocationIdInput.value || 0
    );
    const selectedPath = findPathById(locationTree, selectedLocationId);
    const initialCityId = selectedPath.length > 0 ? Number(selectedPath[0].id) : 0;
    const initialNeighborhoodId =
        selectedPath.length > 1 ? Number(selectedPath[selectedPath.length - 1].id) : null;

    if (initialCityId) {
        propertyCitySelect.value = String(initialCityId);
    }

    fillNeighborhoodOptions(findCityNode(propertyCitySelect.value), initialNeighborhoodId);

    propertyCitySelect.addEventListener('change', () => {
        fillNeighborhoodOptions(findCityNode(propertyCitySelect.value), null);
        syncPropertyLocationPreview();
    });
    propertyNeighborhoodSelect.addEventListener('change', syncPropertyLocationPreview);
    syncPropertyLocationPreview();
}

const priceOnRequestCheckbox = document.querySelector('[data-price-on-request]');
const priceField = document.querySelector('[data-price-field]');

if (priceOnRequestCheckbox && priceField) {
    const syncPriceField = () => {
        const isOnRequest = priceOnRequestCheckbox.checked;
        priceField.disabled = isOnRequest;

        if (isOnRequest) {
            priceField.value = '';
        }
    };

    priceOnRequestCheckbox.addEventListener('change', syncPriceField);
    syncPriceField();
}
