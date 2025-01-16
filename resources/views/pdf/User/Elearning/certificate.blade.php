<!DOCTYPE html>
<html>
<head>
    <title>Certificado de participación</title>

    <style>
      @font-face {
        font-family: 'Montserrat';
        src: url('{{ storage_path('fonts/Montserrat-Light.ttf') }}') format('truetype');
        font-optical-sizing: auto;
        font-weight: 300;
        font-style: normal;
      }

      @font-face {
        font-family: 'Montserrat';
        src: url('{{ storage_path('fonts/Montserrat-Regular.ttf') }}');
        font-optical-sizing: auto;
        font-weight: 400;
        font-style: normal;
      }

      @font-face {
        font-family: 'Montserrat';
        src: url('{{ storage_path('fonts/Montserrat-Bold.ttf') }}');
        font-optical-sizing: auto;
        font-weight: 700;
        font-style: normal;
      }

      @font-face {
        font-family: 'Montserrat';
        src: url('{{ storage_path('fonts/Montserrat-ExtraBold.ttf') }}');
        font-optical-sizing: auto;
        font-weight: 800;
        font-style: normal;
      }

      @font-face {
        font-family: 'Montserrat-italic';
        src: url('{{ storage_path('Montserrat-LightItalic.ttf') }}');
        font-optical-sizing: auto;
        font-weight: 300;
        font-style: italic;
      }

      .italic {
        font-family: 'Montserrat-italic';
        font-weight: 300;
        font-style: italic;
      }

      *, *::before, *::after{
        box-sizing: border-box;
      }

      *{
        margin: 0;
        padding: 0;
      }

      ul[role='list'], ol[role='list']{
        list-style: none;
      }

      html:focus-within{
        scroll-behavior: smooth;
      }

      a:not([class]){
        text-decoration-skip-ink: auto;
      }

      img, picture, svg, video, canvas{
        max-width: 100%;
        height: auto;
        vertical-align: middle;
        font-style: italic;
        background-repeat: no-repeat;
        background-size: cover;
      }

      input, button, textarea, select{
        font: inherit;
      }

      @media (prefers-reduced-motion: reduce){
        html:focus-within {
            scroll-behavior: auto;
        }
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
            transition: none;
        }
      }

      body, html{
        height: 100%;
        scroll-behavior: smooth;
      }
      body {
        font-family: 'Montserrat';
        font-weight: 400;
        text-align: center;
        font-size: 20px;
        padding: 80px;
        padding-bottom: 5px;
        background-image: url('pdf-background.png');
        background-repeat: repeat-x;
        background-position: bottom;
        background-size: auto;
      }
      h1, h2, h3 {
        font-family: 'Montserrat';
        font-weight: 800;
        font-style: normal;
      }

      .title {
        font-weight: 700;
        font-size: 56px;
        letter-spacing: 10px;
        color: #00748c;
      }

      .subtitle {
        font-size: 14px;
        font-weight: 300;
        color: #00748c;
        padding-bottom: 25px;
      }

      hr {
        height: 1px;
        background: #00748c;
        width: 70%;
        margin: 0 auto;
        border: 0;
        position: relative;
        margin-top: 10px;
        margin-bottom: 50px;
      }

      hr::before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        top: -10px;
        background-color: #00748c;
        left: 50%;
        transform: translateX(-50%);
        transform: rotate(45deg);
        z-index: 2;
      }

      hr::after {
        content: '';
        position: absolute;
        width: 80px;
        height: 40px;
        top: -22px;
        background-color: #fefefe;
        left: 52%;
        transform: translateX(-50%);
        z-index: 1;
      }

      .username {
        font-weight: 800;
        color: #00748c;
        padding: 15px 20px;
        display: inline-block;
        border-bottom: 1px solid #000;
        margin-bottom: 40px;
      }

      .course-title {
        font-weight: 300;
        color: #00748c;
        margin-top: 10px;
        margin-bottom: 30px;
      }

      .footer {
        margin-top: 35px;
      }

      .logo {
        margin-top: 10px;
        width: 60%;
      }
    </style>
</head>
<body>
    <main>
      <h1 class="title">CERTIFICADO</h1>
      <h3 class="subtitle">DE PARTICIPACIÓN</h3>
      <hr>
      <p class="text">Se le otorga el presente certificado a:</p>
      <h2 class="username">{{ $user->name }}</h2>
      <p class="text">Por haber completado satisfactoriamente el curso:</p>
      <h2 class="course-title">{{ $course->name }}</h2>
      <p class="text">A los {{ $endDate }}</p>
      <p class="text footer">
        Este certificado acrédita la dedicación y el esfruerzo <br>
        demostrado en el aprendizaje y desarrollo de nuevas <br>
        habilidades
      </p>
      <img class="logo" src="chandelier-logo-pdf.png" alt="">
    </main>
</body>
</html>
