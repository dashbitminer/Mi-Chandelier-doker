<script setup>
import Menu from "@/Layouts/menu/Menu.vue"
import SideNav from "@/Layouts/sidenav/SideNav.vue"
import AppFooter from "@/Layouts/Footer.vue"
</script>

<template>

  <body class="flex w-screen overflow-x-scroll">
    <SideNav class="xs:p-2 sm:p-2 xl:p-5 z-30" />
    <div class="flex-1 pe-5 z-30 h-screen overflow-y-scroll">
      <Menu class="flex-1" />
      <slot />
      <AppFooter />
    </div>
    <img class="fixed bottom-0 right-0 max-w-60 z-10 opacity-10 m-5" src="/logo-glasswing.png" />
    <div class="fixed h-full w-full opacity-10" style="background-image: url('/bg.jpg');"></div>
  </body>
</template>
