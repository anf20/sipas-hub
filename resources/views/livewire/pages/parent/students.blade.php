<div class="flex flex-col gap-6">
    <!-- Section Header -->
    <section class="flex flex-col gap-1 px-1">
        <h2 class="text-2xl font-bold text-forest-text-main font-display">{{ __('Data Anak') }}</h2>
        <p class="text-sm text-forest-text-muted">{{ __('Daftar putra-putri Anda yang terdaftar di sekolah.') }}</p>
    </section>

    <!-- Students List (Stitch Style) -->
    <div class="flex flex-col gap-4">
        @forelse($students as $student)
            <a href="{{ route('parent.invoices') }}" wire:navigate class="bg-white rounded-2xl border border-forest-light-sage/20 shadow-sm p-4 flex items-center gap-4 relative overflow-hidden transition-all duration-200 hover:shadow-md hover:border-forest-sage/40 group">
                <!-- Background Decorative Icon -->
                <div class="absolute top-0 right-0 p-3 pointer-events-none">
                    <flux:icon.user variant="solid" class="text-forest-text-muted opacity-5 size-16 -mr-4 -mt-4 rotate-12 transition-transform duration-300 group-hover:scale-110" />
                </div>

                <!-- Photo/Avatar -->
                <div class="relative w-20 h-20 shrink-0 rounded-2xl overflow-hidden border-2 border-forest-surface bg-white shadow-xs">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-forest-surface/50 flex items-center justify-center">
                            <flux:icon.user variant="solid" class="text-forest-sage size-8" />
                        </div>
                    @endif
                    <!-- Status dot badge -->
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full {{ $student->status === 'aktif' ? 'bg-forest-success' : 'bg-zinc-400' }} border-2 border-white flex items-center justify-center shadow-xs">
                        @if($student->status === 'aktif')
                            <flux:icon.check variant="solid" class="text-white size-3" />
                        @else
                            <flux:icon.x-mark variant="solid" class="text-white size-3" />
                        @endif
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-grow flex flex-col gap-1 min-w-0">
                    <h3 class="text-base font-bold text-forest-text-main leading-tight group-hover:text-forest-primary transition-colors truncate">{{ $student->name }}</h3>
                    
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-0.5">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <flux:icon.identification class="size-3.5 text-forest-text-muted shrink-0" />
                            <span class="text-xs font-semibold text-forest-text-muted truncate">NIS: {{ $student->nis }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 min-w-0">
                            <flux:icon.academic-cap class="size-3.5 text-forest-text-muted shrink-0" />
                            <span class="text-xs font-semibold text-forest-text-muted truncate">
                                @if($student->schoolClass)
                                    {{ $student->schoolClass->name }} ({{ $student->schoolClass->grade }})
                                @else
                                    {{ __('Tingkat') }} {{ $student->current_grade }} ({{ __('Belum Ada Kelas') }})
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-1 flex items-center gap-1.5 text-forest-sage">
                        <flux:icon.calendar class="size-3.5 text-forest-sage shrink-0" />
                        <span class="text-[10px] font-semibold truncate">
                            @if($student->schoolClass)
                                {{ __('Tahun Ajaran :year', ['year' => $student->schoolClass->academicYear->name]) }}
                            @else
                                {{ __('Tahun Masuk: :year', ['year' => $student->entry_year]) }}
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Action icon -->
                <flux:icon.chevron-right class="text-forest-text-muted size-5 shrink-0 transition-transform duration-200 group-hover:translate-x-1" />
            </a>
        @empty
            <div class="py-12 text-center text-forest-text-muted bg-white rounded-2xl border border-dashed border-forest-light-sage/30">
                {{ __('Belum ada data anak yang terdaftar.') }}
            </div>
        @endforelse

        <!-- Utility Info (Stitch Style) -->
        <a href="{{ route('parent.help') }}" wire:navigate class="mt-2 p-4 bg-forest-surface/50 border border-forest-light-sage/20 rounded-2xl flex gap-4 items-start hover:bg-forest-surface transition-colors duration-150 group">
            <flux:icon.information-circle variant="solid" class="text-forest-sage size-6 shrink-0 mt-0.5" />
            <div class="flex-grow min-w-0">
                <h4 class="text-sm font-bold text-forest-text-main">{{ __('Anak Anda Tidak Terdaftar?') }}</h4>
                <p class="text-xs text-forest-text-muted mt-1 leading-relaxed">{{ __('Jika Anda tidak melihat salah satu anak Anda terdaftar atau butuh bantuan lainnya, silakan klik di sini untuk menghubungi tata usaha sekolah.') }}</p>
            </div>
            <flux:icon.chevron-right class="size-4 text-forest-text-muted shrink-0 self-center transition-transform duration-200 group-hover:translate-x-1" />
        </a>
    </div>
</div>
