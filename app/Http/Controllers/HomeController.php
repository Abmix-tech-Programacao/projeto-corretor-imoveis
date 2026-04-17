<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\FilterOption;
use App\Support\FilterCatalog;
use App\Support\LocationHierarchy;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->only([
            'location',
            'child_location',
            'grandchild_location',
            'great_grandchild_location',
            'city',
            'neighborhood',
            'property_type',
            'purpose',
            'menu_category',
            'price_range',
            'bedrooms',
            'bathrooms',
            'min_price',
            'max_price',
            'search',
        ]);

        $highlightedProperties = Property::query()
            ->published()
            ->with('images')
            ->filter($filters)
            ->latest()
            ->take(9)
            ->get();

        $latestProperties = Property::query()
            ->published()
            ->with('images')
            ->latest()
            ->take(4)
            ->get();

        $bedroomOptions = FilterCatalog::numericOptions(FilterOption::GROUP_BEDROOMS);
        $bathroomOptions = FilterCatalog::numericOptions(FilterOption::GROUP_BATHROOMS);
        $purposeOptions = FilterCatalog::purposes();
        $priceRangeOptions = FilterCatalog::priceRanges();

        $filterOptions = [
            'locations' => LocationHierarchy::tree(),
            'location_labels' => LocationHierarchy::labels(),
            'types' => FilterCatalog::propertyTypes(),
            'purposes' => $purposeOptions->isNotEmpty()
                ? $purposeOptions
                : collect([['value' => 'venda', 'label' => 'Venda'], ['value' => 'aluguel', 'label' => 'Aluguel']]),
            'price_ranges' => $priceRangeOptions->isNotEmpty()
                ? $priceRangeOptions
                : collect([
                    ['value' => '0|250000', 'label' => 'Até R$ 250.000'],
                    ['value' => '250001|350000', 'label' => 'R$ 250.001 até R$ 350.000'],
                    ['value' => '350001|500000', 'label' => 'R$ 350.001 até R$ 500.000'],
                    ['value' => '500001|', 'label' => 'Acima de R$ 500.000'],
                ]),
            'bedroom_options' => $bedroomOptions !== [] ? $bedroomOptions : range(1, 10),
            'bathroom_options' => $bathroomOptions !== [] ? $bathroomOptions : range(1, 10),
        ];

        return view('home', [
            'highlightedProperties' => $highlightedProperties,
            'latestProperties' => $latestProperties,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
        ]);
    }
}
