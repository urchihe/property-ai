<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PropertyDescriptionService
{
    /**
     * @return array<int, array{description: string, seo_score: int}>
     */
    public function generate(Property $property, int $options = 1, bool $regenerate = false): array
    {
        $cacheKey = 'property_descriptions_'.$property->id;

        if (! $regenerate && Cache::has($cacheKey)) {
            /** @var array<int, array{description: string, seo_score: int}>|null $cached */
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                return $cached;
            }
        }

        $promptText = <<<PROMPT
            You are an expert real estate copywriter.
            Write a professional, SEO-optimized, and engaging property description.

            Details:
            - Title: {$property->title}
            - Type: {$property->property_type}
            - Location: {$property->location}
            - Price: {$property->price}
            - Key Features: {$property->key_features}
            - Tone: {$property->tone}
            PROMPT;

        $descriptions = [];

        for ($i = 0; $i < $options; $i++) {
            try {
                /** @var array<string, mixed> $response */
                $response = $this->callOpenAIResponses($promptText);
                $desc = $this->extractDescription($response);
                $descriptions[] = [
                    'description' => trim($desc),
                    'seo_score' => $this->calculateSeoScore($desc),
                ];
            } catch (\Throwable $e) {
                $descriptions[] = [
                    'description' => sprintf(
                        'Spacious %s in %s with modern amenities. %s',
                        $property->property_type,
                        $property->location,
                        (string) $e->getMessage()
                    ),
                    'seo_score' => 85,
                ];
            }
        }

        Cache::put($cacheKey, $descriptions, 86400); // 24h

        return $descriptions;
    }

    /**
     * Safely extract description from OpenAI response
     *
     * @param  array<string, mixed>  $response
     */
    protected function extractDescription(array $response): string
    {
        // Check if the response contains an API error
        if (isset($response['error']) && is_array($response['error'])) {
            /** @var string $errorMessage */
            $errorMessage = $response['error']['message'] ?? 'Unknown API error';

            return "Spacious property with modern amenities. {$errorMessage}";
        }

        // Check the structure of the output
        if (
            isset($response['output'])
            && is_array($response['output'])
            && count($response['output']) > 0
            && is_array($response['output'][0])
            && isset($response['output'][0]['content'])
            && is_array($response['output'][0]['content'])
            && count($response['output'][0]['content']) > 0
            && is_array($response['output'][0]['content'][0])
            && isset($response['output'][0]['content'][0]['text'])
            && is_string($response['output'][0]['content'][0]['text'])
        ) {
            return $response['output'][0]['content'][0]['text'];
        }

        // Fallback description if structure is invalid
        return 'Spacious property with modern amenities.';
    }

    /**
     * Call OpenAI Responses API
     *
     * @return array<mixed, mixed>
     *
     * @throws \Exception
     */
    protected function callOpenAIResponses(string $promptText): array
    {
        $apiKey = config('services.openai.key');

        if (! is_string($apiKey) || empty($apiKey)) {
            throw new \Exception('OpenAI API key is not set.');
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-3.5-turbo',
            'input' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'input_text',
                            'text' => $promptText,
                        ],
                    ],
                ],
            ],
        ]);

        $json = $response->json();

        // ensure $json is array<string, mixed>
        if (! is_array($json)) {
            throw new \Exception(
                "OpenAI Responses API error ({$response->status()}): ".(string) $response->body()
            );
        }

        return $json;
    }

    protected function calculateSeoScore(string $description): int
    {
        $score = 50;
        $length = str_word_count($description);

        if ($length > 120) {
            $score += 15;
        } elseif ($length > 80) {
            $score += 10;
        } elseif ($length > 50) {
            $score += 5;
        }

        $keywords = ['spacious', 'modern', 'luxury', 'affordable', 'family', 'investment', 'convenient'];
        foreach ($keywords as $word) {
            if (stripos($description, $word) !== false) {
                $score += 2;
            }
        }

        if (preg_match('/(beautiful|stunning|prime|exclusive)/i', $description)) {
            $score += 5;
        }

        return min(100, max(60, $score));
    }
}
