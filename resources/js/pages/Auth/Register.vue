<script setup>
import {
    VForm
} from 'vuetify/components/VForm'
import AuthProvider from '@/views/pages/authentication/AuthProvider.vue'
import {
    VNodeRenderer
} from '@layouts/components/VNodeRenderer'
import {
    themeConfig
} from '@themeConfig'
import authV2RegisterIllustrationBorderedDark from '@images/pages/auth-v2-register-illustration-bordered-dark.png'
import authV2RegisterIllustrationBorderedLight from '@images/pages/auth-v2-register-illustration-bordered-light.png'
import authV2RegisterIllustrationDark from '@images/pages/auth-v2-register-illustration-dark.png'
import authV2RegisterIllustrationLight from '@images/pages/auth-v2-register-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'
import {
    useForm,
    Link
} from "@inertiajs/vue3";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";

const imageVariant = useGenerateImageVariant(authV2RegisterIllustrationLight, authV2RegisterIllustrationDark, authV2RegisterIllustrationBorderedLight, authV2RegisterIllustrationBorderedDark, true)
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)

definePage({
    meta: {
        layout: 'blank',
        unauthenticatedOnly: true,
    },
})

const form = useForm({
    username: '',
    email: '',
    name: '',
    password: '',
    password_confirmation: '',
    privacyPolicies: false,
});

const isPasswordVisible = ref(false)
</script>

<style scoped>
.auth-background {
  background: url('/public/images/logo/background-login.png') no-repeat center center;
  background-size: cover;
}
</style>

<template>
<Head title="Register" />

<VRow no-gutters class="auth-wrapper bg-surface">

    <VCol cols="12" md="12" class="auth-background auth-card-v2 d-flex align-center justify-center" style="background-color: rgb(var(--v-theme-surface));">
        <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-4">
			<VCardText>
                <div class="d-flex justify-center mb-6">
					<img src="/images/logo/logo-long.png" alt="" style="width:166px; height:56px;">

                </div>
                <h4 class="text-h4 mb-1">
                    Adventure Starts Here
                </h4>
                <p class="mb-0">
                    Make your app management easy and fun!
                </p>
            </VCardText>

            <VCardText>
                <VForm @submit.prevent="form.post('/register')">
                    <VRow>
                        <!-- Username -->
                        <VCol cols="12">
                            <AppTextField v-model="form.username" :error-messages="form.errors.username" :rules="[requiredValidator]" autofocus label="Username" type="text" placeholder="Johndoe" />
                        </VCol>

                        <!-- email -->
                        <VCol cols="12">
                            <AppTextField v-model="form.email" :error-messages="form.errors.email" :rules="[requiredValidator, emailValidator]" label="Email" type="email" placeholder="johndoe@email.com" />
                        </VCol>

                        <!-- email -->
                        <VCol cols="12">
                            <AppTextField v-model="form.name" :error-messages="form.errors.name" :rules="[requiredValidator]" label="Name" type="text" placeholder="John Doe" />
                        </VCol>

                        <!-- password -->
                        <VCol cols="12">
                            <AppTextField v-model="form.password" :error-messages="form.errors.password" :rules="[requiredValidator]" label="Password" placeholder="············" :type="isPasswordVisible ? 'text' : 'password'"
                                :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'" @click:append-inner="isPasswordVisible = !isPasswordVisible" />
                        </VCol>

                        <!-- password -->
                        <VCol cols="12">
                            <AppTextField v-model="form.password_confirmation" :error-messages="form.errors.password_confirmation" :rules="[requiredValidator]" label="Confirm password" placeholder="············"
                                :type="isPasswordVisible ? 'text' : 'password'" :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'" @click:append-inner="isPasswordVisible = !isPasswordVisible" />
                        </VCol>

                        <div class="d-flex align-center mt-2 mb-4">
                            <VCheckbox id="privacy-policy" v-model="form.privacyPolicies" :error-messages="form.errors.privacyPolicies" inline />
                            <VLabel for="privacy-policy" style="opacity: 1;">
                                <span class="me-1">I agree to</span>
                                <a href="javascript:void(0)" class="text-primary">privacy policy & terms</a>
                            </VLabel>
                        </div>

                        <VBtn block type="submit">
                            Sign up
                        </VBtn>

                        <!-- create account -->
                        <VCol cols="12" class="text-center text-base">
                            <span>Already have an account?</span>
                            <Link class="text-primary ms-2" href="/login">
                            Sign in instead
                            </Link>
                        </VCol>
                    </VRow>
                </VForm>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>
