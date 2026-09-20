PEMIRA '{{ substr((string) $election->year, -2) }} — E-VOTING RESMI
==================================================

PENGINGAT PEMBUKAAN VOTING
Voting Akan Dimulai 3 Jam Lagi

Halo {{ $voterName }},

Pemungutan suara untuk {{ $election->name }} akan segera dibuka dalam waktu 3 jam ke depan.

Jadwal Pemungutan Suara:
- Nama Pemilihan : {{ $election->name }}
- Tanggal Voting : {{ $votingDate }}
- Jam Mulai      : {{ $votingStartTime }}

Pemberian suara dapat dilakukan secara online melalui portal resmi PEMIRA segera setelah voting dibuka.

Akses halaman voting melalui tautan berikut:
{{ $votingUrl }}

--------------------------------------------------
Email otomatis oleh Komisi Pemilihan Raya (KPR).
