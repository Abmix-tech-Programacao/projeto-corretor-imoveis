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
        return;
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

const propertyLocationSelect = document.querySelector('[data-property-location-select]');
const propertyCityPreview = document.querySelector('[data-property-city-preview]');
const propertyNeighborhoodPreview = document.querySelector('[data-property-neighborhood-preview]');

if (propertyLocationSelect && propertyCityPreview && propertyNeighborhoodPreview) {
    let lastAutoNeighborhood = propertyNeighborhoodPreview.value || '';

    const syncPropertyLocationPreview = () => {
        const selectedOption = propertyLocationSelect.options[propertyLocationSelect.selectedIndex];
        const city = selectedOption ? selectedOption.dataset.city || '' : '';
        const neighborhood = selectedOption ? selectedOption.dataset.neighborhood || '' : '';

        propertyCityPreview.value = city;

        const currentNeighborhood = (propertyNeighborhoodPreview.value || '').trim();
        if (currentNeighborhood === '' || currentNeighborhood === lastAutoNeighborhood) {
            propertyNeighborhoodPreview.value = neighborhood;
        }

        lastAutoNeighborhood = neighborhood;
    };

    propertyLocationSelect.addEventListener('change', syncPropertyLocationPreview);
    syncPropertyLocationPreview();
}
