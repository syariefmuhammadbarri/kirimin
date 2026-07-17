<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LandingContent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LandingContentController extends Controller
{
    public function index()
    {
        $contents = LandingContent::orderBy('section')->orderBy('order')->get();
        return view('manager.landing-contents.index', compact('contents'));
    }

    public function create()
    {
        $sections = ['hero', 'promo', 'features', 'about', 'services', 'testimonials', 'contact', 'footer'];
        return view('manager.landing-contents.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'section' => 'required|string|max:50',
            'title' => 'required|string|max:200',
            'content' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        LandingContent::create($data);

        return redirect()->route('manager.landing-contents.index')
            ->with('success', 'Konten landing page berhasil ditambahkan.');
    }

    public function edit(LandingContent $landingContent)
    {
        $sections = ['hero', 'promo', 'features', 'about', 'services', 'testimonials', 'contact', 'footer'];
        return view('manager.landing-contents.edit', compact('landingContent', 'sections'));
    }

    public function update(Request $request, LandingContent $landingContent)
    {
        $data = $request->validate([
            'section' => 'required|string|max:50',
            'title' => 'required|string|max:200',
            'content' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $landingContent->update($data);

        return redirect()->route('manager.landing-contents.index')
            ->with('success', 'Konten landing page berhasil diperbarui.');
    }

    public function destroy(LandingContent $landingContent)
    {
        $landingContent->delete();

        return redirect()->route('manager.landing-contents.index')
            ->with('success', 'Konten landing page berhasil dihapus.');
    }
}