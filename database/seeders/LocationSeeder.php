<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terms = config('location_hierarchy.terms', []);

        $this->syncTree($terms, null, 0);
    }

    /**
     * @param array<int, array<string, mixed>> $nodes
     */
    private function syncTree(array $nodes, ?int $parentId, int $depth): void
    {
        foreach ($nodes as $index => $node) {
            $name = (string) ($node['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $slug = (string) ($node['slug'] ?? Str::slug($name));

            $location = Location::updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => $parentId,
                    'name' => $name,
                    'depth' => $depth,
                    'sort_order' => $index,
                ]
            );

            $children = $node['children'] ?? [];
            if (is_array($children) && $children !== []) {
                $this->syncTree($children, $location->id, $depth + 1);
            }
        }
    }
}
