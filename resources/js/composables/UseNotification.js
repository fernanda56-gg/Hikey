import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useNotification() {

    const page = usePage()

    const user = computed(() => page.props?.auth?.user)

    const notificationCount = computed(() => Math.min(user.value?.notificationCount ?? 0, 9))

    return {
        notificationCount,                
    }
}
