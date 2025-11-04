<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'property_type' => 'required|in:House,Flat,Land,Commercial',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'key_features' => 'required|string',
            'tone' => 'required|in:Formal,Casual',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Property title is required.',
            'property_type.required' => 'Please select a property type.',
            'location.required' => 'Property location is required.',
            'price.required' => 'Price is required.',
            'key_features.required' => 'Key features are required.',
        ];
    }
}
