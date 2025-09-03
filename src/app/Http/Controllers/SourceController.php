<?php

namespace App\Http\Controllers;

use App\Models\Bibliography;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class SourceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        App::setLocale(session('locale', 'en'));
    }

    public function index(Request $request)
    {
        $query = Source::query();

        if ($search = $request->get('author')) {
            $query->where('authors', 'like', '%' . $search . '%');
        }

        $sources = $query->orderBy('id', 'desc')->paginate(10);

        return view('sources.index', compact('sources', 'search'));
    }

    public function create()
    {
        return view('sources.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'authors' => 'required|string',
            'type' => 'nullable|string',
            'year' => 'nullable|integer',
            'formatted_entry' => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();

        $source = Source::create($data);

        return redirect()->route('sources.show', $source)
            ->with('success', __('messages.source_created'));
    }

    public function show(Source $source)
    {
        return view('sources.show', compact('source'));
    }

    public function edit(Source $source)
    {
        $this->authorize('update', $source);

        return view('sources.edit', compact('source'));
    }

    public function update(Request $request, Source $source)
    {
        $this->authorize('update', $source);

        $data = $request->validate([
            'title' => 'required|string',
            'authors' => 'required|string',
            'type' => 'nullable|string',
            'year' => 'nullable|integer',
            'formatted_entry' => 'nullable|string',
        ]);

        $source->update($data);

        return redirect()->route('sources.show', $source)
            ->with('success', __('messages.source_updated'));
    }

    public function destroy(Source $source)
    {
        $this->authorize('delete', $source);

        $source->delete();

        return redirect()->route('sources.index')
            ->with('success', __('messages.source_deleted'));
    }
}
