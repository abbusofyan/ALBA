<template>
<div class="layout-wrapper layout-blank" data-allow-mismatch="">
    <div class="misc-wrapper">

		<VCard class="mb-5 pt-3 px-5">
			<div class="d-flex justify-center mb-6">
				<img src="/images/logo/logo-long.png" alt="" style="width:166px; height:56px;">
			</div>
		</VCard>

		<VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-6">
			<VCardText>
				<ErrorHeader :status-code="code" :title="title" :description="description" />
			</VCardText>
			<VCardText>
				<VForm @submit.prevent="form.post('/login')">
						<VBtn block @click="router.get(route('home'))"> Back to Home </VBtn>
				</VForm>
			</VCardText>
		</VCard>
    </div>
</div>

</template>

<style scoped>
.layout-wrapper {
    background: url('/public/images/logo/background-login.png') no-repeat center center;
    background-size: cover;
}
</style>

<script setup>
import {
    computed
} from 'vue'
import {
    useGenerateImageVariant
} from '@core/composable/useGenerateImageVariant'
import misc404 from '@images/pages/404.png'
import miscMaskDark from '@images/pages/misc-mask-dark.png'
import miscMaskLight from '@images/pages/misc-mask-light.png'
import {
    router
} from "@inertiajs/vue3"

const authThemeMask = useGenerateImageVariant(miscMaskLight, miscMaskDark)

const props = defineProps({
    status: Number
})

const code = computed(() => {
    return {
        503: '503',
        500: '500',
        404: '404',
        403: '403',
        401: '401',
    } [props.status]
})

const title = computed(() => {
    return {
        503: 'Service Unavailable ⚠️',
        500: 'Server Error ⚠️',
        404: 'Page Not Found ⚠️',
        403: 'Forbidden ⚠️',
        401: 'You are not authorized! 🔐',
    } [props.status]
})

const description = computed(() => {
    return {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'We couldn\'t find the page you are looking for.',
        403: 'Sorry, you are forbidden from accessing this page.',
        401: 'You don’t have permission to access this page. Go Home!.',
    } [props.status]
})
</script>
<style lang="scss">
@use "@core-scss/template/pages/misc.scss";
</style>
