<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $bibliography->title }}</h2>
    </x-slot>

    <div class="p-6">
        <p><strong>{{ __('messages.created_at') }}:</strong> {{ $bibliography->created_at->format('Y-m-d') }}</p>

        <a href="{{ route('bibliographies.index') }}" class="text-blue-500">{{ __('messages.back') }}</a>
    </div>
</x-app-layout>
