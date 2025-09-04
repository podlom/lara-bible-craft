<?php

/** @var \App\Models\User $user */

?>


<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Pagination Settings') }}
        </h2>


        <p class="mt-1 text-sm text-gray-600">
            {{ __('Set how many sources to display per page.') }}
        </p>
    </header>


    <form method="post" action="{{ route('profile.pagination.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')


        <div>
            <x-input-label for="per_page" :value="__('Items per page')" />
            <x-text-input id="per_page" name="per_page" type="number" min="1" max="100" class="mt-1 block w-20" :value="old('per_page', $user->per_page ?? 10)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('per_page')" />
        </div>


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'pagination-updated')
                <p class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
