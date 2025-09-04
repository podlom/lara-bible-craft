<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('messages.create_bibliography') }}</h2>
    </x-slot>

    <div class="p-6">
        <form action="{{ route('bibliographies.store') }}" method="POST">
            @csrf

            <label class="block">
                <span class="text-gray-700">{{ __('messages.title') }}</span>
                <input name="title" class="form-input mt-1 block w-full" value="{{ old('title') }}" required>
            </label>

            <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">
                {{ __('messages.save') }}
            </button>
        </form>
    </div>
</x-app-layout>
