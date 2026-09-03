{{-- Invalid Data Table (Needs Fix) --}}
<div class="mt-4 p-4 bg-rose-50 rounded-xl border border-rose-200">
    <div class="flex items-center gap-2 mb-4">
        <flux:icon.exclamation-triangle class="size-5 text-rose-600" />
        <flux:heading size="sm" class="text-rose-800">Perlu Perbaikan ({{ count($invalidData) }} baris)</flux:heading>
    </div>

    <div class="overflow-x-auto rounded-lg border border-rose-200">
        <table class="w-full text-sm text-left">
            <thead class="bg-rose-100 text-rose-900 font-medium">
                <tr>
                    <th class="px-4 py-2 text-center w-12">#</th>
                    @foreach($columns as $key => $label)
                        <th class="px-4 py-2">{{ $label }}</th>
                    @endforeach
                    <th class="px-4 py-2">Pesan Error</th>
                    <th class="px-4 py-2 text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-200 bg-white">
                @foreach($invalidData as $index => $row)
                    <tr>
                        <td class="px-4 py-3 text-center text-zinc-500">{{ $row['_index'] ?? $index + 1 }}</td>
                        @foreach($columns as $key => $label)
                            <td class="px-4 py-3">
                                <flux:input size="sm" wire:model.defer="{{ $type }}Invalid.{{ $index }}.{{ $key }}" class="{{ isset($row['_errors'][$key]) ? 'border-rose-500' : '' }}" />
                            </td>
                        @endforeach
                        <td class="px-4 py-3">
                            <ul class="list-disc list-inside text-rose-600 text-xs space-y-1">
                                @foreach($row['_errors'] as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <flux:tooltip content="Simpan Perbaikan">
                                    <flux:button size="sm" variant="subtle" icon="check" class="text-emerald-600 hover:bg-emerald-50" wire:click="fixInvalidRow({{ $index }})" />
                                </flux:tooltip>
                                <flux:tooltip content="Hapus Baris">
                                    <flux:button size="sm" variant="subtle" icon="trash" class="text-rose-600 hover:bg-rose-50" wire:click="removeInvalidRow('{{ $type }}', {{ $index }})" />
                                </flux:tooltip>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
