<div class="flex flex-col gap-huge">
    <!-- Page Header -->
    <section class="flex flex-col gap-tiny px-1">
        <h2 class="font-headline-md text-2xl font-semibold text-primary">{{ __('Pusat Bantuan') }}</h2>
        <p class="font-body-md text-sm text-on-surface-variant">{{ __('Temukan jawaban untuk pertanyaan yang paling sering ditanyakan.') }}</p>
    </section>

    <!-- FAQ Accordion (Alpine.js) -->
    <div class="space-y-3" x-data="{ active: null }">
        <!-- Item 1 -->
        <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-outline-variant">
            <button @click="active !== 1 ? active = 1 : active = null" class="w-full flex justify-between items-center p-normal text-left hover:bg-surface-container/50 transition-colors">
                <span class="font-semibold text-sm">{{ __('Bagaimana cara melakukan pembayaran?') }}</span>
                <flux:icon.chevron-down variant="outline" class="size-4 text-zinc-500 transition-transform duration-200" x-bind:class="active === 1 ? 'rotate-180' : ''" />
            </button>
            <div x-show="active === 1" x-collapse x-cloak>
                <div class="p-normal pt-0 text-xs text-on-surface-variant leading-relaxed">
                    <ol class="list-decimal ml-4 space-y-2">
                        <li>{{ __('Pilih menu "Tagihan" pada navigasi bawah.') }}</li>
                        <li>{{ __('Klik pada kartu tagihan yang ingin dibayar.') }}</li>
                        <li>{{ __('Pilih metode pembayaran (VA, QRIS, atau Dana).') }}</li>
                        <li>{{ __('Klik "Bayar Sekarang" dan selesaikan transaksi sesuai instruksi di layar.') }}</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Item 2 -->
        <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-outline-variant">
            <button @click="active !== 2 ? active = 2 : active = null" class="w-full flex justify-between items-center p-normal text-left hover:bg-surface-container/50 transition-colors">
                <span class="font-semibold text-sm">{{ __('Bisakah saya membayar lebih dari satu tagihan sekaligus?') }}</span>
                <flux:icon.chevron-down variant="outline" class="size-4 text-zinc-500 transition-transform duration-200" x-bind:class="active === 2 ? 'rotate-180' : ''" />
            </button>
            <div x-show="active === 2" x-collapse x-cloak>
                <div class="p-normal pt-0 text-xs text-on-surface-variant leading-relaxed">
                    <p>{{ __('Tentu saja! Pada menu "Tagihan", klik tombol "Pilih Tagihan (Bayar Massal)" di bagian bawah daftar. Anda dapat mencentang beberapa tagihan sekaligus dan membayarnya dalam satu kali transaksi Midtrans.') }}</p>
                </div>
            </div>
        </div>

        <!-- Item 3 -->
        <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-outline-variant">
            <button @click="active !== 3 ? active = 3 : active = null" class="w-full flex justify-between items-center p-normal text-left hover:bg-surface-container/50 transition-colors">
                <span class="font-semibold text-sm">{{ __('Mengapa anak saya tidak muncul dalam daftar?') }}</span>
                <flux:icon.chevron-down variant="outline" class="size-4 text-zinc-500 transition-transform duration-200" x-bind:class="active === 3 ? 'rotate-180' : ''" />
            </button>
            <div x-show="active === 3" x-collapse x-cloak>
                <div class="p-normal pt-0 text-xs text-on-surface-variant leading-relaxed">
                    <p>{{ __('Data anak dihubungkan berdasarkan alamat email orang tua yang terdaftar di sekolah. Jika ada data yang tidak muncul, silakan hubungi bagian Tata Usaha Sekolah untuk memverifikasi data NIS dan Email Anda.') }}</p>
                </div>
            </div>
        </div>

        <!-- Item 4 -->
        <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-outline-variant">
            <button @click="active !== 4 ? active = 4 : active = null" class="w-full flex justify-between items-center p-normal text-left hover:bg-surface-container/50 transition-colors">
                <span class="font-semibold text-sm">{{ __('Di mana saya bisa mengunduh kwitansi pembayaran?') }}</span>
                <flux:icon.chevron-down variant="outline" class="size-4 text-zinc-500 transition-transform duration-200" x-bind:class="active === 4 ? 'rotate-180' : ''" />
            </button>
            <div x-show="active === 4" x-collapse x-cloak>
                <div class="p-normal pt-0 text-xs text-on-surface-variant leading-relaxed">
                    <p>{{ __('Setelah pembayaran berhasil, Anda dapat masuk ke menu "Riwayat" di navigasi bawah. Klik tombol "Kwitansi" pada transaksi yang diinginkan untuk mengunduh bukti bayar dalam format PDF.') }}</p>
                </div>
            </div>
        </div>

        <!-- Item 5 -->
        <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-outline-variant">
            <button @click="active !== 5 ? active = 5 : active = null" class="w-full flex justify-between items-center p-normal text-left hover:bg-surface-container/50 transition-colors">
                <span class="font-semibold text-sm">{{ __('Apa yang harus saya lakukan jika pembayaran gagal?') }}</span>
                <flux:icon.chevron-down variant="outline" class="size-4 text-zinc-500 transition-transform duration-200" x-bind:class="active === 5 ? 'rotate-180' : ''" />
            </button>
            <div x-show="active === 5" x-collapse x-cloak>
                <div class="p-normal pt-0 text-xs text-on-surface-variant leading-relaxed">
                    <p>{{ __('Jangan khawatir, jika saldo Anda sudah terpotong namun status belum berubah, tunggu maksimal 10 menit. Jika status masih belum berubah, silakan hubungi Admin Keuangan Sekolah dengan melampirkan screenshot bukti potong saldo Anda.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Support -->
    <section class="mt-4 p-large bg-primary-container text-on-primary-container rounded-3xl flex flex-col gap-normal items-center text-center">
        <flux:icon.chat-bubble-left-right variant="solid" class="size-12 opacity-80" />
        <div>
            <h3 class="font-bold text-lg">{{ __('Masih butuh bantuan?') }}</h3>
            <p class="text-xs opacity-90">{{ __('Hubungi WhatsApp Admin Keuangan kami untuk respon yang lebih cepat.') }}</p>
        </div>
        <flux:button href="https://wa.me/628123456789" target="_blank" variant="primary" class="w-full !bg-secondary border-none">
            {{ __('Hubungi Admin (WA)') }}
        </flux:button>
    </section>
</div>
