@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('manager.landing-contents.index') }}" class="text-sm text-slate-500 hover:text-blue-600 flex items-center gap-1 transition font-medium">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    <h1 class="text-2xl font-bold text-slate-900 mb-6">Edit Konten Landing Page</h1>

    <form method="POST" action="{{ route('manager.landing-contents.update', $landingContent) }}" class="card-panel rounded-2xl border border-slate-200 p-6 space-y-5">
        @csrf @method('POST')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2" for="section">Section <span class="text-red-500">*</span></label>
                <select id="section" name="section" required
                        class="w-full px-4 py-3 rounded-lg bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                    @foreach($sections as $section)
                        <option value="{{ $section }}" {{ (old('section', $landingContent->section) === $section) ? 'selected' : '' }}>{{ ucfirst($section) }}</option>
                    @endforeach
                </select>
                @error('section')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2" for="order">Urutan <span class="text-red-500">*</span></label>
                <input id="order" type="number" name="order" value="{{ old('order', $landingContent->order) }}" min="0" required
                       class="w-full px-4 py-3 rounded-lg bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                @error('order')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2" for="title">Judul <span class="text-red-500">*</span></label>
            <input id="title" type="text" name="title" value="{{ old('title', $landingContent->title) }}" required maxlength="200"
                   class="w-full px-4 py-3 rounded-lg bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
            @error('title')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2" for="content">Konten</label>
            <textarea id="content" name="content" rows="5"
                      class="w-full px-4 py-3 rounded-lg bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm resize-none">{{ old('content', $landingContent->content) }}</textarea>
            @error('content')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2" for="image">URL Gambar (opsional)</label>
            <input id="image" type="text" name="image" value="{{ old('image', $landingContent->image) }}" maxlength="500"
                   class="w-full px-4 py-3 rounded-lg bg-white border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
            @error('image')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ (old('is_active', $landingContent->is_active)) ? 'checked' : '' }}
                       class="w-4 h-4 rounded bg-white border-slate-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-slate-700 font-medium">Aktifkan konten ini</span>
            </label>
        </div>

        <button type="submit"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
            Perbarui Konten
        </button>
    </form>
</div>
@endsection