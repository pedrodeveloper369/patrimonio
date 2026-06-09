<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
        @page {
            margin: 100px 40px;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
        }
    </style>
</head>
<body>

<header>
    <strong>Minha Empresa</strong><br>
    Relatório de Património
    <hr>
</header>

<h1>{{ $titulo }}</h1>
<p>Data: {{ $data }}</p>

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($itens as $item)
            <tr>
                <td>{{ $item['nome'] }}</td>
                <td>{{ $item['estado'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Relatório gerado automaticamente
</div>

</body>
</html>
