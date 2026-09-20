PEMIRA '{{ substr((string) $election->year, -2) }} — E-VOTING RESMI
==================================================

PENGINGAT PENUTUPAN VOTING
Voting Berakhir 1 Jam Lagi

Halo {{ $voterName }},

Anda belum menggunakan hak suara pada PEMIRA ini.

Waktu pemungutan suara untuk {{ $election->name }} akan segera ditutup. Anda masih memiliki kesempatan untuk memberikan suara sebelum batas waktu berakhir.

Batas Waktu Pemungutan Suara:
- Nama Pemilihan : {{ $election->name }}
- Waktu Berakhir : {{ $votingEndTime }}

Gunakan hak suara Anda sekarang melalui tautan berikut:
{{ $votingUrl }}

--------------------------------------------------
Email otomatis oleh Komisi Pemilihan Raya (KPR).
