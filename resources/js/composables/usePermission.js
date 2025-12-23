import {computed} from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermission(){
    const page = usePage(); //obtiene el acceso a la pagina actual

    const user = computed(() => page.props.auth.user) //obtiene los datos del usuario

    const roles = computed(() => user.value?.roles || []); //obtiene los roles del usuario

    const permissions = computed(() => user.value?.permissions || []); //obtiene los permisos del usuario

    /*ROLES*/
    //verifica que el usuario tenga un rol en especifico
    const hasRole = (role) => {
        return roles.value.includes(role);
    }
    //verifica que tenga al menos uno de los roles
    const hasAnyRole = (rolesArray) => {
        return rolesArray.some(role => roles.value.includes(role));
    }
    //verifica si el usuario tiene todos los roles
    const hasAllRoles = (rolesArray) => {
        return rolesArray.every(role => roles.value.includes(role));
    }

    /*PERMISOS*/
    //verifica que el usuario tenga un permiso en especifico
    const can = (permission) => {
        return permissions.value.includes(permission);
    }
    //verifica que al menos tenga uno de los permisos
    const canAny = (permissionsArray) => {
        return permissionsArray.some(permission => permissions.value.includes(permission));
    }
    //verifica si tiene todos los permisos
    const canAll = (permissionsArray) => {
        return permissionsArray.every(permission => permissions.value.includes(permission));
    }

    //verifica si el usuario esta autentificado
    const isAuthenticated = computed(() => !!user.value);

    return{
        user,
        roles,
        permissions,
        hasRole,
        hasAnyRole,
        hasAllRoles,
        can,
        canAny,
        canAll,
        isAuthenticated,
    }

}
