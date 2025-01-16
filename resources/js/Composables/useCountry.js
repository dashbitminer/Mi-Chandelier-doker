import { computed, ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

export function useCountry(providedCountries = null) {
    const page = usePage()
    const defaultCountry = 'SLV'
    const currentCountry = computed(() => page.props.currentCountry || defaultCountry)

    const countries = ref({})
    const isLoading = ref(false)
    const error = ref(null)

    const fetchCountries = async () => {
      isLoading.value = true
      error.value = null

      try {
          const response = await fetch('/countries')
          if (!response.ok) {
              throw new Error('Error al cargar los países')
          }
          countries.value = await response.json()
      } catch (err) {
          error.value = err.message
      } finally {
          isLoading.value = false
      }
    }

    const changeCountry = (newCountry) => {
        const currentPath = window.location.pathname
        const newPath = currentPath.replace(/^\/[^\/]+/, `/${newCountry}`)
        router.visit(newPath, {
            preserveScroll: true
        })
    }

    const visitRoute = (name, params = {}, options = {}) => {
        router.visit(route(name, {
            country: currentCountry.value,
            ...params
        }), options)
    }

    return {
      currentCountry,
      countries,
      isLoading,
      error,
      fetchCountries,
      changeCountry,
      visitRoute,
    }
}

