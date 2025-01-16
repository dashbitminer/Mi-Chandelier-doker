<!DOCTYPE html>
<html>
<head>
  <title>Informe de usuarios para hojas de tiempo</title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    table, th, td {
      border: 1px solid black;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }
    .page-break {
      page-break-after: always;
    }
  </style>
</head>
<body>
<h1>Informe de usuarios para hojas de tiempo</h1>
<ul>
  <li>Proyecto: <strong>{{$project['name']}}</strong></li>
  <li>Hoja de tiempo: <strong>{{$timeSheetTemplate['title']}}</strong></li>
</ul>


@if (in_array('incompleted', $statuses))
    <h4>Usuarios pendiente de crear sus hojas de tiempo</h4>
  <table>
    <thead>
    <tr>
      <th>Usuario</th>
      <th>Supervisor</th>
    </tr>
    </thead>
    <tbody>
      @foreach($timeSheetProjectsIncompleted as $timeSheetProject)
      <tr>
        <td>
          {{ $timeSheetProject['userName'] }}
          <br />
          {{ $timeSheetProject['userEmail'] }}
        </td>
        <td>
          {{ $timeSheetProject['reviewerName'] }}
          <br />
          {{ $timeSheetProject['reviewerEmail'] }}
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  @if (in_array('completed', $statuses))

    <h4>Usuarios pendiente de revision de sus hojas de tiempo</h4>
  <table>
    <thead>
    <tr>
      <th>Usuario</th>
      <th>Supervisor</th>
    </tr>
    </thead>
    <tbody>
    @foreach($timeSheetProjectsCompleted as $timeSheetProject)
      <tr>
        <td>
          {{ $timeSheetProject['userName'] }}
          <br />
          {{ $timeSheetProject['userEmail'] }}
        </td>
        <td>
          {{ $timeSheetProject['reviewerName'] }}
          <br />
          {{ $timeSheetProject['reviewerEmail'] }}
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
   @endif

  @if (in_array('approved', $statuses))

    <h4>Usuarios con proceso completo de revision de sus hojas de tiempo</h4>
  <table>
    <thead>
    <tr>
      <th>Usuario</th>
      <th>Supervisor</th>
    </tr>
    </thead>
    <tbody>
    @foreach($timeSheetProjectsApproved as $timeSheetProject)
      <tr>
        <td>
          {{ $timeSheetProject['userName'] }}
          <br />
          {{ $timeSheetProject['userEmail'] }}
        </td>
        <td>
          {{ $timeSheetProject['reviewerName'] }}
          <br />
          {{ $timeSheetProject['reviewerEmail'] }}
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
   @endif

</body>
</html>
