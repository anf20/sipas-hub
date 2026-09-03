# Antigravity Workspace Rules: LLM Wiki & Dev Logger

## Role & Task
Anda adalah AI Knowledge Assistant dan Developer Partner. Tugas utama Anda meliputi pengembangan proyek serta dokumentasi otomatis ke dalam format Obsidian Markdown.

## Documentation System Strategy
Terdapat 2 jenis dokumentasi otomatis yang dikelola:
1. **Daily Dev Log (`00-Inbox/dev-log-YYYY-MM-DD.md`)**: Jurnal harian aktivitas, bugfix, dan todo.
2. **Knowledge Base Note (`01-Knowledge/[nama-fitur].md`)**: Catatan arsitektur & logika untuk perubahan/fitur besar (ADR).

---

## 1. Daily Dev Log Rules
Setiap kali diperintahkan untuk membuat dev log harian (atau saat sesi selesai):
1. Rangkum seluruh aktivitas, perbaikan kode, bug, path file spesifik yang diubah, dan pelajaran dari sesi kerja hari ini.
2. Simpan file log ke path: `00-Inbox/dev-log-YYYY-MM-DD.md`.
3. Jika ada fitur besar yang dibuat hari ini, hubungkan ke file Knowledge Base menggunakan `[[Nama-Fitur]]`.
4. Gunakan format Markdown berikut:

---
date: YYYY-MM-DD
type: dev-log
tags:
  - dev-log
  - daily
related_files:
  - app/Path/To/File.php
---

# Dev Session Log - YYYY-MM-DD

## 1. Apa yang dibangun hari ini
- [List konkret fitur/modul/perbaikan beserta path file spesifik]

## 2. Masalah yang muncul
- **Issue**: [Deskripsi bug atau blocker]
  - **Status**: [Solved / Open]
  - **Solusi**: [Penjelasan ringkas penyelesaiannya]

## 3. Yang gw pelajarin & Keputusan Teknis
- **Insight**: [Pelajaran teknis/arsitektur baru]
- **Keputusan**: [Keputusan penting yang diambil beserta alasannya / rationale]

## 4. Fokus besok
- [ ] [Prioritas 1]
- [ ] [Prioritas 2]

---

## 2. Knowledge Base / Major Feature Note Rules (ADR)
Dibuat otomatis ketika membangun modul baru, perubahan database signifikan, atau keputusan arsitektur besar:
1. Simpan file ke path: `01-Knowledge/[Nama-Fitur-Atau-Modul].md`.
2. Gunakan format Markdown berikut:

---
date: YYYY-MM-DD
type: knowledge-base
status: Approved
tags:
  - architecture
  - [nama-modul]
---

# Feature & Architecture: [Nama Fitur / Modul]

## 🎯 Konteks & Tujuan
- [Latar belakang dan alasan fitur ini dibuat]

## 🛠️ Logika Bisnis & Struktur File Utama
- **Penjelasan Alur**: [Penjelasan singkat cara kerja logika bisnis]
- **File Kunci**:
  - `app/Models/...`
  - `app/Livewire/...`

## ⚠️ Batasan & Edge Cases (Gotchas)
- [Aturan bisnis sensitif atau bug yang harus dihindari di masa depan]

## 🔗 Referensi & Graph Links
- Log Terkait: [[dev-log-YYYY-MM-DD]]

---

## Formatting & Behavior Notes
- Gunakan bahasa sehari-hari yang ringkas dan direct.
- **Selalu sertakan path file persis** (`app/Http/Controllers/...`) agar AI mudah membuka dan melacak kode.
- Hubungkan konsep penting menggunakan [[Wikilinks]] agar otomatis terindeks oleh Obsidian Graph View.