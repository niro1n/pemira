<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $election->name }} — Voting Akan Dimulai</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F7F5EF; margin: 0; padding: 24px; color: #111111;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 560px; background-color: #FFFFFF; border: 2px solid #111111; box-shadow: 4px 4px 0px #111111;">
        <tr>
            <td style="background-color: #062B4C; padding: 20px 24px; border-bottom: 2px solid #111111;">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <span style="font-size: 20px; font-weight: 800; color: #FFFFFF; letter-spacing: 1px;">PEMIRA</span>
                            <span style="font-size: 20px; font-weight: 800; color: #D4A12C; margin-left: 4px;">'{{ substr((string) $election->year, -2) }}</span>
                        </td>
                        <td align="right">
                            <span style="background-color: #D4A12C; color: #111111; font-size: 10px; font-weight: 700; padding: 4px 8px; text-transform: uppercase; border: 1px solid #111111;">E-VOTING RESMI</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 28px 24px;">
                <div style="display: inline-block; background-color: #062B4C; color: #FFFFFF; font-size: 11px; font-weight: 700; padding: 3px 8px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                    PENGINGAT PEMBUKAAN VOTING
                </div>
                <h2 style="font-size: 20px; font-weight: 800; color: #062B4C; margin-top: 0; margin-bottom: 14px; text-transform: uppercase;">
                    Voting Akan Dimulai 3 Jam Lagi
                </h2>
                <p style="font-size: 14px; line-height: 1.6; margin-bottom: 16px; color: #111111;">
                    Halo <strong>{{ $voterName }}</strong>,
                </p>
                <p style="font-size: 14px; line-height: 1.6; margin-bottom: 20px; color: #111111;">
                    Pemungutan suara untuk <strong>{{ $election->name }}</strong> akan segera dibuka dalam waktu 3 jam ke depan. Pastikan Anda bersiap untuk menggunakan hak pilih Anda secara jujur, adil, dan rahasia.
                </p>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #F7F5EF; border: 2px solid #111111; margin-bottom: 24px;">
                    <tr>
                        <td style="padding: 16px 18px;">
                            <div style="font-size: 11px; font-weight: 700; color: #062B4C; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">JADWAL PEMUNGUTAN SUARA</div>
                            <table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size: 13px; color: #111111;">
                                <tr>
                                    <td width="35%" style="font-weight: 600;">Nama Pemilihan:</td>
                                    <td>{{ $election->name }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Tanggal Voting:</td>
                                    <td>{{ $votingDate }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Jam Mulai:</td>
                                    <td><strong>{{ $votingStartTime }}</strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <p style="font-size: 13px; line-height: 1.6; margin-bottom: 24px; color: #333333;">
                    Pemberian suara dapat dilakukan secara online melalui portal resmi PEMIRA segera setelah voting dibuka.
                </p>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
                    <tr>
                        <td align="center">
                            <a href="{{ $votingUrl }}" target="_blank" style="display: inline-block; background-color: #062B4C; color: #FFFFFF; text-decoration: none; font-size: 14px; font-weight: 700; padding: 14px 28px; border: 2px solid #111111; box-shadow: 3px 3px 0px #D4A12C; text-transform: uppercase; letter-spacing: 0.5px;">
                                MENUJU HALAMAN VOTING &rarr;
                            </a>
                        </td>
                    </tr>
                </table>
                <p style="font-size: 12px; line-height: 1.6; color: #666666; margin-bottom: 0;">
                    Email ini dikirimkan secara otomatis oleh sistem kepada pemilih yang telah terdaftar dan terverifikasi. Harap jaga kerahasiaan akun Anda.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #062B4C; padding: 14px 24px; border-top: 2px solid #111111; text-align: center;">
                <span style="font-size: 11px; color: #FFFFFF; opacity: 0.8;">
                    &copy; {{ date('Y') }} Komisi Pemilihan Raya (KPR)
                </span>
            </td>
        </tr>
    </table>
</body>
</html>
