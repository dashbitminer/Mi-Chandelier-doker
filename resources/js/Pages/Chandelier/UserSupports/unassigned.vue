<template>
  <main class='m-auto px-8 w-full md:px-0 md:w-9/12'>
      <img src="/chandelier-logo-2.png" class='my-8 m-auto 2xs:w-48 sm:w-60 md:w-72' />

      <p class='my-4'>
        ¡Hola!
      </p>

      <p class='my-4'>
        Notamos que tienes una cuenta de correo @glasswing.org, pero aún no se ha cargado tu información en el sistema Brilo. Esto significa que, por ahora, no puedes acceder a todas las funcionalidades de Chandelier.
      </p>

      <p class='my-4'>
        No te preocupes, el equipo del departamento de administración está trabajando en ello, y en los próximos días tu información será integrada. Una vez que esto suceda, podrás disfrutar de todos los beneficios de Chandelier.
      </p>

      <p class='my-4'>
        Mientras tanto, lamentamos cualquier inconveniente que esto pueda causarte. Si necesitas asistencia técnica o tienes alguna pregunta, no dudes en escribirnos a soporteit@glasswing.org. Estamos aquí para ayudarte.
      </p>

      <p class='my-4'>
        ¡Gracias por tu paciencia!
      </p>

      <p class='mt-8'>
        
        <LinkToPrimary :href="'/'">Volver a la página de inicio</LinkToPrimary>
      </p>
  </main>
</template>
<script setup>
import LinkToPrimary from "@/Components/Chandelier/Common/LinkTo.vue";
</script>
