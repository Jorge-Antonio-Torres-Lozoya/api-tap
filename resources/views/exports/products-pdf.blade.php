<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 120px 40px 80px 40px;
        }

        * {
            font-family: 'Helvetica', Arial, sans-serif;
        }

        body {
            margin: 0;
            color: #1a1a1a;
            font-size: 11px;
        }

        /* Fixed header on every page */
        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            height: 70px;
        }

        .brand-bar {
            background-color: #1a1a1a;
            padding: 16px 24px;
            border-radius: 6px;
        }

        .brand-bar .logo {
            font-size: 20px;
            font-weight: bold;
            color: #ffffff;
        }

        .brand-bar .logo .accent {
            color: #FFC72C;
        }

        .brand-bar .subtitle {
            color: #9ca3af;
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .brand-bar .doc-info {
            color: #ffffff;
            font-size: 10px;
            text-align: right;
        }

        .brand-bar .doc-info .date {
            color: #9ca3af;
        }

        /* Fixed footer on every page */
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 40px;
            color: #9ca3af;
            font-size: 9px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }

        footer .pagenum:before {
            content: "Página " counter(page);
        }

        main {
            margin-top: 24px;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 4px;
            color: #1a1a1a;
        }

        .summary {
            color: #6b7280;
            font-size: 10px;
            margin: 0 0 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background-color: #FFC72C;
            color: #1a1a1a;
            text-align: left;
            padding: 9px 10px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <header>
        <div class="brand-bar">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <span class="logo">GRUPO<span class="accent">TAP</span></span><br>
                        <span class="subtitle">Terminal Portuaria</span>
                    </td>
                    <td class="doc-info">
                        <span class="date">Generado: {{ now()->format('d/m/Y H:i') }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </header>

    <footer>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>Grupo TAP — Terminal Portuaria, Manzanillo, Colima.</td>
                <td class="text-right"><span class="pagenum"></span></td>
            </tr>
        </table>
    </footer>

    <main>
        <h1>Listado de Productos</h1>
        <p class="summary">Total de registros: {{ count($products) }}</p>

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th class="text-right">Precio</th>
                    <th>Fecha de Creación</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->code }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->brand }}</td>
                        <td class="text-right">${{ number_format($product->price) }}</td>
                        <td>{{ $product->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:20px; color:#9ca3af;">
                            No hay productos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>

</html>
