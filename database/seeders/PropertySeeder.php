<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = [
            [
                'title' => 'Novo Mundo Cury 2 e 3 Dormitórios',
                'code' => 'CP-1001',
                'property_type' => 'Apartamento',
                'purpose' => 'venda',
                'menu_category' => 'lancamento',
                'city' => 'São Paulo',
                'state' => 'SP',
                'neighborhood' => 'Vila Carrão',
                'location_slug' => 'vila-carrao',
                'address' => 'Rua Serra de Botucatu, 900',
                'price' => 235000,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'parking_spaces' => 1,
                'area' => 58,
                'is_featured' => true,
                'description' => 'Projeto residencial com lazer completo, fácil acesso ao metrô e condições especiais.',
                'features' => 'Piscina|Academia|Playground|Portaria 24h',
                'featured_image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
                'latitude' => -23.561743,
                'longitude' => -46.558094,
                'gallery' => [
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'title' => 'Marcos Freire Cury 2 Dormitórios',
                'code' => 'CP-1002',
                'property_type' => 'Apartamento',
                'purpose' => 'venda',
                'menu_category' => 'breve-lancamento',
                'city' => 'São Paulo',
                'state' => 'SP',
                'neighborhood' => 'Campo Limpo',
                'location_slug' => 'campo-limpo',
                'address' => 'Estrada de Itapecerica, 1300',
                'price' => 253000,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'parking_spaces' => 1,
                'area' => 52,
                'is_featured' => true,
                'description' => 'Apartamento pronto para morar, planta inteligente e condomínio com segurança.',
                'features' => 'Churrasqueira|Coworking|Salão de festas|Espaço pet',
                'featured_image' => 'https://images.unsplash.com/photo-1560185007-cde436f6a4d0?auto=format&fit=crop&w=1200&q=80',
                'latitude' => -23.641300,
                'longitude' => -46.766407,
                'gallery' => [
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600566753104-685f3f61c999?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'title' => 'Barra Funda 800 Cury 1 e 2 Dormitórios',
                'code' => 'CP-1003',
                'property_type' => 'Apartamento',
                'purpose' => 'venda',
                'menu_category' => 'imovel-pronto',
                'city' => 'São Paulo',
                'state' => 'SP',
                'neighborhood' => 'Barra Funda',
                'location_slug' => 'barra-funda',
                'address' => 'Rua do Bosque, 2100',
                'price' => 234000,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'parking_spaces' => 1,
                'area' => 49,
                'is_featured' => true,
                'description' => 'Empreendimento moderno em região central, ideal para quem quer mobilidade.',
                'features' => 'Piscina|Mini mercado|Bicicletário|Lavanderia',
                'featured_image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80',
                'latitude' => -23.525317,
                'longitude' => -46.661453,
                'gallery' => [
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1493666438817-866a91353ca9?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'title' => 'Cidade Parque Guarapiranga',
                'code' => 'CP-1004',
                'property_type' => 'Apartamento',
                'purpose' => 'venda',
                'menu_category' => 'imovel-pronto',
                'city' => 'São Paulo',
                'state' => 'SP',
                'neighborhood' => 'Jardim Ângela',
                'location_slug' => 'zona-sul',
                'address' => 'Estrada do M Boi Mirim, 9200',
                'price' => null,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'parking_spaces' => 1,
                'area' => 45,
                'is_featured' => false,
                'description' => 'Condomínio clube com valor acessível e opções de financiamento facilitado.',
                'features' => 'Quadra|Praça|Salão de jogos|Portaria',
                'featured_image' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=1200&q=80',
                'latitude' => -23.737052,
                'longitude' => -46.743840,
                'gallery' => [
                    'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'title' => 'Parque das Nações Cury',
                'code' => 'CP-1005',
                'property_type' => 'Apartamento',
                'purpose' => 'venda',
                'menu_category' => 'lancamento',
                'city' => 'Guarulhos',
                'state' => 'SP',
                'neighborhood' => 'Vila Endres',
                'location_slug' => 'guarulhos',
                'address' => 'Avenida Guarulhos, 3100',
                'price' => 232000,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'parking_spaces' => 1,
                'area' => 51,
                'is_featured' => true,
                'description' => 'Excelente opção para primeira moradia em localização com comércio completo.',
                'features' => 'Piscina|Pet place|Brinquedoteca|Portaria 24h',
                'featured_image' => 'https://images.unsplash.com/photo-1572120360610-d971b9d7767c?auto=format&fit=crop&w=1200&q=80',
                'latitude' => -23.470373,
                'longitude' => -46.541779,
                'gallery' => [
                    'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'title' => 'Lançamento Cury Arujá',
                'code' => 'CP-1006',
                'property_type' => 'Apartamento',
                'purpose' => 'venda',
                'menu_category' => 'breve-lancamento',
                'city' => 'Arujá',
                'state' => 'SP',
                'neighborhood' => 'Jardim Real',
                'location_slug' => 'aruja',
                'address' => 'Avenida Mário Covas, 850',
                'price' => 266000,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'parking_spaces' => 1,
                'area' => 60,
                'is_featured' => true,
                'description' => 'Lançamento com condição especial de entrada parcelada e lazer premium.',
                'features' => 'Piscina aquecida|Academia|Espaço gourmet|Coworking',
                'featured_image' => 'https://images.unsplash.com/photo-1600047509782-20d39509f26d?auto=format&fit=crop&w=1200&q=80',
                'latitude' => -23.387819,
                'longitude' => -46.320637,
                'gallery' => [
                    'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'title' => 'Lançamento na Lapa',
                'code' => 'CP-1007',
                'property_type' => 'Apartamento',
                'purpose' => 'venda',
                'menu_category' => 'lancamento',
                'city' => 'São Paulo',
                'state' => 'SP',
                'neighborhood' => 'Lapa',
                'location_slug' => 'lapa',
                'address' => 'Rua Clemente Álvares, 270',
                'price' => 235000,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'parking_spaces' => 1,
                'area' => 47,
                'is_featured' => false,
                'description' => 'Unidades compactas e modernas em região valorizada da zona oeste.',
                'features' => 'Salão de festas|Pet care|Lavanderia|Playground',
                'featured_image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80',
                'latitude' => -23.521972,
                'longitude' => -46.705147,
                'gallery' => [
                    'https://images.unsplash.com/photo-1600573472592-401b489a3cdc?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600607687644-c7f34b66d8f5?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'title' => 'Matriz Lapa Cury 1 e 2 Dormitórios',
                'code' => 'CP-1008',
                'property_type' => 'Apartamento',
                'purpose' => 'aluguel',
                'menu_category' => 'para-alugar',
                'city' => 'São Paulo',
                'state' => 'SP',
                'neighborhood' => 'Lapa',
                'location_slug' => 'lapa',
                'address' => 'Rua Cerro Corá, 108',
                'price' => null,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'parking_spaces' => 1,
                'area' => 54,
                'is_featured' => false,
                'description' => 'Excelente custo-benefício para morar perto de tudo, com fácil acesso às marginais.',
                'features' => 'Portaria 24h|Mini market|Espaço zen|Bicicletário',
                'featured_image' => 'https://images.unsplash.com/photo-1516455590571-18256e5bb9ff?auto=format&fit=crop&w=1200&q=80',
                'latitude' => -23.532412,
                'longitude' => -46.699963,
                'gallery' => [
                    'https://images.unsplash.com/photo-1600566752355-35792bedcfea?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb3?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'title' => 'Lançamento Av. Cecília Lottenberg',
                'code' => 'CP-1009',
                'property_type' => 'Apartamento',
                'purpose' => 'venda',
                'menu_category' => 'breve-lancamento',
                'city' => 'São Paulo',
                'state' => 'SP',
                'neighborhood' => 'Santo Amaro',
                'location_slug' => 'santo-amaro',
                'address' => 'Av. Cecília Lottenberg, 120',
                'price' => null,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'parking_spaces' => 1,
                'area' => 62,
                'is_featured' => true,
                'description' => 'Projeto premium na zona sul, com plantas amplas e área de lazer completa.',
                'features' => 'Piscina coberta|Academia premium|Salão gourmet|Espaço delivery',
                'featured_image' => 'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80',
                'latitude' => -23.633120,
                'longitude' => -46.706531,
                'gallery' => [
                    'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
        ];

        foreach ($properties as $propertyData) {
            $gallery = $propertyData['gallery'];
            unset($propertyData['gallery']);

            $locationSlug = (string) ($propertyData['location_slug'] ?? '');
            unset($propertyData['location_slug']);

            $propertyData['location_id'] = Location::query()->where('slug', $locationSlug)->value('id');

            $propertyData['slug'] = Str::slug($propertyData['title']).'-'.$propertyData['code'];

            $property = Property::updateOrCreate(
                ['code' => $propertyData['code']],
                $propertyData
            );

            foreach ($gallery as $position => $imagePath) {
                $property->images()->updateOrCreate(
                    ['property_id' => $property->id, 'position' => $position],
                    ['path' => $imagePath, 'caption' => $property->title.' - imagem '.($position + 1)]
                );
            }
        }
    }
}
