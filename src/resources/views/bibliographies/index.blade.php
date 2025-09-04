<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('messages.bibliographies') }}</h2>
    </x-slot>

    <div class="p-6 text-gray-900">
        @auth
            <a href="{{ route('bibliographies.create') }}" class="text-blue-500 underline">{{ __('messages.create_new') }}</a>
        @endauth

        <ul class="mt-4">
            @foreach($bibliographies as $b)
                <li class="mb-2">
                    <a href="{{ route('bibliographies.show', $b) }}" class="text-lg text-indigo-600">{{ $b->title }}</a>
                    @auth
                        @if($b->user_id === auth()->id())
                            <a href="{{ route('bibliographies.edit', $b) }}" class="ml-2 text-sm text-blue-500">{{ __('messages.edit') }}</a>
                            <form action="{{ route('bibliographies.destroy', $b) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-sm text-red-500">{{ __('messages.delete') }}</button>
                            </form>
                        @endif
                    @endauth
                </li>
            @endforeach
        </ul>

        {{ $bibliographies->links() }}
    </div>
</x-app-layout>
