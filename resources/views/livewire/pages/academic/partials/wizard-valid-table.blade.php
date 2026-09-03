{{-- Valid Data Table (Preview) --}}
<div class="mt-4">
    <div class="flex items-center gap-2 mb-4">
        <flux:icon.check-circle class="size-5 text-emerald-600" />
        <flux:heading size="sm" class="text-zinc-800">Siap Disimpan ({{ count($validData) }} baris)</flux:heading>
    </div>

    <div class="overflow-x-auto rounded-lg border border-zinc-200 shadow-sm max-h-96">
        <table class="w-full text-sm text-left">
            <thead class="bg-zinc-50 text-zinc-700 font-medium sticky top-0 shadow-sm">
                <tr>
                    <th class="px-4 py-2 text-center w-12">#</th>
                    @foreach($columns as $key => $label)
                        <th class="px-4 py-2">{{ $label }}</th>
                    @endforeach
                    <th class="px-4 py-2 text-center w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 bg-white">
                @foreach($validData as $index => $row)
                    <tr class="hover:bg-zinc-50 transition-colors">
                        <td class="px-4 py-2 text-center text-zinc-500">{{ $index + 1 }}</td>
                        @foreach($columns as $key => $label)
                            <td class="px-4 py-2 text-zinc-800 whitespace-nowrap">
                                @if(is_numeric($row[$key]) && (str_contains($key, 'amount') || str_contains($key, 'arrears')))
                                    Rp {{ number_format($row[$key], 0, ',', '.') }}
                                @else
                                    {{ is_array($row[$key]) ? implode(', ', $row[$key]) : $row[$key] }}
                                @endif
                            </td>
                        @endforeach
                        <td class="px-4 py-2 text-center">
                            <flux:tooltip content="Hapus">
                                <flux:button size="sm" variant="subtle" icon="trash" class="text-rose-500 hover:text-rose-700" wire:click="{{ $removeMethod }}({{ $index }})" />
                            </flux:tooltip>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
