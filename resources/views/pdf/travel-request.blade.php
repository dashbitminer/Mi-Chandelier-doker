<!DOCTYPE html>
<html>
<head>
  <title>Solicitudes de viaje</title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }
    td {
      padding: 0;
      text-align: left;
      vertical-align: top;
    }

    ul{
      padding: 0;
      margin: 0;
      list-style: none;
    }
    .h2{
      font-weight: bolder;
    }
    .text-right{
      text-align: right;
    }
    .page-break {
      page-break-after: always;
    }

    .expenses,
    .signatures{
      margin-top: 20px;
    }

    .expenses td,
    .expenses th {
      border: 1px solid #718096;
    }

    .signatures table {
      width: 80%
    }

    .signatures td {
      width: 50%;
      padding-right: 20px;
    }
    .signatures label{
      display: block;
      border-bottom: solid 1px #718096;
    }
  </style>
</head>
<body>
  <div class="header">
    <strong>Solicitud de viaje</strong>
    <ul>
      <li>Proyecto: {{$travelRequest['projectName']}}</li>
      <li>Solicitante: {{$travelRequest['userName']}}</li>
      <li>Fecha inicio: {{$travelRequest['departureDateFormatted']}}</li>
      <li>Fecha fin: {{$travelRequest['arrivalDateFormatted']}}</li>
      <li>Total: {{$travelRequest['totalFormatted']}}</li>
      <li>Descripcion: {{$travelRequest['description']}}</li>
    </ul>
  </div>

  <div class="expenses">
    <div class="h2">
      Gastos del viaje
    </div>
    <table width="100%">
      <tr>
        <th>Tipo</th>
        <th>Monto</th>
        <th>Comentario</th>
      </tr>
      @foreach($travelRequest['expenses'] as $expense)
        <tr>
          <td>{{$expense['expenseKindName']}}</td>
          <td>{{$expense['amountFormatted']}}</td>
          <td>{{$expense['comment']}}</td>
        </tr>
      @endforeach
    </table>
  </div>

  <div class="signatures">
    <table>
      <tr>
        <td>
          <label>F.</label>
          <ul>
            <li>Colaborador: {{$travelRequest['userName']}}</li>
            <li>Cargo: {{$travelRequest['userPosition']}}</li>
          </ul>
        </td>
        <td>
          <label>F.</label>
          <ul>
            <li>Supervisor: {{$travelRequest['reviewerName']}}</li>
            <li>Cargo: {{$travelRequest['reviewerPosition']}}</li>
          </ul>
        </td>
      </tr>
    </table>
  </div>

  <div class="footer">
  </div>

</body>
</html>
