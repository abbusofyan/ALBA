<template>
    <div style="background-image: url('/background.png'); background-repeat: no-repeat; background-size: cover">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <div class="text-center mb-3">
                    <img src="../../../assets/img/branding/jobson-logo.png" height="120"/>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="form.post('/verify-code')" class="mb-3">
                            <div class="mb-4">
                                <label for="text" class="text-center d-block mb-4">OTP Code is sent to {{ phone }}</label>
                                <div class="row justify-content-center">
                                    <div class="col-md-12 otp-input-wrap">
                                        <VOtpInput
                                            ref="otpInput"
                                            v-model:value="form.verification_code"
                                            input-classes="otp-input"
                                            separator=""
                                            :num-inputs="6"
                                            :should-auto-focus="true"
                                            input-type="letter-numeric"
                                            :placeholder="['_', '_', '_', '_', '_', '_']"
                                        />
                                    </div>
                                </div>
                                <div v-if="form.errors.verification_code" class="invalid-feedback mt-2 d-block text-center">{{ form.errors.verification_code }}</div>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-primary d-grid w-100" type="submit" :disabled="form.processing">Verify Login</button>
                            </div>
                        </form>

                        <div class="text-center mb-2">
                            <a @click="resendCode" href="javascript:void(0);">
                                <span>Resend Code</span>
                            </a>
                        </div>

                        <div class="text-center">
                            <Link href="/logout" method="post">
                                <small>Back to Login</small>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {useForm, Link, usePage} from "@inertiajs/vue3";
import VOtpInput from "vue3-otp-input";
import {computed, onMounted} from "vue";
import {useToast} from "vue-toastification";
import Swal from "sweetalert2";
import {Inertia} from "@inertiajs/inertia";

const toast = useToast();

const props = defineProps({
    phone: String,
    another_device: String,
    send: Boolean,
});

const form = useForm({
    verification_code: '',
});

const created = computed(() => usePage().props.value.flash.created);
const updated = computed(() => usePage().props.value.flash.updated);

const resendCode = async () => {
    await form.post("/resend-login-code");
    if (created.value) {
        toast.success(created.value, {
            icon: "fa-solid fa-check",
            timeout: 3000,
        });
    }

    if (updated.value) {
        toast.warning(updated.value, {
            icon: "fa-solid fa-check",
            timeout: 3000,
        });
    }
};

onMounted(() => {
    if (props.another_device) {
        Swal.fire({
            title: "Warning",
            text: "Your login attempt from a new device has been detected. To proceed, please confirm that you want to sign in on this device and log out from your previous device?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ea5455",
            cancelButtonColor: "#009CDB",
            confirmButtonText: "Yes, Proceed!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                resendCode()
            } else {
                Inertia.visit("/logout", {method: "post"});
            }
        });
    } else {
        if (props.send) {
            resendCode()
        }
    }
})
</script>
