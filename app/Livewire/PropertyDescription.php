<?php

namespace App\Livewire;

use App\Models\Property;
use App\Services\PropertyDescriptionService;
use Livewire\Component;

class PropertyDescription extends Component
{
    public ?string $title = null;

    public ?string $property_type = null;

    public ?string $location = null;

    public ?float $price = null;

    public ?string $key_features = null;

    public string $tone = 'Formal';

    public ?string $ai_description = null;

    public ?int $seo_score = null;

    public bool $loading = false;

    public ?Property $property = null;

    /**
     * @var array<string, mixed>
     */
    protected $rules = [
        'title' => 'required|string|max:255',
        'property_type' => 'required|in:House,Flat,Land,Commercial',
        'location' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'key_features' => 'required|string',
        'tone' => 'required|in:Formal,Casual',
    ];

    /**
     * Handle description generation with validation.
     */
    public function generateDescription(PropertyDescriptionService $descriptionService): void
    {
        /** @var array<string, mixed> $validated */
        $validated = $this->validate();

        $this->loading = true;

        $property = Property::create($validated);
        $this->reset(['title', 'property_type', 'location', 'price', 'key_features', 'tone']);

        $this->property = $property;

        $results = $descriptionService->generate($property, 1);

        /** @var array{description: string, seo_score: int}|null $best */
        $best = collect($results)->sortByDesc('seo_score')->first();

        $description = $best['description'] ?? 'No description generated';
        $seoScore = $best['seo_score'] ?? null;

        $property->update(['ai_description' => $description, 'seo_score' => $seoScore]);

        $this->ai_description = $description;
        $this->seo_score = $seoScore;
        $this->loading = false;
    }

    /**
     * Regenerate AI description for the selected property.
     */
    public function regenerateDescription(PropertyDescriptionService $descriptionService): void
    {
        $this->loading = true;
        $this->ai_description = '';
        if (! $this->property) {
            $this->loading = false;

            return;
        }

        $results = $descriptionService->generate($this->property, 1, true);

        /** @var array{description: string, seo_score: int}|null $best */
        $best = collect($results)->sortByDesc('seo_score')->first();

        $description = $best['description'] ?? 'No description generated';
        $seoScore = $best['seo_score'] ?? null;

        $this->property->update(['ai_description' => $description, 'seo_score' => $seoScore]);

        $this->ai_description = $description;
        $this->seo_score = $seoScore;
        $this->loading = false;
    }

    /**
     * Validate each field as it's updated
     */
    public function updated(string $propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function clearError(string $field): void
    {
        $this->resetErrorBag($field);
    }

    /**
     * Render the Livewire component.
     */
    public function render(): mixed
    {
        return view('livewire.property-description');
    }
}
