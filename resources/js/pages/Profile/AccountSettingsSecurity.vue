<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { toast } from "vue3-toastify";

const props = defineProps({
  user: Object
})

const isNewPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)

const passwordRequirements = [
  'Minimum 8 characters long - the more, the better',
  'At least one lowercase character',
  'At least one number, symbol, or whitespace character',
]

const form = useForm({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
});

const updatePassword = () => {
  form.put(route('users.password-update', { id: props.user.id }), {
    preserveScroll: true,
    onSuccess: () => {
      const flashMessage = usePage().props.flash;

      if (flashMessage.success) {
        toast.success(flashMessage.success, {
          theme: "colored",
          type: "success",
        });
      }

      if (flashMessage.error) {
        toast.error(flashMessage.error, {
          theme: "colored",
          type: "error",
        });
      }
    },
    onError: (errors) => {
      Object.values(errors).forEach((errorMessages) => {
        toast.error(errorMessages, {
          theme: "colored",
          type: "error",
        });
      });
    },
  });
}
</script>

<template>
  <VRow>
    <!-- SECTION: Change Password -->
    <VCol cols="12">
      <VCard title="Change Password">
        <VForm @submit.prevent="updatePassword" v-model="isFormValid">
          <VCardText class="pt-0">
            <!-- 👉 Current Password -->
            <VRow>
              <VCol cols="12" md="6">
                <!-- 👉 current password -->
                <AppTextField v-model="form.current_password" :type="isCurrentPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isCurrentPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  label="Current Password" autocomplete="on" placeholder="············"
                  @click:append-inner="isCurrentPasswordVisible = !isCurrentPasswordVisible" />
              </VCol>
            </VRow>

            <!-- 👉 New Password -->
            <VRow>
              <VCol cols="12" md="6">
                <!-- 👉 new password -->
                <AppTextField v-model="form.new_password" :type="isNewPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isNewPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'" label="New Password"
                  autocomplete="on" placeholder="············"
                  @click:append-inner="isNewPasswordVisible = !isNewPasswordVisible" />
              </VCol>

              <VCol cols="12" md="6">
                <!-- 👉 confirm password -->
                <AppTextField v-model="form.new_password_confirmation"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  label="Confirm New Password" autocomplete="on" placeholder="············"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible" />
              </VCol>
            </VRow>
          </VCardText>

          <!-- 👉 Password Requirements -->
          <VCardText>
            <h6 class="text-h6 text-medium-emphasis mb-4">
              Password Requirements:
            </h6>

            <VList class="card-list">
              <VListItem v-for="item in passwordRequirements" :key="item" :title="item" class="text-medium-emphasis">
                <template #prepend>
                  <VIcon size="10" icon="tabler-circle-filled" />
                </template>
              </VListItem>
            </VList>
          </VCardText>

          <!-- 👉 Action Buttons -->
          <VCardText class="d-flex flex-wrap gap-4">
            <VBtn type="submit" :loading="form.processing">Save changes</VBtn>

            <VBtn type="reset" color="secondary" variant="tonal">
              Reset
            </VBtn>
          </VCardText>
        </VForm>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 16px;
}

.server-close-btn {
  inset-inline-end: 0.5rem;
}
</style>
