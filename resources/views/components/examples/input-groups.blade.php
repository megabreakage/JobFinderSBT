{{-- 
    Input Group Examples
    This file demonstrates various ways to use input grouping with form components
--}}

<div class="max-w-2xl mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Input Group Examples</h1>

    {{-- Example 1: Simple prepend text --}}
    <x-form.input
        name="website"
        label="Website URL"
        placeholder="example.com"
        hint="Enter your website without the protocol"
    >
        <x-slot:prepend>https://</x-slot:prepend>
    </x-form.input>

    {{-- Example 2: Simple append text --}}
    <x-form.input
        name="email_domain"
        label="Email Username"
        placeholder="john.doe"
    >
        <x-slot:append>@company.com</x-slot:append>
    </x-form.input>

    {{-- Example 3: Prepend icon --}}
    <x-form.input
        name="username"
        label="Username"
        placeholder="Enter username"
    >
        <x-slot:prepend>
            <x-icon name="user" size="sm" />
        </x-slot:prepend>
    </x-form.input>

    {{-- Example 4: Both prepend and append --}}
    <x-form.input
        name="price"
        label="Product Price"
        type="number"
        placeholder="0.00"
    >
        <x-slot:prepend>$</x-slot:prepend>
        <x-slot:append>USD</x-slot:append>
    </x-form.input>

    {{-- Example 5: Select with prepend icon --}}
    <x-form.select
        name="country"
        label="Country"
        :options="[
            'us' => 'United States',
            'uk' => 'United Kingdom',
            'ca' => 'Canada',
        ]"
        placeholder="Select a country"
    >
        <x-slot:prepend>
            <x-icon name="map-pin" size="sm" />
        </x-slot:prepend>
    </x-form.select>

    {{-- Example 6: Advanced input group with multiple elements --}}
    <x-form.input-group label="Search Jobs" hint="Search by title, keywords, or location">
        <x-form.input-addon position="prepend">
            <x-icon name="magnifying-glass" size="sm" />
        </x-form.input-addon>
        <input
            type="text"
            name="search"
            placeholder="Job title, keywords..."
            class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        />
        <x-ui.button class="rounded-l-none">
            Search
        </x-ui.button>
    </x-form.input-group>

    {{-- Example 7: Price range with multiple inputs --}}
    <x-form.input-group label="Salary Range" hint="Enter your expected salary range">
        <x-form.input-addon position="prepend">$</x-form.input-addon>
        <input
            type="number"
            name="min_salary"
            placeholder="Min"
            class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm rounded-none"
        />
        <x-form.input-addon position="append">to</x-form.input-addon>
        <input
            type="number"
            name="max_salary"
            placeholder="Max"
            class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm rounded-none"
        />
        <x-form.input-addon position="append">per year</x-form.input-addon>
    </x-form.input-group>

    {{-- Example 8: Phone number with country code --}}
    <x-form.input-group label="Phone Number">
        <x-form.input-addon position="prepend">
            <x-icon name="phone" size="sm" />
        </x-form.input-addon>
        <select
            name="country_code"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm rounded-none"
        >
            <option value="+1">+1</option>
            <option value="+44">+44</option>
            <option value="+91">+91</option>
        </select>
        <input
            type="tel"
            name="phone"
            placeholder="123-456-7890"
            class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm rounded-r-md"
        />
    </x-form.input-group>

    {{-- Example 9: File size input --}}
    <x-form.input
        name="file_size"
        label="Maximum File Size"
        type="number"
        placeholder="10"
    >
        <x-slot:append>
            <select class="border-0 bg-transparent text-gray-500 sm:text-sm">
                <option>KB</option>
                <option>MB</option>
                <option>GB</option>
            </select>
        </x-slot:append>
    </x-form.input>

    {{-- Example 10: Percentage input --}}
    <x-form.input
        name="discount"
        label="Discount"
        type="number"
        placeholder="0"
        min="0"
        max="100"
    >
        <x-slot:append>%</x-slot:append>
    </x-form.input>
</div>
