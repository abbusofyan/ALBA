<script setup>
import { router } from "@inertiajs/vue3";

const props = defineProps({
  user: Object
})

const edit = async () => {
  router.visit(route('user.profile-edit'));
};
</script>

<template>
  <VCard class="mb-6 text-black">
    <VCardText class="d-flex justify-space-between align-center flex-wrap gap-4">
      <h5 class="text-h5">
        User Information
      </h5>
      <VBtn @click="edit()">
        Edit
        <VIcon end icon="tabler-edit" />
      </VBtn>
    </VCardText>
    <VDivider class="mb-5" />
    <VCardText>
      <p class="text-sm text-disabled">
        ABOUT ME
      </p>
      <VList class="card-list">
        <span class="d-flex align-center mb-2">
          <div class="text-body-1 font-weight-medium me-2">Username:</div>
          <div>{{ props.user.username }}</div>
        </span>
        <span class="d-flex align-center mb-2">
          <div class="text-body-1 font-weight-medium me-2">Full Name:</div>
          <div>{{ props.user.name }}</div>
        </span>
        <span class="d-flex align-center mb-2">
          <div class="text-body-1 font-weight-medium me-2">Email:</div>
          <div>{{ props.user.email }}</div>
        </span>
        <span class="d-flex align-center">
          <div class="text-body-1 font-weight-medium me-2">Role:</div>
          <div>{{ props.user.role }}</div>
        </span>
      </VList>

      <p class="text-sm text-disabled mt-6">
        CONTACTS
      </p>

      <VList class="card-list text-medium-emphasis">
        <span class="d-flex align-center mb-2">
          <div class="text-body-1 font-weight-medium me-2">Phone:</div>
          <div>{{ props.user.phone }}</div>
        </span>
        <span class="d-flex align-center mb-2">
          <div class="text-body-1 font-weight-medium me-2">Address:</div>
          <div>{{ props.user.address }}</div>
        </span>
        <span class="d-flex align-center mb-2">
          <div class="text-body-1 font-weight-medium me-2">ZipCode:</div>
          <div>{{ props.user.zipcode }}</div>
        </span>
      </VList>
    </VCardText>
  </VCard>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 16px;
}
</style>
