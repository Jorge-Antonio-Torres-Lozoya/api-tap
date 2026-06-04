<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de contraseña</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f3f5; font-family:Arial, Helvetica, sans-serif;">
    @php
        $resetUrl = config('app.frontend_url') . '/reset-password?token=' . $token;
    @endphp

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f3f5; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color:#1a1a1a; padding:32px 40px; text-align:center;">
                            <span style="color:#ffffff; font-size:26px; font-weight:bold; letter-spacing:1px;">GRUPO</span><span style="color:#FFC72C; font-size:26px; font-weight:bold; letter-spacing:1px;">TAP</span>
                            <p style="margin:6px 0 0; color:#9ca3af; font-size:12px; letter-spacing:2px; text-transform:uppercase;">Terminal Portuaria</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:40px;">
                            <h1 style="margin:0 0 16px; color:#1a1a1a; font-size:22px;">Recuperación de contraseña</h1>
                            <p style="margin:0 0 16px; color:#4b5563; font-size:15px; line-height:1.6;">
                                Hola, recibimos una solicitud para restablecer la contraseña de tu cuenta.
                                Haz clic en el siguiente botón para crear una nueva contraseña.
                            </p>

                            <!-- Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0;">
                                <tr>
                                    <td align="center" style="border-radius:6px; background-color:#FFC72C;">
                                        <a href="{{ $resetUrl }}" target="_blank"
                                           style="display:inline-block; padding:14px 36px; color:#1a1a1a; font-size:15px; font-weight:bold; text-decoration:none; border-radius:6px;">
                                            Restablecer contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 8px; color:#6b7280; font-size:13px; line-height:1.6;">
                                Este enlace es válido por <strong>60 minutos</strong>. Si expira, deberás solicitar uno nuevo.
                            </p>
                            <p style="margin:0 0 24px; color:#6b7280; font-size:13px; line-height:1.6;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:
                            </p>
                            <p style="margin:0 0 24px; word-break:break-all;">
                                <a href="{{ $resetUrl }}" style="color:#2563eb; font-size:13px;">{{ $resetUrl }}</a>
                            </p>

                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:24px 0;">

                            <p style="margin:0; color:#9ca3af; font-size:13px; line-height:1.6;">
                                Si no solicitaste este cambio, ignora este correo. Tu contraseña permanecerá sin cambios.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f9fafb; padding:24px 40px; text-align:center; border-top:1px solid #e5e7eb;">
                            <p style="margin:0; color:#9ca3af; font-size:12px; line-height:1.5;">
                                Grupo TAP — Terminal Portuaria<br>
                                Banda C, Tramo 9, Puerto Interior de San Pedrito, Manzanillo, Colima.
                            </p>
                            <p style="margin:8px 0 0; color:#c1c5cb; font-size:11px;">
                                Este es un correo automático, por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
