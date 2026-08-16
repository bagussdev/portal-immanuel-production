<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atur Ulang Password</title>
</head>
<body style="margin:0;padding:0;background:#f0f9ff;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">Gunakan tautan aman ini untuk membuat password baru Portal Immanuel Production.</div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f0f9ff;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:620px;background:#ffffff;border:1px solid #dbeafe;border-radius:24px;overflow:hidden;box-shadow:0 18px 45px rgba(15,23,42,.12);">
                    <tr><td style="height:5px;background:linear-gradient(90deg,#0284c7,#ef4444,#0284c7);font-size:0;line-height:0;">&nbsp;</td></tr>
                    <tr>
                        <td style="background:#08111f;padding:28px 36px;text-align:center;">
                            <img src="<?php echo e(asset('assets/brand/immanuel-production-white-logo.png')); ?>" width="150" alt="Immanuel Production" style="display:block;width:150px;max-width:100%;height:auto;margin:0 auto 10px;">
                            <div style="font-size:12px;font-weight:800;letter-spacing:2px;color:#ffffff;">PORTAL IMMANUEL PRODUCTION</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px 42px 20px;">
                            <div style="display:inline-block;padding:7px 12px;border-radius:999px;background:#e0f2fe;color:#0369a1;font-size:10px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;">Pemulihan akun</div>
                            <h1 style="margin:18px 0 12px;font-size:28px;line-height:1.25;color:#0f172a;">Atur ulang password kamu</h1>
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.75;color:#475569;">Halo <strong style="color:#0f172a;"><?php echo e($recipientName); ?></strong>,</p>
                            <p style="margin:0;font-size:15px;line-height:1.75;color:#475569;">Kami menerima permintaan untuk membuat password baru pada akun Portal Immanuel Production. Tekan tombol berikut untuk melanjutkan.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:12px 42px 26px;">
                            <a href="<?php echo e($resetUrl); ?>" style="display:inline-block;border-radius:12px;background:#0284c7;color:#ffffff;text-decoration:none;font-size:15px;font-weight:800;padding:15px 28px;box-shadow:0 8px 18px rgba(2,132,199,.22);">Buat Password Baru</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 42px 28px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;">
                                <tr>
                                    <td style="padding:18px 20px;font-size:13px;line-height:1.65;color:#64748b;">
                                        <strong style="display:block;margin-bottom:4px;color:#334155;">Tautan berlaku <?php echo e($expiresIn); ?> menit</strong>
                                        Demi keamanan, tautan hanya dapat digunakan untuk akun ini. Jika sudah kedaluwarsa, minta tautan baru dari halaman login.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 42px 34px;">
                            <p style="margin:0 0 8px;font-size:12px;line-height:1.6;color:#64748b;">Jika tombol tidak dapat ditekan, salin alamat berikut ke browser:</p>
                            <p style="margin:0;padding:12px 14px;border-radius:10px;background:#f1f5f9;font-size:11px;line-height:1.6;color:#0369a1;word-break:break-all;"><?php echo e($resetUrl); ?></p>
                            <p style="margin:22px 0 0;font-size:13px;line-height:1.65;color:#64748b;">Jika kamu tidak meminta reset password, abaikan email ini. Password akunmu tidak berubah.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top:1px solid #e2e8f0;background:#f8fafc;padding:22px 36px;text-align:center;font-size:11px;line-height:1.6;color:#94a3b8;">
                            Email otomatis dari Portal Immanuel Production.<br>Mohon tidak membalas email ini.
                        </td>
                    </tr>
                </table>
                <p style="margin:18px 0 0;font-size:11px;color:#94a3b8;">&copy; <?php echo e(date('Y')); ?> Immanuel Production</p>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\emails\auth\reset-password.blade.php ENDPATH**/ ?>