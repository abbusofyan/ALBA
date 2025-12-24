<template>
  <Layout>
    <div>
      <SelectCountryPhone
        @country-selected="onCountrySelected"
        @phone-changed="onPhoneChanged"
      />
      <div class="display-info">
        <p><strong>Selected Country:</strong> {{ selectedCountry?.name || "None" }}</p>
        <p><strong>Phone Number:</strong> {{ phoneNumber || "None" }}</p>
        <p><strong>Full Phone Number:</strong> {{ fullPhoneNumber || "None" }}</p>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from "vue";
import SelectCountryPhone from "@/Components/SelectCountryPhone.vue";
import Layout from "@/layouts/blank.vue";

const selectedCountry = ref(null);
const phoneNumber = ref("");
const fullPhoneNumber = ref("");

const onCountrySelected = (country) => {
  selectedCountry.value = country;
};

const onPhoneChanged = (data) => {
  phoneNumber.value = data.phoneNumber;
  fullPhoneNumber.value = data.fullPhoneNumber;
};
</script>

<style scoped>
.display-info {
  margin-block-start: 20px;
}
</style>
