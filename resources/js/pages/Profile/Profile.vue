<script setup>
import { Head } from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import About from '../Profile/About.vue';

const props = defineProps({
  user: Object
})

</script>

<template>

  <Head title="Profile" />
  <Layout>
    <VRow v-if="props.user">
      <VCol cols="12">
        <About :user="props.user" />
      </VCol>
    </VRow>
  </Layout>
</template>
