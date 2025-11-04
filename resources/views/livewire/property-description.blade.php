<div
    class="max-w-3xl mx-auto my-10 p-6 md:p-8 bg-white bg-cover bg-center shadow-lg rounded-2xl border border-gray-200 relative overflow-hidden"
    x-data="{ loading: @entangle('loading'), description: @entangle('ai_description') }"
>
    <!-- Overlay for readability -->
    <div class="absolute inset-0 bg-white/90 dark:bg-gray-800/70"></div>

    <!-- Form container -->
    <div class="relative z-10">
        <!-- Title -->
        <h1 class="text-3xl md:text-4xl font-bold text-blue-800 mb-6 text-center">
            AI Property Description Generator
        </h1>

        <!-- Form Fields -->
        <form wire:submit.prevent="generateDescription" class="space-y-5">

            <!-- Title -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Title</label>
                <input
                    type="text"
                    wire:model.debounce.500ms="title"
                    @input="$wire.clearError('title')"
                    class="w-full border rounded-lg px-4 py-3 md:py-4 focus:ring-2 focus:ring-blue-500 focus:outline-none
                           @error('title') border-red-500 focus:ring-red-500 @enderror"
                    placeholder="e.g. Modern 3-bedroom apartment"
                >
                @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Property Type -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Property Type</label>
                <select
                    wire:model.debounce.500ms="property_type"
                    @change="$wire.clearError('property_type')"
                    class="w-full border rounded-lg px-4 py-3 md:py-4 focus:ring-2 focus:ring-blue-500 focus:outline-none
                           @error('property_type') border-red-500 focus:ring-red-500 @enderror"
                >
                    <option value="">-- Select Property Type --</option>
                    @foreach(['House','Flat','Land','Commercial'] as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
                @error('property_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Location -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Location</label>
                <input
                    type="text"
                    wire:model.debounce.500ms="location"
                    @input="$wire.clearError('location')"
                    class="w-full border rounded-lg px-4 py-3 md:py-4 focus:ring-2 focus:ring-blue-500 focus:outline-none
                           @error('location') border-red-500 focus:ring-red-500 @enderror"
                    placeholder="e.g. Lekki, Lagos"
                >
                @error('location')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Price -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Price (₦)</label>
                <input
                    type="number"
                    wire:model.debounce.500ms="price"
                    @input="$wire.clearError('price')"
                    class="w-full border rounded-lg px-4 py-3 md:py-4 focus:ring-2 focus:ring-blue-500 focus:outline-none
                           @error('price') border-red-500 focus:ring-red-500 @enderror"
                    placeholder="e.g. 25000000"
                >
                @error('price')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Key Features -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Key Features</label>
                <textarea
                    wire:model.debounce.500ms="key_features"
                    @input="$wire.clearError('key_features')"
                    rows="5"
                    class="w-full border rounded-lg px-4 py-3 md:py-4 focus:ring-2 focus:ring-blue-500 focus:outline-none
                           @error('key_features') border-red-500 focus:ring-red-500 @enderror"
                    placeholder="e.g. Spacious living room, Modern kitchen, Parking space"
                ></textarea>
                @error('key_features')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Tone -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Tone</label>
                <select
                    wire:model.debounce.500ms="tone"
                    @change="$wire.clearError('tone')"
                    class="w-full border rounded-lg px-4 py-3 md:py-4 focus:ring-2 focus:ring-blue-500 focus:outline-none
                           @error('tone') border-red-500 focus:ring-red-500 @enderror"
                >
                    <option value="Formal">Formal</option>
                    <option value="Casual">Casual</option>
                </select>
                @error('tone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button
                    type="submit"
                    :disabled="loading"
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-semibold px-6 py-3 md:py-4 rounded-lg shadow transition-transform transform hover:scale-105"
                >
                    <span x-show="!loading">Generate AI Description</span>
                    <span x-show="loading">Generating...</span>
                </button>
            </div>
        </form>

        <!-- AI Description -->
        <div
            x-data="{ description: @entangle('ai_description'), loading: @entangle('loading'), toast: null }"
            x-on:notify.window="toast = $event.detail.message; setTimeout(() => toast = null, 2000)"
        >
            <div
                class="mt-8 p-5 bg-gray-50 border border-gray-200 rounded-lg"
                x-show="!loading && description"
                x-transition
            >
                <h2 class="font-semibold text-gray-800 mb-2">AI Generated Description</h2>
                <p class="text-gray-700 whitespace-pre-line" x-text="description"></p>

                <div class="flex flex-wrap gap-2 mt-3">
                    <button
                        x-on:click="navigator.clipboard.writeText(description); $dispatch('notify', { message: 'Copied to clipboard!' })"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition-transform transform hover:scale-105"
                    >
                        Copy to Clipboard
                    </button>

                    <button
                        wire:click="regenerateDescription"
                        wire:loading.attr="disabled"
                        wire:target="regenerateDescription"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg shadow transition-transform transform hover:scale-105"
                    >
                        <span wire:loading.remove wire:target="regenerateDescription">Regenerate</span>
                        <span wire:loading wire:target="regenerateDescription">Regenerating...</span>
                    </button>
                </div>
            </div>

            <!-- Toast Notification -->
            <div
                x-show="toast"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50"
                x-text="toast"
            ></div>
        </div>

        <!-- Loading placeholder -->
        <div
            class="mt-8 p-5 bg-gray-50 border border-gray-200 rounded-lg text-gray-500 italic text-center"
            x-show="loading"
            x-transition
        >
            Generating description...
        </div>
    </div>
</div>
