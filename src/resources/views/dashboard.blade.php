<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">
                        {{ __('Your Bibliography Lists') }}
                    </h3>

                    @if ($bibliographies->isEmpty())
                        <p class="text-gray-600">{{ __('You have no bibliographies yet.') }}</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($bibliographies as $biblio)
                                <li class="border p-4 rounded-md shadow-sm bg-gray-50 hover:bg-gray-100">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $biblio->id }}. {{ $biblio->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ __('Created at') }}: {{ $biblio->created_at->format('Y-m-d') }}</p>
                                            <p class="text-sm text-gray-600"><a href="{{ route('sources.index', ['bibliography' => $biblio->id]) }}" class="inline-block px-4 py-2 bg-blue-600 text-blue-600 rounded hover:bg-blue-700">
                                                {{ __('View Sources') }}
                                            </a></p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
