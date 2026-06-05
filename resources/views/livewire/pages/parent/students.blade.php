<div class="flex flex-col gap-huge">
    <!-- Section Header -->
    <section class="flex flex-col gap-tiny px-1">
        <h2 class="font-headline-md text-2xl font-semibold text-primary">{{ __('Siswa') }}</h2>
        <p class="font-body-md text-sm text-on-surface-variant">{{ __('Daftar putra-putri Anda yang terdaftar di sekolah.') }}</p>
    </section>

    <!-- Students List -->
    <div class="flex flex-col gap-3">
        @forelse($students as $student)
            <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
                <div class="p-normal flex items-start gap-normal">
                    <!-- Photo/Avatar -->
                    <div class="relative">
                        @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" class="w-16 h-16 rounded-2xl object-cover border border-outline-variant">
                        @else
                            <div class="w-16 h-16 rounded-2xl bg-primary-container/10 flex items-center justify-center border border-outline-variant">
                                <flux:icon.user variant="outline" class="text-primary size-8" />
                            </div>
                        @endif
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full {{ $student->status === 'aktif' ? 'bg-secondary' : 'bg-zinc-400' }} border-2 border-white flex items-center justify-center">
                            @if($student->status === 'aktif')
                                <flux:icon.check variant="outline" class="text-white size-3" />
                            @else
                                <flux:icon.x-mark variant="outline" class="text-white size-3" />
                            @endif
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 flex flex-col gap-0.5">
                        <h3 class="font-title-sm text-lg font-bold text-primary leading-tight">{{ $student->name }}</h3>
                        <p class="font-label-bold text-xs text-on-surface-variant">NIS: {{ $student->nis }}</p>
                        
                        <div class="mt-2 flex flex-wrap gap-2">
                            <div class="flex items-center gap-1">
                                <flux:icon.academic-cap variant="outline" class="size-3.5 text-on-surface-variant" />
                                <span class="font-label-bold text-xs font-semibold text-on-surface-variant">
                                    @if($student->schoolClass)
                                        {{ $student->schoolClass->name }} ({{ $student->schoolClass->grade }})
                                    @else
                                        {{ __('Tingkat') }} {{ $student->current_grade }} ({{ __('Belum Ada Kelas') }})
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="mt-1 flex items-center gap-1">
                            <flux:icon.calendar variant="outline" class="size-3.5 text-secondary" />
                            <span class="font-caption text-[10px] text-secondary">
                                @if($student->schoolClass)
                                    {{ __('Tahun Ajaran :year', ['year' => $student->schoolClass->academicYear->name]) }}
                                @else
                                    {{ __('Tahun Masuk: :year', ['year' => $student->entry_year]) }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Link -->
                <div class="bg-surface-container-low px-normal py-3 border-t border-outline-variant flex justify-between items-center">
                    <flux:button variant="ghost" size="sm" :href="route('parent.invoices')" class="!text-xs font-semibold text-primary" wire:navigate>
                        {{ __('Lihat Tagihan') }}
                    </flux:button>
                    <flux:icon.chevron-right variant="outline" class="text-zinc-400 size-4" />
                </div>
            </div>
        @empty
            <div class="p-huge text-center text-on-surface-variant bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant">
                {{ __('Belum ada data anak yang terdaftar.') }}
            </div>
        @endforelse

        <!-- Info Box (Link to Help) -->
        <a href="{{ route('parent.help') }}" wire:navigate class="mt-smallall p-normal bg-surface-container-high/50 rounded-2xl flex gap-normal items-start active:bg-surface-container-high transition-colors">
            <flux:icon.question-mark-circle variant="outline" class="text-on-surface-variant size-5" />
            <div class="flex-1">
                <p class="font-caption text-xs text-on-surface-variant mt-xs leading-relaxed">{{ __('Jika Anda tidak melihat salah satu anak Anda terdaftar atau butuh bantuan lainnya, klik di sini untuk bantuan.') }}</p>
            </div>
            <flux:icon.chevron-right variant="outline" class="size-4 text-zinc-400 self-center" />
        </a>
    </div>
</div>
