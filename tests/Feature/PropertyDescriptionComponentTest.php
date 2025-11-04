<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Services\PropertyDescriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyDescriptionComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_description_button_works()
    {
        // Create a mock service
        $mockService = $this->mock(PropertyDescriptionService::class);
        $mockService->shouldReceive('generate')
            ->once()
            ->andReturn([
                [
                    'description' => 'Spacious House in Lagos with modern amenities.',
                    'seo_score' => 90,
                ],
            ]);

        // Run the Livewire component and inject the mock
        $component = Livewire::test('property-description')
            ->set('title', 'My House')
            ->set('property_type', 'House')
            ->set('location', 'Lagos')
            ->set('price', 2500000)
            ->set('key_features', 'Spacious, Modern')
            ->set('tone', 'Formal')
            ->call('generateDescription', $mockService);

        // Assert component state
        $component->assertSet('ai_description', 'Spacious House in Lagos with modern amenities.');
        $component->assertSet('seo_score', 90);

        // Assert database
        $this->assertDatabaseHas('properties', [
            'title' => 'My House',
            'ai_description' => 'Spacious House in Lagos with modern amenities.',
        ]);
    }

    public function test_regenerate_description_updates_db()
    {
        $property = Property::factory()->create([
            'title' => 'My House',
            'property_type' => 'House',
            'location' => 'Lagos',
            'price' => 2500000,
            'key_features' => 'Spacious, Modern',
            'tone' => 'Formal',
        ]);

        // Mock the service
        $mockService = $this->mock(PropertyDescriptionService::class);
        $mockService->shouldReceive('generate')
            ->once()
            ->withArgs(function ($argProperty, $options, $regenerate) use ($property) {
                // Ensure it's the exact property instance and regenerate flag is true
                return $argProperty->id === $property->id && $options === 1 && $regenerate === true;
            })
            ->andReturn([
                [
                    'description' => 'Updated Spacious House in Lagos with modern amenities.',
                    'seo_score' => 92,
                ],
            ]);

        $component = Livewire::test('property-description')
            ->set('property', $property) // VERY IMPORTANT
            ->call('regenerateDescription', $mockService);

        $component->assertSet('ai_description', 'Updated Spacious House in Lagos with modern amenities.');

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'ai_description' => 'Updated Spacious House in Lagos with modern amenities.',
        ]);
    }
}
