<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Services\PropertyDescriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PropertyDescriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_creates_description_and_caches()
    {
        // Fake the OpenAI Responses API with multiple consecutive responses
        Http::fake([
            'https://api.openai.com/v1/responses' => Http::sequence()
                ->push([
                    'output' => [
                        [
                            'content' => [
                                ['type' => 'output_text', 'text' => 'Spacious House in Lagos with modern amenities.'],
                            ],
                        ],
                    ],
                ], 200)
                ->push([
                    'output' => [
                        [
                            'content' => [
                                ['type' => 'output_text', 'text' => 'Modern House in Lagos with great features.'],
                            ],
                        ],
                    ],
                ], 200),
        ]);

        $service = new PropertyDescriptionService;

        $property = Property::factory()->create([
            'title' => 'Test House',
            'property_type' => 'House',
            'location' => 'Lagos',
            'price' => 1000000,
            'key_features' => 'Spacious, Modern',
            'tone' => 'Formal',
        ]);

        // First generate (should produce 2 descriptions and cache them)
        $results = $service->generate($property, 2, true); // true to bypass cache

        $this->assertCount(2, $results);
        $this->assertStringContainsString('House', $results[0]['description']);
        $this->assertStringContainsString('House', $results[1]['description']);
        $this->assertTrue(Cache::has('property_descriptions_'.$property->id));

        // Regenerate should bypass cache and produce 1 new description
        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output' => [
                    [
                        'content' => [
                            ['type' => 'output_text', 'text' => 'Another House description for regeneration.'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $results2 = $service->generate($property, 1, true);
        $this->assertCount(1, $results2);
        $this->assertStringContainsString('House', $results2[0]['description']);
    }
}
