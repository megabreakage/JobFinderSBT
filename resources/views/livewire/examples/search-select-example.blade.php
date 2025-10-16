<div class="max-w-4xl mx-auto p-6 space-y-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Search Select Component Examples</h1>
        <p class="text-gray-600">Dynamic autocomplete search with dropdown selection</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Example 1: Search Users with Livewire Method --}}
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-medium">Search Users (Livewire)</h3>
            </x-slot:header>

            <x-form.search-select
                name="user_id"
                label="Select User"
                placeholder="Type to search users..."
                hint="Search by name or email"
                wire:model="selectedUserId"
                searchMethod="searchUsers"
                displayKey="name"
                valueKey="id"
                :minChars="2"
            />

            @if($selectedUserId)
                <div class="mt-4 p-3 bg-green-50 rounded-md">
                    <p class="text-sm text-green-800">
                        <strong>Selected User ID:</strong> {{ $selectedUserId }}
                    </p>
                </div>
            @endif
        </x-ui.card>

        {{-- Example 2: Search Companies --}}
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-medium">Search Companies</h3>
            </x-slot:header>

            <x-form.search-select
                name="company_id"
                label="Select Company"
                placeholder="Type to search companies..."
                hint="Search by company name"
                wire:model="selectedCompanyId"
                searchMethod="searchCompanies"
                displayKey="name"
                valueKey="id"
                :minChars="1"
                :debounce="500"
            />

            @if($selectedCompanyId)
                <div class="mt-4 p-3 bg-blue-50 rounded-md">
                    <p class="text-sm text-blue-800">
                        <strong>Selected Company ID:</strong> {{ $selectedCompanyId }}
                    </p>
                </div>
            @endif
        </x-ui.card>

        {{-- Example 3: Search Jobs --}}
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-medium">Search Jobs</h3>
            </x-slot:header>

            <x-form.search-select
                name="job_id"
                label="Select Job"
                placeholder="Type to search jobs..."
                hint="Search by job title or location"
                wire:model="selectedJobId"
                searchMethod="searchJobs"
                displayKey="title"
                valueKey="id"
                :minChars="2"
                noResultsText="No jobs found matching your search"
                loadingText="Searching jobs..."
            />

            @if($selectedJobId)
                <div class="mt-4 p-3 bg-indigo-50 rounded-md">
                    <p class="text-sm text-indigo-800">
                        <strong>Selected Job ID:</strong> {{ $selectedJobId }}
                    </p>
                </div>
            @endif
        </x-ui.card>

        {{-- Example 4: API Endpoint Search --}}
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-medium">API Endpoint Search</h3>
            </x-slot:header>

            <x-form.search-select
                name="api_item"
                label="Search via API"
                placeholder="Type to search..."
                hint="This example uses a REST API endpoint"
                searchUrl="/api/search"
                displayKey="name"
                valueKey="id"
                :minChars="3"
            />

            <div class="mt-4 p-3 bg-yellow-50 rounded-md">
                <p class="text-xs text-yellow-800">
                    <strong>Note:</strong> This example requires an API endpoint at <code>/api/search?q=query</code>
                </p>
            </div>
        </x-ui.card>
    </div>

    {{-- Features Section --}}
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-medium">Component Features</h3>
        </x-slot:header>

        <div class="prose prose-sm max-w-none">
            <ul class="space-y-2">
                <li><strong>Real-time Search:</strong> Debounced input for efficient searching</li>
                <li><strong>Keyboard Navigation:</strong> Use arrow keys to navigate, Enter to select, Escape to close</li>
                <li><strong>Loading States:</strong> Visual feedback during search operations</li>
                <li><strong>Clear Selection:</strong> Easy-to-use clear button when item is selected</li>
                <li><strong>Customizable:</strong> Configure min characters, debounce time, display keys, etc.</li>
                <li><strong>Livewire Integration:</strong> Full wire:model support for reactive updates</li>
                <li><strong>API Support:</strong> Can search via Livewire methods or REST API endpoints</li>
                <li><strong>Accessible:</strong> Proper ARIA attributes and keyboard support</li>
            </ul>
        </div>
    </x-ui.card>

    {{-- Code Example --}}
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-medium">Usage Example</h3>
        </x-slot:header>

        <pre class="bg-gray-50 p-4 rounded-md overflow-x-auto text-xs"><code>&lt;x-form.search-select
    name="user_id"
    label="Select User"
    placeholder="Type to search..."
    wire:model="selectedUserId"
    searchMethod="searchUsers"
    displayKey="name"
    valueKey="id"
    :minChars="2"
    :debounce="300"
/&gt;</code></pre>
    </x-ui.card>
</div>
