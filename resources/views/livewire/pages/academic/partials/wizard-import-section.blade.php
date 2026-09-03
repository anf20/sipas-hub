{{-- Import / Manual Toggle & Upload Section --}}
<div class="p-4 bg-zinc-50 rounded-xl border border-zinc-200">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <flux:heading size="sm">Import Data {{ $stepLabel }} (Excel/CSV)</flux:heading>
            <flux:text class="text-sm">Gunakan template resmi untuk mengimpor data secara massal.</flux:text>
        </div>
        <div class="flex gap-2">
            <flux:button variant="ghost" wire:click="downloadTemplate" icon="arrow-down-tray">Unduh Template</flux:button>
        </div>
    </div>

    <div class="mt-4 flex flex-col sm:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <flux:field>
                <flux:label>Upload File Template (xlsx, xls, csv)</flux:label>
                <flux:input type="file" wire:model="uploadFile" accept=".xlsx,.xls,.csv" />
                <flux:error name="uploadFile" />
            </flux:field>
        </div>
        <flux:button variant="primary" wire:click="uploadAndParse" icon="cloud-arrow-up" wire:loading.attr="disabled" wire:target="uploadFile, uploadAndParse">
            <span wire:loading.remove wire:target="uploadFile, uploadAndParse">Mulai Import</span>
            <span wire:loading wire:target="uploadFile, uploadAndParse">Memproses...</span>
        </flux:button>
    </div>
</div>
