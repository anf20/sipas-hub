<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head', ['title' => 'Executive Summary - SIPAS-Hub'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Amiri:wght@400;700&family=Lexend:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <style>
        .font-arabic { font-family: 'Amiri', serif; }
        .font-display { font-family: 'Lexend', 'Plus Jakarta Sans', sans-serif; }
        .font-body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Islamic Geometric subtle background */
        .bg-islamic-pattern {
            background-color: #0A1E14;
            background-image: radial-gradient(#1E4A34 1.2px, transparent 1.2px), radial-gradient(#1E4A34 1.2px, #0A1E14 1.2px);
            background-size: 48px 48px;
            background-position: 0 0, 24px 24px;
        }

        .bg-mesh-gradient {
            background: radial-gradient(circle at 15% 20%, rgba(46, 125, 50, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 85% 80%, rgba(212, 175, 55, 0.12) 0%, transparent 40%),
                        radial-gradient(circle at 50% 50%, rgba(15, 42, 29, 0.05) 0%, transparent 60%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(227, 238, 212, 0.6);
        }

        .glass-dark-card {
            background: rgba(15, 42, 29, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(174, 195, 176, 0.2);
        }

        .gold-gradient-text {
            background: linear-gradient(135deg, #F9E79F 0%, #D4AF37 50%, #AA7C11 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .emerald-gradient-text {
            background: linear-gradient(135deg, #10B981 0%, #047857 50%, #064E3B 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Print Optimization for Board Meeting handouts */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: #111827 !important;
                font-size: 11pt;
            }
            .page-break {
                page-break-before: always;
            }
            .glass-card, .glass-dark-card {
                background: white !important;
                border: 1px solid #E5E7EB !important;
                box-shadow: none !important;
                color: #111827 !important;
            }
            .text-white {
                color: #111827 !important;
            }
        }
    </style>
</head>
<body class="bg-[#F8FAF6] text-[#0F2A1D] font-body antialiased selection:bg-[#2E7D32] selection:text-white" x-data="{ 
    mobileMenu: false,
    activeRoleTab: 'pimpinan',
    santriCount: 750,
    sppNominal: 500000,
    get totalPotensi() {
        return this.santriCount * this.sppNominal;
    },
    get jamHemat() {
        return Math.round((this.santriCount * 4.5) / 60);
    },
    get formatCurrency() {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(this.totalPotensi);
    },
    formatRupiah(val) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
    }
}">

    <!-- Top Announcement / Syariah Integrity Badge -->
    <div class="no-print bg-[#0F2A1D] border-b border-[#1E4A34] text-emerald-200 text-xs py-2 px-4">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-900/80 text-emerald-300 border border-emerald-700">
                    EXECUTIVE BRIEFING
                </span>
                <span class="text-xs text-emerald-100 font-medium">Dokumen Ringkasan Eksekutif & Presentasi Dewan Masyayikh & Pimpinan Pondok Pesantren</span>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5 text-emerald-300">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Prinsip: Amanah • Transparan • Akuntabel
                </span>
                <button onclick="window.print()" class="hover:text-white transition flex items-center gap-1 font-semibold text-amber-300 hover:underline cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Lembar Presentasi (PDF)
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="no-print sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-[#E3EED4] shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#0F2A1D] to-[#2E7D32] flex items-center justify-center shadow-md text-amber-300 font-display font-bold text-xl border border-emerald-600/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-display font-bold text-xl tracking-tight text-[#0F2A1D]">SIPAS-Hub</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-sm bg-[#E3EED4] text-[#2E7D32]">Pesantren Edition</span>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Sistem Informasi Pengelolaan Syahriyah & Finansial Pesantren</p>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-gray-700">
                <a href="#urgensi" class="hover:text-[#2E7D32] transition">Latar Belakang & Masalah</a>
                <a href="#solusi" class="hover:text-[#2E7D32] transition">Pilar Solusi</a>
                <a href="#modul" class="hover:text-[#2E7D32] transition">Modul Unggulan</a>
                <a href="#alur" class="hover:text-[#2E7D32] transition">Alur Kerja</a>
                <a href="#simulasi" class="hover:text-[#2E7D32] transition">Kalkulator Efisiensi</a>
                <a href="#faq" class="hover:text-[#2E7D32] transition">Tanya Jawab Pimpinan</a>
            </nav>

            <!-- Action Buttons -->
            <div class="hidden sm:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-[#0F2A1D] hover:bg-[#1E4A34] text-white font-semibold text-sm shadow-sm transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Ke Dashboard Sistem
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-[#0F2A1D] hover:bg-[#1E4A34] text-white font-semibold text-sm shadow-sm transition flex items-center gap-2">
                        <span>Masuk Sistem</span>
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <div class="md:hidden flex items-center">
                <button @click="mobileMenu = !mobileMenu" class="p-2 text-gray-700 hover:text-emerald-800 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenu" x-cloak class="md:hidden border-t border-gray-200 bg-white px-4 pt-3 pb-6 space-y-3">
            <a @click="mobileMenu = false" href="#urgensi" class="block text-sm font-medium text-gray-700 py-1">Latar Belakang & Masalah</a>
            <a @click="mobileMenu = false" href="#solusi" class="block text-sm font-medium text-gray-700 py-1">Pilar Solusi</a>
            <a @click="mobileMenu = false" href="#modul" class="block text-sm font-medium text-gray-700 py-1">Modul Unggulan</a>
            <a @click="mobileMenu = false" href="#alur" class="block text-sm font-medium text-gray-700 py-1">Alur Kerja</a>
            <a @click="mobileMenu = false" href="#simulasi" class="block text-sm font-medium text-gray-700 py-1">Kalkulator Efisiensi</a>
            <a @click="mobileMenu = false" href="#faq" class="block text-sm font-medium text-gray-700 py-1">Tanya Jawab Pimpinan</a>
            <div class="pt-2">
                <a href="{{ route('login') }}" class="w-full text-center block px-4 py-2.5 rounded-xl bg-[#0F2A1D] text-white font-semibold text-sm">
                    Masuk Portal Aplikasi
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION: Executive Presentation & Vision -->
    <section class="relative bg-islamic-pattern text-white pt-16 pb-24 overflow-hidden">
        <!-- Ambient Glow Circles -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-emerald-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-10 right-10 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Badges -->
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-8">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-emerald-400/30 text-emerald-300 text-xs font-semibold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    PROPOSAL & RINGKASAN STRATEGIS DIGITALISASI PESANTREN
                </div>
                
                <h1 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl tracking-tight leading-tight sm:leading-snug">
                    Transformasi Tata Kelola Keuangan Pesantren:
                    <span class="gold-gradient-text block mt-1">Amanah, Akuntabel, dan Modern</span>
                </h1>
                
                <p class="text-emerald-100/90 text-base sm:text-lg font-normal leading-relaxed">
                    Satu sistem terintegrasi untuk mengotomatisasi penagihan <strong class="text-white">Syahriyah (SPP)</strong>, penerimaan multi-channel (<strong class="text-amber-300">QRIS & Virtual Account</strong>), rekonsiliasi kas 0 detik, serta pelaporan keuangan real-time untuk Masyayikh, Pimpinan Yayasan, dan Wali Santri.
                </p>
            </div>

            <!-- Ayat / Filosofis Banner -->
            <div class="max-w-2xl mx-auto bg-emerald-950/60 border border-emerald-700/40 rounded-2xl p-4 sm:p-5 text-center backdrop-blur-sm mb-12 shadow-lg">
                <p class="font-arabic text-xl sm:text-2xl text-amber-200 mb-2 leading-loose" dir="rtl">
                    يٰٓاَيُّهَا الَّذِيْنَ اٰمَنُوْٓا اِذَا تَدَايَنْتُمْ بِدَيْنٍ اِلٰٓى اَجَلٍ مُّسَمًّى فَاكْتُبُوْهُ
                </p>
                <p class="text-xs sm:text-sm text-emerald-200/80 italic">
                    "Wahai orang-orang yang beriman, apabila kamu bermuamalah tidak secara tunai untuk waktu yang ditentukan, hendaklah kamu menuliskannya..." (QS. Al-Baqarah: 282)
                </p>
            </div>

            <!-- Key Executive Value Metrics in Hero Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 max-w-5xl mx-auto">
                <div class="glass-dark-card rounded-2xl p-5 text-center border-t-2 border-t-amber-400">
                    <div class="text-2xl sm:text-3xl font-extrabold font-display text-amber-300">100%</div>
                    <div class="text-xs font-semibold text-emerald-200 mt-1 uppercase tracking-wider">Otomatisasi Kas</div>
                    <p class="text-[11px] text-emerald-300/70 mt-1">Rekonsiliasi mutasi tanpa input manual</p>
                </div>
                <div class="glass-dark-card rounded-2xl p-5 text-center border-t-2 border-t-emerald-400">
                    <div class="text-2xl sm:text-3xl font-extrabold font-display text-emerald-300">0 Menit</div>
                    <div class="text-xs font-semibold text-emerald-200 mt-1 uppercase tracking-wider">Verifikasi Bayar</div>
                    <p class="text-[11px] text-emerald-300/70 mt-1">Status lunas detik itu juga via Webhook</p>
                </div>
                <div class="glass-dark-card rounded-2xl p-5 text-center border-t-2 border-t-amber-400">
                    <div class="text-2xl sm:text-3xl font-extrabold font-display text-amber-300">Anti-Fraud</div>
                    <div class="text-xs font-semibold text-emerald-200 mt-1 uppercase tracking-wider">Audit Trail Digital</div>
                    <p class="text-[11px] text-emerald-300/70 mt-1">Setiap aksi tercatat nama user & IP</p>
                </div>
                <div class="glass-dark-card rounded-2xl p-5 text-center border-t-2 border-t-emerald-400">
                    <div class="text-2xl sm:text-3xl font-extrabold font-display text-emerald-300">24/7</div>
                    <div class="text-xs font-semibold text-emerald-200 mt-1 uppercase tracking-wider">Akses Wali Santri</div>
                    <p class="text-[11px] text-emerald-300/70 mt-1">Cek tagihan & unduh PDF dari ponsel</p>
                </div>
            </div>

            <!-- CTA Presentation Bar -->
            <div class="mt-12 text-center flex flex-wrap items-center justify-center gap-4">
                <a href="#modul" class="px-7 py-3.5 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-[#0F2A1D] font-bold text-sm shadow-xl hover:shadow-amber-500/20 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Lihat Modul & Fitur Unggulan
                </a>
                <a href="#simulasi" class="px-7 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-emerald-400/40 text-emerald-100 font-semibold text-sm backdrop-blur-md transition flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Simulasi Penghematan Operasional
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION 1: URGENSI & PROBLEM STATEMENT -->
    <section id="urgensi" class="py-20 bg-white border-b border-[#E3EED4]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-[#2E7D32] bg-[#E3EED4] px-3 py-1 rounded-full">Latar Belakang & Analisis Urgensi</span>
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-[#0F2A1D] mt-3">
                    Tantangan Pengelolaan Keuangan Tradisional vs Solusi SIPAS-Hub
                </h2>
                <p class="text-gray-600 mt-3 text-base">
                    Pondok pesantren modern membutuhkan efisiensi tinggi tanpa mengorbankan ketertiban syariat dan kenyamanan wali santri.
                </p>
            </div>

            <!-- Comparison Table / Grid -->
            <div class="grid md:grid-cols-2 gap-8 items-stretch">
                <!-- Left: Cara Tradisional / Masalah -->
                <div class="bg-red-50/70 border border-red-200 rounded-3xl p-6 sm:p-8 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center font-bold">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-xl text-red-900">Tata Kelola Konvensional</h3>
                                <p class="text-xs text-red-700">Rentan selisih, lambat, dan membebani pengurus</p>
                            </div>
                        </div>

                        <ul class="space-y-4 text-sm text-gray-700">
                            <li class="flex items-start gap-3">
                                <span class="text-red-500 font-bold text-base mt-0.5">✕</span>
                                <div>
                                    <strong class="text-gray-900">Penitipan Uang Tunai Berisiko:</strong> Santri membawa uang tunai berlebih untuk bayar syahriyah rawan tercecer, hilang, atau terpakai jajan sebelum sampai ke bendahara.
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-red-500 font-bold text-base mt-0.5">✕</span>
                                <div>
                                    <strong class="text-gray-900">Banjir Bukti Transfer di WhatsApp Pengurus:</strong> Wali santri transfer ke rekening pribadi/yayasan lalu mengirim foto struk buram, membuat bendahara lembur mencocokkan mutasi satu-satu.
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-red-500 font-bold text-base mt-0.5">✕</span>
                                <div>
                                    <strong class="text-gray-900">Kwitansi Kertas Sering Hilang:</strong> Saat ada komplain atau klaim sudah bayar, mencari arsip nota fisik memakan waktu berjam-jam dan rentan perselisihan.
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-red-500 font-bold text-base mt-0.5">✕</span>
                                <div>
                                    <strong class="text-gray-900">Pimpinan Buta Kas Real-Time:</strong> Kyai dan Pimpinan Yayasan baru menerima laporan rekapitulasi keuangan di akhir bulan setelah TU selesai menghitung manual.
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 pt-4 border-t border-red-200/60 text-xs text-red-700 font-medium">
                        Dampak: Tingginya piutang tertunda, waktu bendahara habis untuk administrasi, dan risiko salah hitung.
                    </div>
                </div>

                <!-- Right: Solusi SIPAS-Hub -->
                <div class="bg-gradient-to-br from-[#0F2A1D] to-[#1E4A34] text-white rounded-3xl p-6 sm:p-8 flex flex-col justify-between shadow-xl border border-emerald-700">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold border border-emerald-400/30">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-xl text-white">Dengan SIPAS-Hub Pesantren</h3>
                                <p class="text-xs text-emerald-300">Otomatis, akurat, dan dapat diawasi 24 jam</p>
                            </div>
                        </div>

                        <ul class="space-y-4 text-sm text-emerald-100">
                            <li class="flex items-start gap-3">
                                <span class="text-amber-400 font-bold text-base mt-0.5">✓</span>
                                <div>
                                    <strong class="text-white">Pembayaran Digital Langsung (QRIS & VA):</strong> Wali santri langsung bayar dari rumah melalui m-Banking (BCA, Mandiri, BRI, BNI) atau e-Wallet tanpa titip tunai santri.
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-amber-400 font-bold text-base mt-0.5">✓</span>
                                <div>
                                    <strong class="text-white">Otomatis Verifikasi 0 Detik:</strong> Tidak perlu kirim slip transfer ke WhatsApp. Webhook payment gateway langsung menandai tagihan lunas detik itu juga.
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-amber-400 font-bold text-base mt-0.5">✓</span>
                                <div>
                                    <strong class="text-white">Kwitansi PDF Resmi & Permanen:</strong> Bukti pembayaran otomatis terbit dengan nomor unik, barcode, dan tersimpan abadi di cloud yang dapat diunduh kapan saja.
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-amber-400 font-bold text-base mt-0.5">✓</span>
                                <div>
                                    <strong class="text-white">Executive Dashboard di HP Pimpinan:</strong> Pimpinan pesantren dapat memantau total pemasukan hari ini, sisa tunggakan, dan grafik persentase pelunasan secara live.
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 pt-4 border-t border-emerald-700/60 text-xs text-emerald-300 font-medium">
                        Hasil: Efisiensi kerja meningkat 85%, transparansi 100%, serta meningkatkan reputasi pesantren di mata publik.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: 4 PILAR STRATEGIS UNTUK PIMPINAN -->
    <section id="solusi" class="py-20 bg-[#F4F8F3] border-b border-[#E3EED4]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-[#2E7D32] bg-[#E3EED4] px-3 py-1 rounded-full">Nilai Strategis Pesantren</span>
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-[#0F2A1D] mt-3">
                    4 Pilar Utama yang Dihadirkan SIPAS-Hub
                </h2>
                <p class="text-gray-600 mt-3 text-base">
                    Dirancang untuk memenuhi standar tata kelola kepesantrenan yang menjaga amanah ummat.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Pilar 1 -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#E3EED4] hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#E3EED4] text-[#2E7D32] flex items-center justify-center font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-lg text-[#0F2A1D]">1. Integritas & Anti-Fraud</h3>
                    <p class="text-xs text-gray-600 mt-2 leading-relaxed">
                        Dilengkapi <em>Audit Trail otomatis</em> dan <em>Transaction Locking</em> sehingga nominal tagihan yang sudah terbayar tidak dapat diubah sembarangan oleh siapapun.
                    </p>
                </div>

                <!-- Pilar 2 -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#E3EED4] hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#E3EED4] text-[#2E7D32] flex items-center justify-center font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-lg text-[#0F2A1D]">2. Efisiensi Tenaga TU</h3>
                    <p class="text-xs text-gray-600 mt-2 leading-relaxed">
                        Generate ribuan invoice syahriyah bulanan dalam 1x klik menggunakan antrean sistem latar belakang (Background Queue), menghemat puluhan jam kerja staf.
                    </p>
                </div>

                <!-- Pilar 3 -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#E3EED4] hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#E3EED4] text-[#2E7D32] flex items-center justify-center font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-lg text-[#0F2A1D]">3. Layanan Wali Santri</h3>
                    <p class="text-xs text-gray-600 mt-2 leading-relaxed">
                        Portal berbasis smartphone (Mobile-First) yang sangat ringan dan mudah digunakan bahkan oleh orang tua santri yang kurang akrab dengan teknologi.
                    </p>
                </div>

                <!-- Pilar 4 -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#E3EED4] hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-2xl bg-[#E3EED4] text-[#2E7D32] flex items-center justify-center font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-lg text-[#0F2A1D]">4. Keputusan Cepat Kyai</h3>
                    <p class="text-xs text-gray-600 mt-2 leading-relaxed">
                        Pimpinan yayasan & pondok dapat merencanakan pembangunan fisik pesantren atau program santri dengan data proyeksi kas masuk yang akurat.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: SHOWCASE MODUL UNGGULAN & FITUR TEKNIS -->
    <section id="modul" class="py-20 bg-white border-b border-[#E3EED4]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-[#2E7D32] bg-[#E3EED4] px-3 py-1 rounded-full">Katalog Modul Sistem</span>
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-[#0F2A1D] mt-3">
                    Modul-Modul Utama SIPAS-Hub
                </h2>
                <p class="text-gray-600 mt-3 text-base">
                    Dirancang menyeluruh untuk mencakup seluruh siklus operasional akademik dan finansial pesantren.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Modul 1 -->
                <div class="border border-[#E3EED4] rounded-3xl p-6 bg-[#FAFDF9] flex flex-col justify-between hover:border-emerald-500 transition shadow-xs">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-[#0F2A1D] text-amber-300 flex items-center justify-center font-bold mb-4">
                            1
                        </div>
                        <h3 class="font-display font-bold text-lg text-[#0F2A1D]">Smart Tagihan Syahriyah & Iuran Massal</h3>
                        <p class="text-xs text-gray-600 mt-2 mb-4 leading-relaxed">
                            Mendukung berbagai jenis pos pembayaran pesantren:
                        </p>
                        <ul class="text-xs text-gray-700 space-y-2">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Syahriyah / SPP Bulanan Otomatis</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Iuran Uang Makan & Asrama Santri</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Pengadaan Kitab Kuning & Buku Ajar</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Infaq Pembangunan & Kegiatan PHBI / Haflah</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 text-[11px] text-[#2E7D32] font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Fitur Generator Massal (Background Job)
                    </div>
                </div>

                <!-- Modul 2 -->
                <div class="border border-[#E3EED4] rounded-3xl p-6 bg-[#FAFDF9] flex flex-col justify-between hover:border-emerald-500 transition shadow-xs">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-[#0F2A1D] text-amber-300 flex items-center justify-center font-bold mb-4">
                            2
                        </div>
                        <h3 class="font-display font-bold text-lg text-[#0F2A1D]">Gerbang Pembayaran Digital (Payment Gateway)</h3>
                        <p class="text-xs text-gray-600 mt-2 mb-4 leading-relaxed">
                            Integrasi resmi dengan Payment Gateway nasional:
                        </p>
                        <ul class="text-xs text-gray-700 space-y-2">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span><strong>QRIS Nasional:</strong> GoPay, OVO, ShopeePay, DANA</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span><strong>Virtual Account:</strong> BCA, Mandiri, BRI, BNI, BSI</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Notifikasi Webhook aman anti Race Condition</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Opsi Bayar Tunai langsung di meja Kasir TU</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 text-[11px] text-[#2E7D32] font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Otomatis Sinkron 1 Detik
                    </div>
                </div>

                <!-- Modul 3 -->
                <div class="border border-[#E3EED4] rounded-3xl p-6 bg-[#FAFDF9] flex flex-col justify-between hover:border-emerald-500 transition shadow-xs">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-[#0F2A1D] text-amber-300 flex items-center justify-center font-bold mb-4">
                            3
                        </div>
                        <h3 class="font-display font-bold text-lg text-[#0F2A1D]">Portal Khusus Wali Santri (Mobile)</h3>
                        <p class="text-xs text-gray-600 mt-2 mb-4 leading-relaxed">
                            Memberikan ketenangan hati bagi orang tua murid:
                        </p>
                        <ul class="text-xs text-gray-700 space-y-2">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Cek rincian tagihan anak tanpa harus ke pondok</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Satu akun wali bisa mengelola lebih dari 1 santri</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Unduh Kwitansi PDF resmi dengan stempel digital</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Riwayat pembayaran transparan sejak tahun awal</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 text-[11px] text-[#2E7D32] font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Ringan & Akses Mudah di Smartphone
                    </div>
                </div>

                <!-- Modul 4 -->
                <div class="border border-[#E3EED4] rounded-3xl p-6 bg-[#FAFDF9] flex flex-col justify-between hover:border-emerald-500 transition shadow-xs">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-[#0F2A1D] text-amber-300 flex items-center justify-center font-bold mb-4">
                            4
                        </div>
                        <h3 class="font-display font-bold text-lg text-[#0F2A1D]">Manajemen Akademik & Santri</h3>
                        <p class="text-xs text-gray-600 mt-2 mb-4 leading-relaxed">
                            Tertib administrasi santri dan kelas:
                        </p>
                        <ul class="text-xs text-gray-700 space-y-2">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Data santri, NIS, NISN, NIK, dan wali terpusat</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Manajemen kenaikan kelas (Promotion Manager)</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Impor massal dari Excel / data EMIS Kemenag</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Riwayat alumni & mutasi santri tetap terjaga rapi</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 text-[11px] text-[#2E7D32] font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Struktur Database Standar Nasional
                    </div>
                </div>

                <!-- Modul 5 -->
                <div class="border border-[#E3EED4] rounded-3xl p-6 bg-[#FAFDF9] flex flex-col justify-between hover:border-emerald-500 transition shadow-xs">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-[#0F2A1D] text-amber-300 flex items-center justify-center font-bold mb-4">
                            5
                        </div>
                        <h3 class="font-display font-bold text-lg text-[#0F2A1D]">Executive Dashboard & Laporan Keuangan</h3>
                        <p class="text-xs text-gray-600 mt-2 mb-4 leading-relaxed">
                            Visibilitas penuh untuk dewan pimpinan yayasan:
                        </p>
                        <ul class="text-xs text-gray-700 space-y-2">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Statistik pemasukan harian, mingguan, dan bulanan</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Filter rincian tunggakan per kelas / angkatan</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Tingkat kepatuhan pembayaran santri (Payment Rate)</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Ekspor laporan siap cetak ke Excel dan PDF</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 text-[11px] text-[#2E7D32] font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Keputusan Cepat Berbasis Data Nyata
                    </div>
                </div>

                <!-- Modul 6 -->
                <div class="border border-[#E3EED4] rounded-3xl p-6 bg-[#FAFDF9] flex flex-col justify-between hover:border-emerald-500 transition shadow-xs">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-[#0F2A1D] text-amber-300 flex items-center justify-center font-bold mb-4">
                            6
                        </div>
                        <h3 class="font-display font-bold text-lg text-[#0F2A1D]">Audit Trail & Hak Akses Berjenjang</h3>
                        <p class="text-xs text-gray-600 mt-2 mb-4 leading-relaxed">
                            Keamanan sistem dan pemisahan wewenang:
                        </p>
                        <ul class="text-xs text-gray-700 space-y-2">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Role khusus: Pimpinan, Bendahara, Staf TU, Wali</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Pencatatan riwayat perubahan data sensitif</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Proteksi terhadap manipulasi nominal tagihan</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Otentikasi aman dengan standar keamanan modern</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 text-[11px] text-[#2E7D32] font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Menjamin Amanah & Bebas Penyelewengan
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: ALUR KERJA SISTEM (WORKFLOW) -->
    <section id="alur" class="py-20 bg-[#F4F8F3] border-b border-[#E3EED4]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-[#2E7D32] bg-[#E3EED4] px-3 py-1 rounded-full">Sederhana & Efisien</span>
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-[#0F2A1D] mt-3">
                    Bagaimana SIPAS-Hub Beroperasi Sehari-hari?
                </h2>
                <p class="text-gray-600 mt-3 text-base">
                    Alur 4 langkah praktis yang memangkas birokrasi berbelit menjadi serba instan.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 relative">
                <!-- Step 1 -->
                <div class="bg-white rounded-3xl p-6 border border-[#E3EED4] shadow-xs relative">
                    <div class="inline-block px-3 py-1 bg-emerald-100 text-[#2E7D32] text-xs font-bold rounded-lg mb-3">Langkah 1</div>
                    <h3 class="font-display font-bold text-base text-[#0F2A1D]">Terbit Tagihan Otomatis</h3>
                    <p class="text-xs text-gray-600 mt-2">
                        Awal bulan, sistem otomatis menerbitkan tagihan syahriyah untuk seluruh santri aktif sesuai jenjang kelasnya masing-masing.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-3xl p-6 border border-[#E3EED4] shadow-xs relative">
                    <div class="inline-block px-3 py-1 bg-emerald-100 text-[#2E7D32] text-xs font-bold rounded-lg mb-3">Langkah 2</div>
                    <h3 class="font-display font-bold text-base text-[#0F2A1D]">Notifikasi ke Wali Santri</h3>
                    <p class="text-xs text-gray-600 mt-2">
                        Wali santri menerima tagihan di portalnya dan dapat memilih metode bayar termudah: QRIS atau Virtual Account bank pilihan.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-3xl p-6 border border-[#E3EED4] shadow-xs relative">
                    <div class="inline-block px-3 py-1 bg-emerald-100 text-[#2E7D32] text-xs font-bold rounded-lg mb-3">Langkah 3</div>
                    <h3 class="font-display font-bold text-base text-[#0F2A1D]">Pembayaran & Validasi Instan</h3>
                    <p class="text-xs text-gray-600 mt-2">
                        Ketika wali santri mentransfer, sistem bank mengirim sinyal otomatis. Tagihan langsung berubah status menjadi <strong>LUNAS</strong> dalam 1 detik.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-white rounded-3xl p-6 border border-[#E3EED4] shadow-xs relative">
                    <div class="inline-block px-3 py-1 bg-emerald-100 text-[#2E7D32] text-xs font-bold rounded-lg mb-3">Langkah 4</div>
                    <h3 class="font-display font-bold text-base text-[#0F2A1D]">Kwitansi & Laporan Update</h3>
                    <p class="text-xs text-gray-600 mt-2">
                        Wali santri menerima kwitansi digital resmi, dan kas pesantren otomatis bertambah di dashboard pimpinan secara akurat.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 5: INTERACTIVE SIMULATION / EFFICIENCY CALCULATOR -->
    <section id="simulasi" class="py-20 bg-white border-b border-[#E3EED4]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-xs font-bold uppercase tracking-wider text-[#2E7D32] bg-[#E3EED4] px-3 py-1 rounded-full">Simulasi Interaktif untuk Pimpinan</span>
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-[#0F2A1D] mt-3">
                    Hitung Estimasi Efisiensi & Potensi Arus Kas
                </h2>
                <p class="text-gray-600 mt-3 text-base">
                    Geser nilai di bawah ini untuk melihat perkiraan volume transaksi dan waktu operasional staf yang dapat diselamatkan setiap bulannya.
                </p>
            </div>

            <!-- Calculator Component -->
            <div class="max-w-4xl mx-auto bg-gradient-to-br from-[#0F2A1D] to-[#1E4A34] text-white rounded-3xl p-6 sm:p-10 shadow-2xl border border-emerald-600">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <!-- Left: Interactive Inputs -->
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-semibold text-emerald-200">Jumlah Santri Aktif:</label>
                                <span class="font-display font-bold text-amber-300 text-lg" x-text="santriCount + ' Santri'"></span>
                            </div>
                            <input type="range" min="100" max="3000" step="50" x-model.number="santriCount" class="w-full accent-amber-400 cursor-pointer bg-emerald-900/80 rounded-lg h-2" />
                            <div class="flex justify-between text-[10px] text-emerald-400 mt-1">
                                <span>100 Santri</span>
                                <span>1.500 Santri</span>
                                <span>3.000 Santri</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-semibold text-emerald-200">Rata-rata Syahriyah / Santri:</label>
                                <span class="font-display font-bold text-amber-300 text-lg" x-text="formatRupiah(sppNominal)"></span>
                            </div>
                            <input type="range" min="100000" max="2500000" step="50000" x-model.number="sppNominal" class="w-full accent-amber-400 cursor-pointer bg-emerald-900/80 rounded-lg h-2" />
                            <div class="flex justify-between text-[10px] text-emerald-400 mt-1">
                                <span>Rp 100 rb</span>
                                <span>Rp 1.25 jt</span>
                                <span>Rp 2.5 jt</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-950/70 border border-emerald-700/50 text-xs text-emerald-200 space-y-1">
                            <div class="flex items-center gap-1.5 text-amber-300 font-semibold">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                Analisis Dampak Produktivitas
                            </div>
                            <p>
                                Otomatisasi ini menghemat rata-rata <strong class="text-white">4.5 menit per santri</strong> dalam proses pencatatan, verifikasi mutasi, dan pencetakan kwitansi kertas.
                            </p>
                        </div>
                    </div>

                    <!-- Right: Calculated Results -->
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-emerald-400/30 space-y-6 text-center">
                        <div>
                            <div class="text-xs uppercase tracking-wider font-semibold text-emerald-300">Estimasi Arus Kas Syahriyah / Bulan</div>
                            <div class="font-display font-extrabold text-2xl sm:text-3xl text-amber-300 mt-1" x-text="formatCurrency"></div>
                            <div class="text-[11px] text-emerald-200 mt-0.5">Tercatat 100% rapi di sistem tanpa risiko selisih</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-emerald-700/60">
                            <div class="p-3 rounded-xl bg-emerald-950/60 border border-emerald-800">
                                <div class="font-display font-bold text-xl text-emerald-300" x-text="jamHemat + ' Jam'"></div>
                                <div class="text-[10px] text-emerald-200 mt-0.5">Waktu TU Dihemat / Bln</div>
                            </div>
                            <div class="p-3 rounded-xl bg-emerald-950/60 border border-emerald-800">
                                <div class="font-display font-bold text-xl text-emerald-300">0 Lembar</div>
                                <div class="text-[10px] text-emerald-200 mt-0.5">Kwitansi Kertas Hilang</div>
                            </div>
                        </div>

                        <div class="text-[11px] text-emerald-300/80 italic">
                            * Waktu staf yang dihemat dapat dialihkan untuk peningkatan mutu tarbiyah dan pelayanan santri.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 6: PERSPEKTIF PENGGUNA (ROLE PERSPECTIVE TABS) -->
    <section class="py-20 bg-[#F4F8F3] border-b border-[#E3EED4]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-[#2E7D32] bg-[#E3EED4] px-3 py-1 rounded-full">Kenyamanan Multi-Pengguna</span>
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-[#0F2A1D] mt-3">
                    Manfaat Nyata Bagi Seluruh Elemen Pesantren
                </h2>
                <p class="text-gray-600 mt-3 text-base">
                    Satu platform yang dirancang presisi untuk memenuhi kebutuhan setiap pemangku kepentingan.
                </p>
            </div>

            <!-- Role Selector Buttons -->
            <div class="flex justify-center flex-wrap gap-3 mb-10">
                <button @click="activeRoleTab = 'pimpinan'" :class="activeRoleTab === 'pimpinan' ? 'bg-[#0F2A1D] text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Bagi Pengasuh & Pimpinan Yayasan
                </button>
                <button @click="activeRoleTab = 'bendahara'" :class="activeRoleTab === 'bendahara' ? 'bg-[#0F2A1D] text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Bagi Bendahara & Staf Tata Usaha
                </button>
                <button @click="activeRoleTab = 'wali'" :class="activeRoleTab === 'wali' ? 'bg-[#0F2A1D] text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Bagi Wali Santri & Orang Tua
                </button>
            </div>

            <!-- Tab 1: Pimpinan -->
            <div x-show="activeRoleTab === 'pimpinan'" x-cloak class="bg-white rounded-3xl p-8 border border-[#E3EED4] shadow-sm">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div>
                        <div class="inline-block px-3 py-1 bg-amber-100 text-amber-900 rounded-lg text-xs font-bold mb-3">Perspektif Pengasuh & Yayasan</div>
                        <h3 class="font-display font-bold text-2xl text-[#0F2A1D]">Kendali Finansial & Ketenteraman Pesantren</h3>
                        <p class="text-sm text-gray-600 mt-3 leading-relaxed">
                            Pimpinan tidak perlu lagi menunggu sampai ada masalah keuangan untuk bertindak. Visibilitas menyeluruh tersedia dalam hitungan detik.
                        </p>
                        <div class="mt-6 space-y-3">
                            <div class="flex items-start gap-3 text-sm text-gray-700">
                                <span class="text-[#2E7D32] font-bold">✓</span>
                                <span><strong>Pengawasan Arus Kas 24 Jam:</strong> Akses pantauan dana masuk kapan saja melalui laptop atau HP Kyai.</span>
                            </div>
                            <div class="flex items-start gap-3 text-sm text-gray-700">
                                <span class="text-[#2E7D32] font-bold">✓</span>
                                <span><strong>Pencegahan Kebocoran:</strong> Sistem menutup celah penyelewengan dana syahriyah dengan log audit digital.</span>
                            </div>
                            <div class="flex items-start gap-3 text-sm text-gray-700">
                                <span class="text-[#2E7D32] font-bold">✓</span>
                                <span><strong>Keputusan Pembangunan Terarah:</strong> Mengetahui точно perkiraan dana masuk untuk program ma'had.</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#0F2A1D] text-white p-6 rounded-2xl border border-emerald-700">
                        <div class="text-xs uppercase tracking-wider text-emerald-300 font-semibold mb-2">Pesan Inti untuk Pimpinan</div>
                        <blockquote class="italic text-sm text-emerald-100 leading-relaxed">
                            "SIPAS-Hub mengembalikan marwah transparansi syariat dalam muamalah pondok, memastikan setiap rupiah infaq dan syahriyah tercatat dengan amanah dan berkah."
                        </blockquote>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Bendahara / TU -->
            <div x-show="activeRoleTab === 'bendahara'" x-cloak class="bg-white rounded-3xl p-8 border border-[#E3EED4] shadow-sm">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div>
                        <div class="inline-block px-3 py-1 bg-emerald-100 text-[#2E7D32] rounded-lg text-xs font-bold mb-3">Perspektif Bendahara & TU</div>
                        <h3 class="font-display font-bold text-2xl text-[#0F2A1D]">Bebas dari Lembur & Tumpukan Kuitansi</h3>
                        <p class="text-sm text-gray-600 mt-3 leading-relaxed">
                            Tugas rutin yang dulu memakan waktu berhari-hari kini selesai dalam beberapa klik saja.
                        </p>
                        <div class="mt-6 space-y-3">
                            <div class="flex items-start gap-3 text-sm text-gray-700">
                                <span class="text-[#2E7D32] font-bold">✓</span>
                                <span><strong>Generate SPP Otomatis:</strong> Tagihan satu pesantren terbit seketika di awal bulan.</span>
                            </div>
                            <div class="flex items-start gap-3 text-sm text-gray-700">
                                <span class="text-[#2E7D32] font-bold">✓</span>
                                <span><strong>Rekonsiliasi Bank Otomatis:</strong> Tidak perlu cek mutasi rekening satu per satu secara manual.</span>
                            </div>
                            <div class="flex items-start gap-3 text-sm text-gray-700">
                                <span class="text-[#2E7D32] font-bold">✓</span>
                                <span><strong>Ekspor Laporan Cepat:</strong> Siap cetak laporan keuangan dalam format PDF & Excel saat rapat evaluasi.</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#0F2A1D] text-white p-6 rounded-2xl border border-emerald-700">
                        <div class="text-xs uppercase tracking-wider text-emerald-300 font-semibold mb-2">Efisiensi Nyata Staf TU</div>
                        <blockquote class="italic text-sm text-emerald-100 leading-relaxed">
                            "Staf tata usaha tidak lagi terbebani melayani antrean uang tunai ratusan santri, sehingga dapat lebih fokus pada pengelolaan akademik dan pengarsipan berkas."
                        </blockquote>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Wali Santri -->
            <div x-show="activeRoleTab === 'wali'" x-cloak class="bg-white rounded-3xl p-8 border border-[#E3EED4] shadow-sm">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div>
                        <div class="inline-block px-3 py-1 bg-blue-100 text-blue-900 rounded-lg text-xs font-bold mb-3">Perspektif Wali Santri</div>
                        <h3 class="font-display font-bold text-2xl text-[#0F2A1D]">Tenang, Praktis, & Nyaman dari Mana Saja</h3>
                        <p class="text-sm text-gray-600 mt-3 leading-relaxed">
                            Orang tua santri yang berada di luar kota tidak perlu khawatir mengenai pembayaran administrasi anaknya.
                        </p>
                        <div class="mt-6 space-y-3">
                            <div class="flex items-start gap-3 text-sm text-gray-700">
                                <span class="text-[#2E7D32] font-bold">✓</span>
                                <span><strong>Bayar Lewat HP:</strong> Cukup scan QRIS atau bayar via m-Banking kapan saja 24/7.</span>
                            </div>
                            <div class="flex items-start gap-3 text-sm text-gray-700">
                                <span class="text-[#2E7D32] font-bold">✓</span>
                                <span><strong>Kuitansi Langsung Terbit:</strong> Bukti bayar tersimpan aman dan bisa diunduh kapan pun dibutuhkan.</span>
                            </div>
                            <div class="flex items-start gap-3 text-sm text-gray-700">
                                <span class="text-[#2E7D32] font-bold">✓</span>
                                <span><strong>Transparansi Penuh:</strong> Rincian biaya jelas tanpa ada kekhawatiran uang santri hilang di jalan.</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#0F2A1D] text-white p-6 rounded-2xl border border-emerald-700">
                        <div class="text-xs uppercase tracking-wider text-emerald-300 font-semibold mb-2">Kepuasan Wali Santri</div>
                        <blockquote class="italic text-sm text-emerald-100 leading-relaxed">
                            "Wali santri merasa tenang menyekolahkan anaknya karena pesantren dikelola secara profesional, modern, dan sangat transparan."
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 7: FREQUENTLY ASKED QUESTIONS FOR EXECUTIVES (FAQ) -->
    <section id="faq" class="py-20 bg-white border-b border-[#E3EED4]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-xs font-bold uppercase tracking-wider text-[#2E7D32] bg-[#E3EED4] px-3 py-1 rounded-full">Pertanyaan Khas Pimpinan & Dewan Pengurus</span>
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-[#0F2A1D] mt-3">
                    Tanya Jawab Seputar Implementasi
                </h2>
                <p class="text-gray-600 mt-3 text-base">
                    Jawaban lugas atas keraguan teknis dan operasional yang sering ditanyakan.
                </p>
            </div>

            <div class="space-y-4" x-data="{ openFaq: 1 }">
                <!-- FAQ 1 -->
                <div class="border border-[#E3EED4] rounded-2xl p-5 bg-[#FAFDF9] transition">
                    <button @click="openFaq = openFaq === 1 ? 0 : 1" class="w-full flex justify-between items-center text-left font-display font-bold text-base text-[#0F2A1D] cursor-pointer">
                        <span>1. Apakah uang pembayaran wali langsung masuk ke rekening Pesantren?</span>
                        <svg class="w-5 h-5 text-[#2E7D32] transform transition-transform" :class="openFaq === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 1" x-cloak class="mt-3 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                        <strong>Ya, betul.</strong> Integrasi pembayaran resmi Midtrans disalurkan langsung ke rekening resmi yayasan/pesantren yang didaftarkan. Sistem SIPAS-Hub hanya bertindak sebagai pencatat dan verifikator otomatis tanpa memotong atau menahan dana pesantren.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="border border-[#E3EED4] rounded-2xl p-5 bg-[#FAFDF9] transition">
                    <button @click="openFaq = openFaq === 2 ? 0 : 2" class="w-full flex justify-between items-center text-left font-display font-bold text-base text-[#0F2A1D] cursor-pointer">
                        <span>2. Bagaimana jika masih ada wali santri di desa yang belum terbiasa m-Banking / QRIS?</span>
                        <svg class="w-5 h-5 text-[#2E7D32] transform transition-transform" :class="openFaq === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 2" x-cloak class="mt-3 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                        Sistem SIPAS-Hub tetap menyediakan <strong>fitur Pembayaran Kasir Manual / Tunai di TU</strong>. Staf TU cukup mencari nama santri, klik terima uang tunai, dan kwitansi resmi langsung dicetak detik itu juga. Jadi wali santri sepuh tetap terlayani dengan sangat baik.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="border border-[#E3EED4] rounded-2xl p-5 bg-[#FAFDF9] transition">
                    <button @click="openFaq = openFaq === 3 ? 0 : 3" class="w-full flex justify-between items-center text-left font-display font-bold text-base text-[#0F2A1D] cursor-pointer">
                        <span>3. Seberapa aman data santri dan transaksi keuangan dari kebocoran?</span>
                        <svg class="w-5 h-5 text-[#2E7D32] transform transition-transform" :class="openFaq === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 3" x-cloak class="mt-3 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                        SIPAS-Hub dibangun dengan standar keamanan industri: enkripsi kata sandi tingkat tinggi, proteksi terhadap <em>Parameter Tampering</em> dan <em>Timing Attacks</em>, serta pencatatan jejak audit (Audit Trail) lengkap untuk mencegah manipulasi internal.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="border border-[#E3EED4] rounded-2xl p-5 bg-[#FAFDF9] transition">
                    <button @click="openFaq = openFaq === 4 ? 0 : 4" class="w-full flex justify-between items-center text-left font-display font-bold text-base text-[#0F2A1D] cursor-pointer">
                        <span>4. Berapa lama proses migrasi data dari pembukuan lama ke SIPAS-Hub?</span>
                        <svg class="w-5 h-5 text-[#2E7D32] transform transition-transform" :class="openFaq === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 4" x-cloak class="mt-3 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                        Sangat cepat! Data santri dan wali yang ada di Excel atau format EMIS Kemenag dapat langsung diimpor secara massal ke dalam sistem dalam waktu kurang dari 1 hari kerja tanpa harus mengetik ulang satu per satu.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 8: FINAL EXECUTIVE CALL TO ACTION & PRINT FOOTER -->
    <section class="py-20 bg-islamic-pattern text-white relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-8">
            <div class="inline-flex items-center gap-2 px-4 py-1 rounded-full bg-emerald-900/80 border border-emerald-500/40 text-amber-300 text-xs font-semibold">
                KESIMPULAN EKSEKUTIF
            </div>

            <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-white">
                Siap Membawa Tata Kelola Pesantren ke Tingkat Tertinggi?
            </h2>

            <p class="text-emerald-100/90 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                Kombinasi nilai-nilai amanah syariah dan teknologi otomasi modern untuk mewujudkan kemandirian finansial dan kemuliaan pesantren.
            </p>

            <div class="pt-4 flex flex-wrap justify-center gap-4 no-print">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-8 py-4 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-[#0F2A1D] font-bold text-base shadow-xl transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <span>Buka Dashboard Pimpinan</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-4 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-[#0F2A1D] font-bold text-base shadow-xl transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <span>Mulai Masuk Aplikasi (Login)</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @endauth

                <button onclick="window.print()" class="px-7 py-4 rounded-xl bg-white/10 hover:bg-white/20 border border-emerald-400/40 text-emerald-100 font-semibold text-base backdrop-blur-md transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Ringkasan Eksekutif (PDF)</span>
                </button>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-[#08170F] text-emerald-300/80 py-10 border-t border-emerald-900 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg bg-emerald-700 text-amber-300 flex items-center justify-center font-bold text-xs">
                    S
                </div>
                <span class="text-white font-semibold">SIPAS-Hub Pesantren</span>
                <span class="text-emerald-500">• Digitalisasi Keuangan Amanah</span>
            </div>
            <div class="text-center sm:text-right text-emerald-400/70">
                &copy; {{ date('Y') }} SIPAS-Hub. Dikembangkan khusus untuk tata kelola pondok pesantren modern.
            </div>
        </div>
    </footer>

</body>
</html>
