import { computed } from 'vue';

export function usePermissions() {
  const permissions = computed(() => {
    return window.$page.props.permissions || [];
  });

  const can = (permission) => {
    return permissions.value.includes(permission);
  };

  return {
    permissions,
    can,
  };
}
