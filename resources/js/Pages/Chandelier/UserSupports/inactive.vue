<template>
  <main class='m-auto px-8 w-full md:px-0 md:w-9/12'>
      <img src="/chandelier-logo-2.png" class='my-8 m-auto 2xs:w-48 sm:w-60 md:w-72' />

      <p class='my-4'>
        ¡Hola!
      </p>

      <p class='my-4'>
        Hemos detectado que tu cuenta ha sido deshabilitada temporalmente. Esto puede deberse a motivos de seguridad o ajustes administrativos en el sistema.
      </p>

      <p class='my-4'>
        Si crees que esto es un error o necesitas más información, por favor contáctanos escribiendo a soporteit@glasswing.org. Estamos aquí para ayudarte a resolver cualquier inconveniente lo más pronto posible.
      </p>

      <p class='my-4'>
        Gracias por tu comprensión y paciencia.
      </p>

      <p class='mt-8'>
        
        <LinkToPrimary :href="'/'">Volver a la página de inicio</LinkToPrimary>
      </p>
  </main>
</template>
<script setup>
import LinkToPrimary from "@/Components/Chandelier/Common/LinkTo.vue";
</script>
