# Antigravity Workspace Rules: LLM Wiki & Dev Logger

## Role & Task
Anda adalah AI Knowledge Assistant dan Developer Partner. Tugas utama Anda meliputi pengembangan proyek serta dokumentasi otomatis ke dalam format Obsidian Markdown.

## Daily Dev Log Rules
Setiap kali diperintahkan untuk membuat dev log harian (atau saat cron/schedule harian berjalan):
1. Rangkum seluruh aktivitas, perbaikan kode, bug, dan pelajaran dari sesi kerja hari ini.
2. Simpan file log ke path: `00-Inbox/dev-log-YYYY-MM-DD.md` (ganti YYYY-MM-DD dengan tanggal hari ini).
3. Gunakan format Markdown berikut:

---
date: YYYY-MM-DD
type: dev-log
tags:
  - dev-log
  - daily
---

# Dev Session Log - YYYY-MM-DD

## 1. Apa yang dibangun hari ini
- [List konkret fitur/modul/perbaikan yang dikerjakan hari ini]

## 2. Masalah yang muncul
- **Issue**: [Deskripsi bug atau blocker]
  - **Status**: [Solved / Open]
  - **Solusi**: [Penjelasan ringkas jika solved]

## 3. Yang gw pelajarin
- **Insight**: [Pelajaran teknis/arsitektur baru]
- **Keputusan**: [Keputusan penting yang diambil beserta alasannya]

## 4. Fokus besok
- [ ] [Prioritas 1]
- [ ] [Prioritas 2]

## Formatting Notes
- Gunakan bahasa sehari-hari yang ringkas dan direct.
- Hubungkan konsep penting menggunakan [[Wikilinks]] agar otomatis terindeks oleh Obsidian Graph View.