<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:heading size="xl">{{ __('Manajemen Pengguna & Role') }}</flux:heading>
    </flux:header>

    <flux:main>
        <div class="space-y-4">
            <div class="flex justify-between items-center gap-4">
                <div class="flex items-center gap-4 flex-1">
                    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="{{ __('Cari pengguna...') }}" class="max-w-xs" />
                    <flux:button :href="route('management.users.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah Pengguna') }}</flux:button>
                </div>
            </div>
             
             @if (session('status'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    {{ session('status') }}
                </div>
            @endif

             @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    {{ session('error') }}
                </div>
            @endif

             <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Nama') }}</flux:table.column>
                    <flux:table.column>{{ __('Email') }}</flux:table.column>
                    <flux:table.column>{{ __('Role') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($users as $user)
                        <flux:table.row :key="$user->id">
                            <flux:table.cell font-weight="medium">{{ $user->name }}</flux:table.cell>
                            <flux:table.cell>{{ $user->email }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-1 flex-wrap">
                                    @foreach($user->roles as $role)
                                        <flux:badge size="sm" color="gray" >{{ $role->name }}</flux:badge>
                                    @endforeach
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-end gap-2">
                                    <flux:button variant="ghost" size="sm" icon="eye" :href="route('management.users.show', $user->id)" wire:navigate />
                                    <flux:button variant="ghost" size="sm" icon="pencil" :href="route('management.users.edit', $user->id)" wire:navigate />
                                    
                                    @if($user->id !== auth()->id())
                                        <flux:modal.trigger name="delete-user-{{ $user->id }}">
                                            <flux:button variant="ghost" size="sm" icon="trash" />
                                        </flux:modal.trigger>

                                        <flux:modal name="delete-user-{{ $user->id }}" class="min-w-[22rem]">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">{{ __('Hapus Pengguna?') }}</flux:heading>
                                                    <flux:subheading>{{ __('Tindakan ini tidak dapat dibatalkan.') }}</flux:subheading>
                                                </div>
                                                <div class="flex gap-2">
                                                    <flux:spacer />
                                                    <flux:modal.close>
                                                        <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                                                    </flux:modal.close>
                                                    <flux:button variant="danger" wire:click="delete({{ $user->id }})" wire:loading.attr="disabled">{{ __('Hapus') }}</flux:button>
                                                </div>
                                            </div>
                                        </flux:modal>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4" class="text-center py-8 text-zinc-500">
                                {{ __('Tidak ada pengguna ditemukan.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
             </flux:table>

             <div class="mt-4">
                {{ $users->links() }}
             </div>
        </div>
    </flux:main>
</div>
