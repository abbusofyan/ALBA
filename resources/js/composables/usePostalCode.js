// ✅ usePostalCode.js
import { ref, watch } from 'vue'
import { useApi } from '@/composables/useApi'

export function usePostalCode(form) {
    const selectedLocation = ref(null)
    const loading = ref(false)  // <-- Add loading state

    function onPostalCodeInput(event) {
        const digits = event.target.value.replace(/\D/g, '').slice(0, 6)
        form.postal_code = digits
    }

    watch(() => form.postal_code, async (newVal) => {
        form.address = null
        selectedLocation.value = null
        form.errors.postal_code = null

        if (newVal && newVal.length === 6) {
            loading.value = true  // <-- Start loading

            const { data, error } = await useApi(`/onemap/getLocationByPostalCode/${newVal}`).json()

            if (error.value) {
                form.errors.postal_code = error.value.status === 404
                    ? 'Postal code not found'
                    : 'Failed to fetch location'
                loading.value = false  // <-- Stop loading
                return
            }

            const location = data.value?.data?.location
            if (location) {
                form.address = location.ADDRESS
                selectedLocation.value = {
                    lat: parseFloat(location.LATITUDE),
                    lng: parseFloat(location.LONGITUDE),
                }
                form.errors.postal_code = null
            }

            loading.value = false  // <-- Stop loading
        }
    })

    return {
        onPostalCodeInput,
        selectedLocation,
        loading  // <-- Return loading state
    }
}
