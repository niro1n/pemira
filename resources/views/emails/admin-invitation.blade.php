<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Administrator PEMIRA 2026</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F7F5EF; margin: 0; padding: 24px; color: #111111;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 560px; background-color: #FFFFFF; border: 2px solid #111111; box-shadow: 4px 4px 0px #111111;">
        <tr>
            <td style="background-color: #062B4C; padding: 20px 24px; border-bottom: 2px solid #111111;">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <span style="font-size: 20px; font-weight: 800; color: #FFFFFF; letter-spacing: 1px;">PEMIRA</span>
                            <span style="font-size: 20px; font-weight: 800; color: #D4A12C; margin-left: 4px;">'26</span>
                        </td>
                        <td align="right">
                            <span style="background-color: #D4A12C; color: #111111; font-size: 10px; font-weight: 700; padding: 4px 8px; text-transform: uppercase; border: 1px solid #111111;">PORTAL OPERASIONAL</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 28px 24px;">
                <div style="display: inline-block; background-color: #062B4C; color: #FFFFFF; font-size: 11px; font-weight: 700; padding: 3px 8px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                    UNDANGAN AKSES RESMI
                </div>
                <h2 style="font-size: 18px; font-weight: 800; color: #062B4C; margin-top: 0; margin-bottom: 14px; text-transform: uppercase;">
                    Undangan Pembuatan Akun Administrator
                </h2>
                <p style="font-size: 14px; line-height: 1.6; margin-bottom: 16px; color: #111111;">
                    Halo,
                </p>
                <p style="font-size: 14px; line-height: 1.6; margin-bottom: 20px; color: #111111;">
                    Anda menerima undangan untuk membuat akun Admin PEMIRA dari <strong>{{ $inviterName }}</strong>. Melalui akun ini, Anda akan memiliki hak akses administratif untuk membantu pengelolaan operasional sistem pemilihan.
                </p>
                <div style="text-align: center; margin-bottom: 28px;">
                    <a
                        href="{{ $acceptUrl }}"
                        style="display: inline-block; background-color: #062B4C; color: #FFFFFF; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding: 14px 28px; text-decoration: none; border: 2px solid #111111; box-shadow: 3px 3px 0px #D4A12C;"
                    >
                        TERIMA UNDANGAN &rarr;
                    </a>
                </div>
                <div style="background-color: #F7F5EF; border: 2px solid #111111; padding: 14px 16px; margin-bottom: 24px;">
                    <p style="font-size: 12px; line-height: 1.5; color: #333333; margin: 0;">
                        Tautan undangan ini hanya berlaku selama <strong>24 jam</strong> (hingga <strong>{{ $expiresAtFormatted }}</strong>) dan hanya dapat digunakan <strong>satu kali</strong>.
                    </p>
                    <p style="font-size: 11px; word-break: break-all; color: #062B4C; margin-top: 10px; margin-bottom: 0;">
                        Jika tombol di atas tidak dapat diklik, salin dan buka tautan berikut pada peramban Anda:<br>
                        <span style="font-family: monospace; color: #111111;">{{ $acceptUrl }}</span>
                    </p>
                </div>
                <p style="font-size: 12px; line-height: 1.6; color: #666666; margin-bottom: 0;">
                    Jika Anda tidak merasa berhak atau tidak mengenali undangan ini, abaikan email ini dan jangan berikan tautan di atas kepada siapapun.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #062B4C; padding: 14px 24px; border-top: 2px solid #111111; text-align: center;">
                <span style="font-size: 11px; color: #FFFFFF; opacity: 0.8;">
                    &copy; {{ date('Y') }} Komisi Pemilihan Raya (KPR) Politeknik Negeri Bali
                </span>
            </td>
        </tr>
    </table>
</body>
</html>
