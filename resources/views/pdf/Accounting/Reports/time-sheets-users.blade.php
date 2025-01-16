<!DOCTYPE html>
<html>
<head>
  <title>Hojas de tiempo</title>
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

    .summary,
    .weeks,
    .signatures,
    .footer{
      margin-top: 10px;
    }

    .weeks td {
      border: 1px solid #718096;
    }
    .weeks td label{
      padding: 2px 5px;
      border: solid 1px #718096;
      display: block;
    }
    .weeks td label span{
      margin-left: 2px;
      display: inline-block;
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

    .footer td {
      width: 50%;
      padding: 2px 10px;
    }
  </style>
</head>
<body>
  @php
    $counter = 0;
  @endphp
  
  @foreach($timeSheetProjects as $timeSheetProject)
    @php
      $counter += 1;
    @endphp
    <div class="header">
      <strong>Hojas de tiempo</strong>
      <ul>
        <li>Nombre del proyecto: {{$timeSheetProject['projectName']}}</li>
        <li>Colaborador: {{$timeSheetProject['timeSheet']['userName']}}</li>
        <li>Cargo: --</li>
        <li>Periodo reportado: {{$timeSheetProject['timeSheet']['title']}}</li>
      </ul>
    </div>

    <div class="weeks">
      @foreach($timeSheetProject['weeks'] as $week)
        <div class="h2">
          {{$week['title']}}
        </div>
        <table width="100%">
          <tr>
            <td width="23%">
              <label>Dia</label>
              <label>Fecha</label>
              <label>Hora</label>
            </td>

            @foreach($week['days'] as $day)
              @if($day['enable'] == true)
                <td width="11%">
                  <label>{{$day['dayName']}}</label>
                  <label>{{$day['day']}}</label>
                  <label>
                    {{$day['hours']}}
                    @if(!is_null($day['absenceTypeCode']))
                      <span>({{$day['absenceTypeCode']}})</span>
                    @endif
                  </label>
                </td>
              @else
                <td width="11%"></td>
              @endif
            @endforeach
        </table>
        <div class="text-right">
          Total: {{$week['hours']}} Horas
        </div>
        @if($week['totalWorkingDays'] > 0)
          <div>
            Descripcion de las tareas de las semana:
            <br />
            <em>{!! $week['comment'] !!}</em>
            <br />
            <br />
          </div>
        @endif
      @endforeach
    </div>

    <div class="summary">
      <ul>
        <li>Total horas trabajadas: {{$timeSheetProject['hours']}} Horas</li>
        <li>Porcentaje de salario: {{$timeSheetProject['percentage']}}%</li>
      </ul>
    </div>

    <div class="signatures">
      <table>
        <tr>
          <td>
            <label>F.</label>
            <ul>
              <li>Colaborador: {{$timeSheetProject['timeSheet']['userName']}}</li>
              <li>Cargo: {{$timeSheetProject['timeSheet']['userPosition']}}</li>
            </ul>
          </td>
          <td>
            <label>F.</label>
            <ul>
              <li>Supervisor: {{$timeSheetProject['timeSheet']['reviewerName']}}</li>
              <li>Cargo: {{$timeSheetProject['timeSheet']['reviewerPosition']}}</li>
            </ul>
          </td>
        </tr>
      </table>
    </div>

    <div class="footer">
      <table>
        <tr>
          <td>
            Nomenclatura:
            <ul>
              @foreach($absenceTypes as $absenceType)
                <li>({{$absenceType['code']}}) {{$absenceType['name']}}</li>
              @endforeach
            </ul>
          </td>
          <td>
            Comentarios:
            <div>{{$timeSheetProject['timeSheet']['comment']}}</div>
          </td>
        </tr>
      </table>
    </div>
    @if($counter < count($timeSheetProjects))
      <div class="page-break"></div>
    @endif
  @endforeach

</body>
</html>
