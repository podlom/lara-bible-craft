{{-- resources/views/sources/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.sources') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <form method="GET" action="{{ route('sources.index') }}">
                    <input name="author" value="{{ request('author') }}" placeholder="{{ __('messages.filter_by_author') }}"
                           class="border rounded px-2 py-1">
                    <button type="submit"
                            class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ __('messages.filter') }}
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <a href="{{ route('sources.create') }}"
                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('messages.create_new_source') }}
                    </a>
                    <table class="mt-4 w-full">
                        <thead>
                        <tr class="text-left border-b">
                            <th>{{ __('messages.title') }}</th>
                            <th>{{ __('messages.authors') }}</th>
                            <th>{{ __('messages.year') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sources as $source)
                            <tr class="border-b">
                                <td><a href="{{ route('sources.show', $source) }}"
                                       class="text-blue-600 hover:underline">{{ $source->title }}</a></td>
                                <td>{{ $source->authors_string }}</td>
                                <td>{{ $source->year }}</td>
                                <td class="space-x-2">
                                    @can('update', $source)
                                        <a href="{{ route('sources.edit', $source) }}"
                                           class="text-yellow-600 hover:underline">{{ __('messages.edit') }}</a>
                                    @endcan
                                    @can('delete', $source)
                                        <form action="{{ route('sources.destroy', $source) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline"
                                                    onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                                {{ __('messages.delete') }}
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $sources->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
