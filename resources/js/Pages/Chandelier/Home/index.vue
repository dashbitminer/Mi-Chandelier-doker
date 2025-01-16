<script setup>

import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ChandelierPage from '@/Components/Chandelier/Common/Page.vue';
import AreaTitle from "@/Components/Chandelier/Common/AreaTitle.vue";
import AreaSubTitle from "@/Components/Chandelier/Common/AreaSubTitle.vue";

const page = usePage()
const auth = computed(() => page.props.auth)

defineOptions({
  layout: MainLayout,
});
</script>

<template>
  <main>
    <ChandelierPage>
      <AreaTitle class="ms-10 mb-2">
        Hola {{ auth.user.name.split(" ")[0] }}!
      </AreaTitle>
      <div class="flex p-4">
        <img class="rounded-2xl object-cover xl:w-1/3 sm:w-1/4 xs:w-1/4 aspect-w-3 aspect-h-2 shadow-lg" src="/images/home1.jpg">
        <div class="ms-6 xl:w-2/3 sm:w-full">
          <p>
            Este es un lugar donde cada rincón ha sido pensado para iluminar su camino y potenciar su impacto en el
            mundo.
            Aquí, no solo encontrarán herramientas y recursos diseñados para facilitar su trabajo diario, sino también
            un
            reflejo de nuestra visión compartida y el compromiso profundo hacia la misión que nos une.
          </p>
          <br/>

          <p>
            En cada clic, en cada tarea, en cada interacción, recuerden que están contribuyendo a construir un futuro
            mejor
            para los niños y jóvenes que dependen de nuestra ayuda. Cada proyecto que emprenden, cada esfuerzo que
            hacen,
            cada
            sonrisa que provocan es una chispa de luz que transforma vidas y enciende esperanzas.
          </p>
          <br/>

          <p>
            Gracias por ser parte de esta travesía. Su pasión, dedicación y amor son los pilares sobre los que se
            edifica
            el
            sueño de Glasswing. Juntos, con la ayuda de Chandelier, seguiremos creando un mundo donde cada niño y joven
            pueda
            alcanzar su máximo potencial.
          </p>
          <br/>

          <p>
            Les damos la más cálida bienvenida a esta plataforma que hemos creado con tanto cariño, y les agradecemos
            por
            ser
            la esencia de nuestra misión. Estamos emocionados de compartir este viaje con ustedes y de ver las
            maravillas
            que
            lograremos juntos.
          </p>
          <br/>

          <p>
            ¡Bienvenidos a Chandelier, y gracias por hacer brillar el futuro!
          </p>
          <br/>

          <p>
            Con aprecio y gratitud,
            <br/>
            El equipo de Glasswing
          </p>
        </div>
      </div>
    </ChandelierPage>
  </main>
</template>
