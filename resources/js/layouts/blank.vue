<script setup>
import DefaultLayoutWithVerticalNav from "@/layouts/components/DefaultLayoutWithVerticalNav.vue";
import { ref, onMounted, computed, onBeforeUpdate, onUpdated } from "vue";
import { usePage } from "@inertiajs/vue3";
import { toast } from "vue3-toastify";

const page = usePage();
import AppLoadingIndicator from "@/Components/AppLoadingIndicator.vue";

const { injectSkinClasses } = useSkins();

const created = computed(() => page.props.flash.created);
const updated = computed(() => page.props.flash.updated);
const deleted = computed(() => page.props.flash.deleted);
const signed = computed(() => page.props.flash.signed);
const canceled = computed(() => page.props.flash.canceled);
const registered = computed(() => page.props.flash.registered);
const error = computed(() => page.props.flash.error);

onMounted(() => {
  if (created.value) {
    toast.success(created.value, {
      theme: "colored",
      type: "success",
    });
  }

  if (updated.value) {
    toast.info(updated.value, {
      theme: "colored",
      type: "info",
    });
  }
});

onBeforeUpdate(() => {
  if (updated.value) {
    toast.info(updated.value, {
      theme: "colored",
      type: "info",
    });

    page.props.flash.updated = null;
  }

  if (signed.value) {
    toast.success(signed.value, {
      theme: "colored",
      type: "success",
    });

    page.props.flash.signed = null;
  }

  if (canceled.value) {
    toast.error(canceled.value, {
      theme: "colored",
      type: "error",
    });

    page.props.flash.canceled = null;
  }

  if (deleted.value) {
    toast.error(deleted.value, {
      theme: "colored",
      type: "error",
    });

    page.props.flash.deleted = null;
  }

  if (error.value) {
    toast.error(error.value, {
      theme: "colored",
      type: "error",
    });

    page.props.flash.deleted = null;
  }
});
// ℹ️ This will inject classes in body tag for accurate styling
injectSkinClasses();

// SECTION: Loading Indicator
const isFallbackStateActive = ref(false);
const refLoadingIndicator = ref(null);

watch(
  [isFallbackStateActive, refLoadingIndicator],
  () => {
    if (isFallbackStateActive.value && refLoadingIndicator.value)
      refLoadingIndicator.value.fallbackHandle();
    if (!isFallbackStateActive.value && refLoadingIndicator.value)
      refLoadingIndicator.value.resolveHandle();
  },
  { immediate: true }
);

const items = [
    {
      title: 'Users',
      disabled: false,
      href: 'breadcrumbs_dashboard',
    },
    {
      title: 'User List',
      disabled: true,
      href: 'breadcrumbs_link_1',
    }
  ]
// !SECTION
</script>

<template>
  <AppLoadingIndicator ref="refLoadingIndicator" />
  <DefaultLayoutWithVerticalNav>
    <slot />
  </DefaultLayoutWithVerticalNav>
  <div class="layout-wrapper layout-blank" data-allow-mismatch></div>
</template>

<style>
.layout-wrapper.layout-blank {
  flex-direction: column;
}
</style>
