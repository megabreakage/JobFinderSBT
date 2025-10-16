@props([
    'label' => null,
    'name' => null,
    'placeholder' => 'Search...',
    'hint' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'searchUrl' => null,
    'searchMethod' => null,
    'minChars' => 2,
    'debounce' => 300,
    'displayKey' => 'name',
    'valueKey' => 'id',
    'noResultsText' => 'No results found',
    'loadingText' => 'Searching...',
])

@php
$componentId = 'search-select-' . uniqid();
@endphp

<div class="mb-4" x-data="{
    open: false,
    search: '',
    selected: null,
    selectedDisplay: '',
    results: [],
    loading: false,
    highlightedIndex: -1,
    
    init() {
        this.$watch('search', (value) => {
            if (value.length >= {{ $minChars }}) {
                this.performSearch(value);
            } else {
                this.results = [];
                this.open = false;
            }
        });
    },
    
    async performSearch(query) {
        this.loading = true;
        this.open = true;
        
        try {
            @if($searchUrl)
                const response = await fetch(`{{ $searchUrl }}?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                this.results = data.results || data;
            @elseif($searchMethod)
                @this.call('{{ $searchMethod }}', query).then(results => {
                    this.results = results;
                });
            @endif
        } catch (error) {
            console.error('Search error:', error);
            this.results = [];
        } finally {
            this.loading = false;
            this.highlightedIndex = -1;
        }
    },
    
    selectItem(item) {
        this.selected = item['{{ $valueKey }}'];
        this.selectedDisplay = item['{{ $displayKey }}'];
        this.search = item['{{ $displayKey }}'];
        this.open = false;
        this.results = [];
        
        // Update Livewire model if wire:model is present
        @if($attributes->wire('model'))
            @this.set('{{ $attributes->wire('model')->value() }}', this.selected);
        @endif
        
        // Dispatch custom event
        this.$dispatch('item-selected', { item: item });
    },
    
    clearSelection() {
        this.selected = null;
        this.selectedDisplay = '';
        this.search = '';
        this.results = [];
        this.open = false;
        
        @if($attributes->wire('model'))
            @this.set('{{ $attributes->wire('model')->value() }}', null);
        @endif
    },
    
    handleKeydown(event) {
        if (!this.open) return;
        
        switch(event.key) {
            case 'ArrowDown':
                event.preventDefault();
                this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.results.length - 1);
                this.scrollToHighlighted();
                break;
            case 'ArrowUp':
                event.preventDefault();
                this.highlightedIndex = Math.max(this.highlightedIndex - 1, 0);
                this.scrollToHighlighted();
                break;
            case 'Enter':
                event.preventDefault();
                if (this.highlightedIndex >= 0 && this.results[this.highlightedIndex]) {
                    this.selectItem(this.results[this.highlightedIndex]);
                }
                break;
            case 'Escape':
                this.open = false;
                this.highlightedIndex = -1;
                break;
        }
    },
    
    scrollToHighlighted() {
        this.$nextTick(() => {
            const highlighted = this.$refs.dropdown?.querySelector('[data-highlighted=true]');
            if (highlighted) {
                highlighted.scrollIntoView({ block: 'nearest' });
            }
        });
    }
}" @click.away="open = false" @keydown="handleKeydown($event)">

    @if($label)
        <label for="{{ $componentId }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <div class="relative">
            <input
                type="text"
                id="{{ $componentId }}"
                x-model="search"
                @focus="search.length >= {{ $minChars }} && results.length > 0 ? open = true : null"
                @input.debounce.{{ $debounce }}ms="search"
                placeholder="{{ $placeholder }}"
                autocomplete="off"
                {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10' . ($error ? ' border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '')]) }}
                @if($required) required @endif
                @if($disabled) disabled @endif
            />
            
            <!-- Loading spinner or clear button -->
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <button
                    type="button"
                    x-show="selected && !loading"
                    @click="clearSelection()"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <x-icon name="x-mark" size="sm" />
                </button>
                
                <svg
                    x-show="loading"
                    class="animate-spin h-4 w-4 text-gray-400"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    x-cloak
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                <x-icon
                    name="chevron-down"
                    size="sm"
                    class="text-gray-400"
                    x-show="!loading && !selected"
                />
            </div>
        </div>

        <!-- Hidden input for form submission -->
        <input type="hidden" name="{{ $name }}" x-model="selected" />

        <!-- Dropdown results -->
        <div
            x-show="open && (results.length > 0 || loading)"
            x-ref="dropdown"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
            x-cloak
        >
            <!-- Loading state -->
            <div x-show="loading" class="px-4 py-2 text-sm text-gray-500">
                {{ $loadingText }}
            </div>

            <!-- Results -->
            <template x-for="(item, index) in results" :key="item['{{ $valueKey }}']">
                <div
                    @click="selectItem(item)"
                    :data-highlighted="highlightedIndex === index"
                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50': highlightedIndex === index }"
                >
                    <span class="block truncate" x-text="item['{{ $displayKey }}']"></span>
                    
                    <!-- Selected checkmark -->
                    <span
                        x-show="selected === item['{{ $valueKey }}']"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600"
                    >
                        <x-icon name="check" size="sm" />
                    </span>
                </div>
            </template>

            <!-- No results -->
            <div
                x-show="!loading && results.length === 0 && search.length >= {{ $minChars }}"
                class="px-4 py-2 text-sm text-gray-500"
            >
                {{ $noResultsText }}
            </div>
        </div>
    </div>

    @if($hint && !$error)
        <p class="mt-1 text-sm text-gray-500">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>

@once
    @push('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @endpush
@endonce
