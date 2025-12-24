<script setup>
import AuthProvider from "@/views/pages/authentication/AuthProvider.vue";
import {
    useGenerateImageVariant
} from "@core/composable/useGenerateImageVariant";
import authV2LoginIllustrationBorderedDark from "@images/pages/auth-v2-login-illustration-bordered-dark.png";
import authV2LoginIllustrationBorderedLight from "@images/pages/auth-v2-login-illustration-bordered-light.png";
import authV2LoginIllustrationDark from "@images/pages/auth-v2-login-illustration-dark.png";
import authV2LoginIllustrationLight from "@images/pages/auth-v2-login-illustration-light.png";
import authV2MaskDark from "@images/pages/misc-mask-dark.png";
import authV2MaskLight from "@images/pages/misc-mask-light.png";
import {
    VNodeRenderer
} from "@layouts/components/VNodeRenderer";
import {
    themeConfig
} from "@themeConfig";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";
import {
    useForm,
    Link,
    Head
} from "@inertiajs/vue3";

definePage({
    meta: {
        layout: "blank",
        public: true,
    },
});

const form = useForm({
    login: null,
    password: null,
    remember: false,
});

const isPasswordVisible = ref(false);
const authThemeImg = useGenerateImageVariant(
    authV2LoginIllustrationLight,
    authV2LoginIllustrationDark,
    authV2LoginIllustrationBorderedLight,
    authV2LoginIllustrationBorderedDark,
    true
);
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark);
</script>

<style scoped>
.auth-background {
  background: url('/public/images/logo/background-login.png') no-repeat center center;
  background-size: cover;
}
</style>

<template>
<Head title="Login" />

<VRow no-gutters class="auth-background auth-wrapper bg-surface">
    <VCol cols="12" md="12" class="auth-card-v2 d-flex align-center justify-center">
        <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-6">
            <VCardText>
                <div class="d-flex justify-center mb-6">
					<img src="/images/logo/logo-long.png" alt="" style="width:166px; height:56px;">

                </div>
                <h4 class="text-h4 mb-1">
                    Welcome to
                    <span class="text-capitalize">ALBA Web App</span>
                </h4>
                <p class="mb-0">
                    Please sign-in to your account to proceed
                </p>
            </VCardText>
            <VCardText>
                <VForm @submit.prevent="form.post('/login')">
                    <VRow>
                        <VCol cols="12">
                            <AppTextField id="login" label="Email or Username" v-model="form.login" :error-messages="form.errors.login" placeholder="johndoe@email.com" autofocus />
                        </VCol>

                        <VCol cols="12">
                            <AppTextField id="password" label="Password" v-model="form.password" :error-messages="form.errors.password" :type="isPasswordVisible ? 'text' : 'password'" placeholder="············" autocomplete="password" :append-inner-icon="
                    isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'
                  " @click:append-inner="isPasswordVisible = !isPasswordVisible" />

                            <div class="d-flex align-center flex-wrap justify-space-between my-6">
                                <VCheckbox v-model="form.remember" label="Remember me" />
                                <Link class="text-primary" href="/forgot-password">
                                Forgot Password?
                                </Link>
                            </div>

                            <VBtn id="btn-submit" block type="submit"> Sign in </VBtn>
                        </VCol>

                    </VRow>
                </VForm>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
