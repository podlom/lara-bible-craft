<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('View Source') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 bg-white shadow-sm rounded-lg p-6">
            <p><strong>{{ __('Title') }}:</strong> {{ $source->title }}</p>
            <p><strong>{{ __('Authors') }}:</strong> {{ $source->authors }}</p>
            <p><strong>{{ __('Year') }}:</strong> {{ $source->year }}</p>
            <p><strong>{{ __('Type') }}:</strong> {{ $source->type }}</p>
            <p><strong>{{ __('Formatted Entry') }}:</strong> {{ $source->formatted_entry }}</p>

            <div class="mt-4 flex gap-2">
                @can('update', $source)
                    <a href="{{ route('sources.edit', $source) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ __('Edit') }}
                    </a>
                @endcan

                @can('delete', $source)
                    <form method="POST" action="{{ route('sources.destroy', $source) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('{{ __('Are you sure?') }}')"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
