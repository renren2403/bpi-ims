<!--create.blade.php-->
@extends('layouts.app')
@section('title', 'Add Operating Unit')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Add Operating Unit</h2>
                <p class="text-xs text-slate-500 mt-0.5">Register a new bureau division, center, or field station for
                    monitoring.</p>
            </div>
            <a href="{{ route('admin.operating-units.index') }}"
                class="text-xs font-bold text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition">
                &larr; Back to List
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm">
            <form method="POST" action="{{ route('admin.operating-units.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Official Unit Name <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="e.g. Crop Pest Management Division" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Unit Acronym / Code
                            <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. CPMD, BNCRDC"
                            required oninput="this.value = this.value.toUpperCase()"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition uppercase">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Classification
                            Type</label>
                        @php
                            $typeOptions = $classificationTypes ?? \App\Models\OperatingUnit::TYPES;
                            $selectedType = old('type', 'Division');
                        @endphp
                        <select name="type"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition">
                            <option value="">-- Select Type --</option>
                            @foreach ($typeOptions as $typeOption)
                                <option value="{{ $typeOption }}" {{ $selectedType === $typeOption ? 'selected' : '' }}>
                                    {{ $typeOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Key Officers (Nullable on Creation) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Division Chief / Unit Head <span class="text-slate-400 font-normal lowercase">(optional)</span>
                        </label>
                        <select name="division_chief_id"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition">
                            <option value="">-- Select Division Chief --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('division_chief_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} {{ $user->designation ? "({$user->designation})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Reporting Officer / Encoder <span class="text-slate-400 font-normal lowercase">(optional)</span>
                        </label>
                        <select name="reporting_officer_id"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition">
                            <option value="">-- Select Reporting Officer --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('reporting_officer_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} {{ $user->designation ? "({$user->designation})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mandate &
                        Description</label>
                    <textarea name="description" rows="3" placeholder="Brief mandate or functional scope of this operating unit..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-medium transition">{{ old('description') }}</textarea>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="font-bold text-slate-700">Active (Unit is included in active reporting cycles and BED-2
                            monitoring)</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.operating-units.index') }}"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">Cancel</a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md shadow-emerald-600/20 transition">
                        Save Operating Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
