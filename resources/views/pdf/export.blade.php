<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
</head>
<body style="width: 100%;">
    <h1>{{ $title }}</h1>
    <p>Relatório gerado em: {{ $date }}</p>

    <table style="width: 100%;border: 1px solid black;">
        <tr style="width: 100%;">
            <th style="width: 50%; text-align: left;">
                Inscrição Cód.
            </th>
            <th style="width: 50%; text-align: left;">
                Nome do aluno
            </th>
        </tr>
        <tbody style="width: 100%;">
            @foreach($items as $item)
            <tr style="width: 100%; border: 1px solid black !important;">
                <td style="width: 50%; text-align: left;">
                    {{ $item['register_num'] }}
                </td>
                <td style="width: 50%; text-align: left;">
                    {{ $item['student_name'] }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
