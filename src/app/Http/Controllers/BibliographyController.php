<?php

namespace App\Http\Controllers;

use App\Models\Bibliography;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class BibliographyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        App::setLocale(session('locale', 'en'));
    }

    public function index()
    {
        $query = Bibliography::query();

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        }

        $bibliographies = $query->latest()->paginate(10);

        return view('bibliographies.index', compact('bibliographies'));
    }

    public function create()
    {
        return view('bibliographies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $bibliography = Bibliography::create([
            'title' => $validated['title'],
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('bibliographies.show', $bibliography)->with('success', __('messages.created_successfully'));
    }

    public function show(Bibliography $bibliography)
    {
        if (Auth::check() && $bibliography->user_id !== Auth::id()) {
            abort(403);
        }

        return view('bibliographies.show', compact('bibliography'));
    }

    public function edit(Bibliography $bibliography)
    {
        if ($bibliography->user_id !== Auth::id()) {
            abort(403);
        }

        return view('bibliographies.edit', compact('bibliography'));
    }

    public function update(Request $request, Bibliography $bibliography)
    {
        if ($bibliography->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $bibliography->update($validated);

        return redirect()->route('bibliographies.show', $bibliography)->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Bibliography $bibliography)
    {
        if ($bibliography->user_id !== Auth::id()) {
            abort(403);
        }

        $bibliography->delete();

        return redirect()->route('bibliographies.index')->with('success', __('messages.deleted_successfully'));
    }
}
