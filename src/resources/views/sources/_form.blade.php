@csrf

<div class="mb-3">
    <label class="form-label">{{ __('Type') }}</label>
    <select name="type" class="form-control" required>
        @foreach(['book', 'article', 'standard', 'website', 'report', 'law', 'thesis'] as $t)
            <option value="{{ $t }}" @selected(old('type', $source->type ?? '') === $t)>
                {{ ucfirst($t) }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Authors (JSON array)') }}</label>
    <textarea name="authors" class="form-control" required>{{ old('authors', $source->authors ?? '[]') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Title') }}</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $source->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Year') }}</label>
    <input type="number" name="year" class="form-control" value="{{ old('year', $source->year ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Formatted Entry') }}</label>
    <textarea name="formatted_entry" class="form-control" required>{{ old('formatted_entry', $source->formatted_entry ?? '') }}</textarea>
</div>

<button class="btn btn-success" type="submit">{{ __('Save') }}</button>
