<script setup>
// import {defineProps} from 'vue'
import { useTheme } from "vuetify";
import ScrollToTop from "@core/components/ScrollToTop.vue";
import { Head, usePage } from "@inertiajs/vue3";
import initCore from "@core/initCore";
import { initConfigStore, useConfigStore } from "@core/stores/config";
import { hexToRgb } from "@core/utils/colorConverter";

const props = defineProps({
  inertiaApp: Object,
});

const { global } = useTheme();

initCore();
initConfigStore();

const configStore = useConfigStore();

const page = usePage();
</script>

<template>
  <VLocaleProvider :rtl="configStore.isAppRTL">
    <!-- ℹ️ This is required to set the background color of active nav link based on currently active global theme's primary -->
    <VApp
      :style="`--v-global-theme-primary: ${hexToRgb(
        global.current.value.colors.primary
      )}`"
    >
      <div class="layout-wrapper layout-blank">
        <component :is="inertiaApp" />
        <ScrollToTop />
      </div>
    </VApp>
  </VLocaleProvider>
</template>
