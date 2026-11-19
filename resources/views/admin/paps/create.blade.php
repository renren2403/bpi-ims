<!--create.blade.php Program / PAP-->
@extends('layouts.app')
@section('title', 'Register New Program (PAP)')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Register Program / PAP</h2>
                <p class="text-xs text-slate-500 mt-0.5">Add an official budget program line-item to the IPMS directory.</p>
            </div>
            <a href="{{ route('admin.paps.index') }}"
                class="text-xs font-bold text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition">&larr;
                Back to Directory</a>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl space-y-1">
                <p class="font-extrabold">Please check the following errors:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm">
            <form method="POST" action="{{ route('admin.paps.store') }}" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Program / PAP Code <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. PAP-04 or 310100100001000" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold uppercase transition">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category <span class="text-rose-500">*</span></label>
                        <select name="category" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Program / Project Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. National Plant Quarantine & Phytosanitary Protection" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description / Scope</label>
                    <textarea name="description" rows="3" placeholder="Brief description of the program mandates, objectives, or target sector."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">{{ old('description') }}</textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="is_active" class="font-bold text-slate-700">Set as Active Program immediately</label>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.paps.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold transition">Cancel</a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold shadow-md shadow-emerald-600/20 transition">
                        Register & Continue &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
