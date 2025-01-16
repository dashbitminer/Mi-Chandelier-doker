<template>
  <aside>
    <nav>
      <div
        class="bg-white 2xs:border-1 xs:border-2 2xl:border-2 shadow-md rounded-2xl flex flex-col h-[95vh] overflow-y-scroll sm:w-0 2xl:w-72 md:w-72 2xs:w-0">
        <Link :href="'/'">
          <img class="p-8" src="/chandelier-logo-2.png"/>
        </Link>
        <hr>
        <ul>
          <SideNavItem entry="Inicio" icon="mdi mdi-home" target="/"/>

          <li class='m-2' v-if="isAbleBriloSignIn()">
            <a :href="briloSignInUrl()" class="block py-4 px-4 rounded-md hover:transition-colors hover:bg-primary-100 transition"
                 target='_blank' rel="noopener noreferrer">
                <i class="text-slate-700 group-hover:text-slate-700 ms-3 text-xl mdi mdi-open-in-new" />
                <span class="ms-3 text-slate-600 group-hover:text-slate-600 text-nowrap">
                    Ir a Brilo
                </span>
            </a>
          </li>

          <hr v-if="isAbleSection('operaciones')" class="mb-2">
          <SidenavSectionTitle v-if="isAbleSection('operaciones')">Operaciones</SidenavSectionTitle>
          <SideNavItem v-if="isAblePermission('operaciones.solicitudes-viaje')" entry="Solicitudes de viaje"
                       icon="mdi mdi-wallet-travel" :target="`/${currentCountry}/travel-requests`"/>
          <SideNavItem v-if="isAblePermission('operaciones.hojas-tiempo')" entry="Hojas de tiempo"
                       icon="mdi mdi-archive-clock-outline" :target="`/${currentCountry}/time-sheets`"/>

          <hr v-if="isAbleSection('formaciones')" class="mb-2">
          <SidenavSectionTitle v-if="isAbleSection('formaciones')">Formaciones</SidenavSectionTitle>
          <SideNavItem v-if="isAblePermission('formaciones.cursos')" entry="Cursos disponibles"
                       icon="mdi mdi-certificate" :target="`/${currentCountry}/user/elearning/courses`"/>
          <SideNavItem v-if="isAblePermission('formaciones.cursos')" entry="Cursos completados"
                       icon="mdi mdi-certificate-outline"
                       :target="`/${currentCountry}/user/elearning/courses/completed`"/>
          <SideNavItem v-if="isAblePermission('formaciones.centro-ayuda')" entry="Centro de ayuda"
                       icon="mdi mdi-folder-question-outline"
                       :target="`/${currentCountry}/user/elearning/help-center/`"/>

          <hr v-if="isAbleSection('contabilidad')" class="mb-2">
          <SidenavSectionTitle v-if="isAbleSection('contabilidad')">Contabilidad</SidenavSectionTitle>
          <SideNavItem v-if="isAblePermission('contabilidad.proyectos')" entry="Proyectos"
                       icon="mdi mdi-account-multiple" :target="`/${currentCountry}/accounting/projects`"/>
          <SideNavItem v-if="isAblePermission('contabilidad.hojas-tiempo')" entry="Hojas de tiempo"
                       icon="mdi mdi-archive-clock-outline"
                       :target="`/${currentCountry}/accounting/time-sheet-templates`"/>
          <SideNavItem v-if="isAblePermission('contabilidad.tipo-pago')" entry="Tipo de pago"
                       icon="mdi mdi-cash-multiple" :target="`/${currentCountry}/accounting/payment-periods`"/>
          <SideNavItem v-if="isAblePermission('contabilidad.solicitudes-viaje')" entry="Solicitudes de viaje"
                       icon="mdi mdi-wallet-travel" :target="`/${currentCountry}/accounting/travel-requests`"/>

          <hr v-if="isAbleSection('admin.formaciones')" class="mb-2">
          <SidenavSectionTitle v-if="isAbleSection('admin.formaciones')">Administración de Formaciones
          </SidenavSectionTitle>
          <SideNavItem v-if="isAblePermission('admin.formaciones.categorias')" entry="Categorias"
                       icon="mdi mdi-file-tree" :target="`/${currentCountry}/elearning/categories`"/>
          <SideNavItem v-if="isAblePermission('admin.formaciones.cursos')" entry="Cursos" icon="mdi mdi-certificate"
                       :target="`/${currentCountry}/elearning/courses`"/>
          <SideNavItem v-if="isAblePermission('admin.formaciones.cursos-eliminados')" entry="Cursos eliminados"
                       icon="mdi mdi-trash-can-outline" :target="`/${currentCountry}/elearning/deleted-courses`"/>

          <hr v-if="isAbleSection('admin')" class="mb-2">
          <SidenavSectionTitle v-if="isAbleSection('admin')">Administrador
          </SidenavSectionTitle>
          <SideNavItem v-if="isAblePermission('admin.usuarios')" entry="Usuarios"
                       icon="mdi mdi-account-multiple" :target="`/${currentCountry}/backoffice/users`"/>
        </ul>
      </div>
    </nav>
  </aside>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3"
import SideNavItem from "@/Components/Chandelier/SideNav/SideNavItem.vue"
import SidenavSectionTitle from "@/Components/Chandelier/SideNav/SideNavSectionTitle.vue"
import { useCountry } from "@/Composables/useCountry";

const { props } = usePage()

const isAbleSection = (section) => {
  return props.sections && props.sections.includes(section);
}

const isAblePermission = (permission) => {
  return props.permissions && props.permissions.includes(permission);
}

const isAbleBriloSignIn = () => {
  return props.brilo && props.brilo.url !== null && props.brilo.token !== null;
}

const briloSignInUrl = () => {
  return [props.brilo.url, 'Account/LogOnTknExt?tkn=', props.brilo.token].join('')
}

const {currentCountry} = useCountry()
</script>
