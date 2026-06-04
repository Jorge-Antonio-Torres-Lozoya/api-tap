<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperación de contraseña</title>
</head>
<body>
    <h2>Recuperación de contraseña — Grupo TAP</h2>
    <p>Recibimos una solicitud para restablecer tu contraseña.</p>
    <p>Usa el siguiente enlace (válido por 60 minutos):</p>
    <p>
        <a href="{{ config('app.frontend_url') }}/reset-password?token={{ $token }}">
            Restablecer contraseña
        </a>
    </p>
    <p>Si no solicitaste este cambio, ignora este mensaje.</p>
</body>
</html>
