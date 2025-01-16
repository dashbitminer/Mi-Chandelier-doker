<template>

  <li class='m-2'>
    <Link :href="target" class="block py-4 px-4 rounded-md hover:transition-colors hover:bg-primary-100 transition"
        :class="{ 'bg-primary-500': isSelected(target) }">
        <i class="text-slate-700 group-hover:text-slate-700 ms-3 text-xl"
            :class="{ 'text-zinc-100': isSelected(target) }, [icon]" />
        <span class="ms-3 text-slate-600 group-hover:text-slate-600 text-nowrap"
            :class="{ 'text-zinc-100': isSelected(target) }">
            {{ entry }}
        </span>
    </Link>
  </li>
</template>
<script setup>
import { Link } from "@inertiajs/vue3"
</script>

<script>

export default {
    name: "SidenavItem",
    props: {
        entry: {
            type: String,
            required: true
        },
        icon: {
            type: String,
            required: true
        },
        target: {
            type: String,
            required: false
        }
    },
    computed: {
        currentUrl() {
            return this.$page.url
        }
    },
    methods: {
        isSelected(target) {

          return (this.currentUrl.startsWith(target) && target !== "/");
        }
    }
}
</script>
