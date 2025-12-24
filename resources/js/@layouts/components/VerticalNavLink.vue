<script setup>
import { layoutConfig } from "@layouts";
import { can } from "@layouts/plugins/casl";
import { useLayoutConfigStore } from "@layouts/stores/config";
import {
  // getComputedNavLinkToProp,
  getDynamicI18nProps,
  // isNavLinkActive,
} from "@layouts/utils";

const props = defineProps({
  item: {
    type: null,
    required: true,
  },
});
import { Link, usePage } from "@inertiajs/vue3";
const baseUrl = computed(() => page.props.base_url);
const page = usePage();
const configStore = useLayoutConfigStore();
const hideTitleAndBadge = configStore.isVerticalNavMini();

const getComputedNavLinkToProp = (item) => {
  const { base_url } = usePage().props;
  return {
    href: item.link ? `${base_url}/${item.link}` : "#",
  };
};

const isNavLinkActive = (item) => {
  const currentPage = usePage().url.split("?")[0];
  const formattedPage = currentPage.replace(/^\/+/, "");

  const formattedPageParts = formattedPage.split("/");
  const itemLinkParts = item.link ? item.link.split("/") : [];

  const isFirstPartMatch = formattedPageParts[0] === itemLinkParts[0];
  const isRestMatch =
    itemLinkParts.length === 1 ||
    formattedPageParts.slice(1).join("/") === itemLinkParts.slice(1).join("/");

  return isFirstPartMatch && isRestMatch;
};

const isImage = (icon) => {
  return typeof icon === "string" && /\.(png|jpe?g|svg|gif|webp)$/i.test(icon);
};
</script>

<template>
  <li
    v-if="can(item.action, item.subject)"
    class="nav-link"
    :class="{ disabled: item.disable }"
  >
    <Link
      v-bind="getComputedNavLinkToProp(item)"
      :class="{
        'router-link-active router-link-exact-active': isNavLinkActive(item),
      }"
    >
      <Component
        :is="
          isImage(item.icon) ? 'img' : layoutConfig.app.iconRenderer || 'div'
        "
        :src="isImage(item.icon) ? item.icon : undefined"
        v-bind="
          !isImage(item.icon)
            ? item.icon || layoutConfig.verticalNav.defaultNavItemIconProps
            : {}
        "
        class="nav-item-icon"
      />
      <TransitionGroup name="transition-slide-x">
        <Component
          :is="layoutConfig.app.i18n.enable ? 'i18n-t' : 'span'"
          v-show="!hideTitleAndBadge"
          key="title"
          class="nav-item-title"
          v-bind="getDynamicI18nProps(item.title, 'span')"
        >
          {{ item.name }}
        </Component>
      </TransitionGroup>
    </Link>
  </li>
</template>

<style lang="scss">
.layout-vertical-nav {
  .nav-link a {
    display: flex;
    align-items: center;
  }
}

.layout-nav-type-vertical .layout-vertical-nav .nav-link > .router-link-active.router-link-exact-active {
  background: linear-gradient(270deg, rgba(68, 161, 43, 0.7) 0%, rgb(68, 161, 43) 100%) !important;
}



</style>
