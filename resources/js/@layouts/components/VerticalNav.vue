<script setup>
import { can as canPermission } from "@/utils/permissions";
import { router, usePage } from "@inertiajs/vue3";
import { layoutConfig } from "@layouts";
import {
  VerticalNavGroup,
  VerticalNavLink,
  VerticalNavSectionTitle,
} from "@layouts/components";
import { useLayoutConfigStore } from "@layouts/stores/config";
import { injectionKeyIsVerticalNavHovered } from "@layouts/symbols";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import { VNodeRenderer } from "./VNodeRenderer";

const page = usePage();
const permissions = computed(() => page.props.auth.user.permissions);

function can(permission) {
  return canPermission(permission, permissions.value);
}

const menuItems = [
  {
    name: "Dashboard",
    link: "dashboard",
    icon: {
      icon: "tabler-smart-home",
    },
    children: [
      can("view roles") || can("view permissions")
        ? {
            name: "Role & Permissions",
            children: [
              can("view roles") && {
                name: "Roles",
                link: "role",
              },
              can("view permissions") && {
                name: "Permissions",
                link: "permission",
              },
            ].filter(Boolean),
          }
        : null,
      can("view menu") && {
        name: "Menus",
        link: "menu",
      },
      can("view app-data") && {
        name: "App Data",
        link: "app-data",
      },
    ].filter(Boolean),
  },
  can('view-setting') && {
    name: "Settings",
    link: "settings",
    icon: {
      icon: "tabler-settings",
    },
  },
  can('view-setting') && {
    name: "Notification",
    link: "push-notifications",
    icon: {
      icon: "tabler-bell",
    },
  },
  can("view-user") ||
  can("view-staff") ||
  can("view-school") ||
  can("view-enterprise")
    ? {
        name: "User Management",
        icon: {
          icon: "tabler-users",
        },
        children: [
          can("view-staff") && {
            name: "Staff",
            link: "staffs",
          },
          can("view-user") && {
            name: "User",
            link: "users",
          },
          can("view-school") && {
            name: "School",
            link: "schools",
          },
          can("view-enterprise") && {
            name: "Enterprise",
            link: "enterprises",
          },
        ].filter(Boolean),
      }
    : null,
  (can("view-bin") || can("view-bin-type") || can("view-recycling")) && {
    heading: "BIN & RECYCLING MANAGEMENT",
  },
  can("view-bin-type") && {
    name: "Bin Type",
    link: "bin-types",
    icon: {
      icon: "tabler-smart-home",
    },
  },
  can("view-bin") && {
    name: "Bin",
    icon: {
      icon: "tabler-pentagon",
    },
    children: [
      {
        name: "Bin List",
        link: "bins",
      },
      {
        name: "Download Template",
        link: "download-template",
      },
    ],
  },
  can("view-recycling") && {
    name: "Recycling",
    icon: {
      icon: "tabler-recycle",
    },
    children: [
      {
        name: "Recycling Activity",
        link: "recyclings",
      },
    ],
  },
  can("view-event") && {
    heading: "EVENTS",
  },
  can("view-event") && {
    name: "Event",
    icon: {
      icon: "tabler-calendar",
    },
    children: [
      {
        name: "Event List",
        link: "events",
      },
	  {
        name: "Download Template",
        link: "events/download-import-template-view",
      },
    ],
  },
  (can("view-reward") || can("view-banner")) &&{
    heading: "REWARDS AND VOUCHERS",
  },
  can("view-reward") && {
    name: "Rewards",
    icon: {
      icon: "tabler-star",
    },
    children: [
      {
        name: "Reward List",
        link: "rewards",
      },
    ],
  },
  can("view-banner") && {
    name: "Banner",
    icon: {
      icon: "tabler-star",
    },
    children: [
      {
        name: "Banner List",
        link: "banners",
      },
    ],
  },
].filter(Boolean);

const props = defineProps({
  tag: {
    type: null,
    required: false,
    default: "aside",
  },
  navItems: {
    type: null,
    required: true,
  },
  isOverlayNavActive: {
    type: Boolean,
    required: true,
  },
  toggleIsOverlayNavActive: {
    type: Function,
    required: true,
  },
});

const refNav = ref();
const isHovered = useElementHover(refNav);

provide(injectionKeyIsVerticalNavHovered, isHovered);

const configStore = useLayoutConfigStore();

const resolveNavItemComponent = (item) => {
  if ("heading" in item) return VerticalNavSectionTitle;
  if (
    "children" in item &&
    Array.isArray(item.children) &&
    item.children.length > 0
  ) {
    return VerticalNavGroup;
  }

  return VerticalNavLink;
};

/*ℹ️ Close overlay side when route is changed
Close overlay vertical nav when link is clicked
*/

watch(() => {
  props.toggleIsOverlayNavActive(false);
});

const isVerticalNavScrolled = ref(false);
const updateIsVerticalNavScrolled = (val) =>
  (isVerticalNavScrolled.value = val);

const handleNavScroll = (evt) => {
  isVerticalNavScrolled.value = evt.target.scrollTop > 0;
};

const hideTitleAndIcon = configStore.isVerticalNavMini(isHovered);
</script>

<template>
  <Component
    :is="props.tag"
    ref="refNav"
    data-allow-mismatch
    class="layout-vertical-nav"
    :class="[
      {
        'overlay-nav': configStore.isLessThanOverlayNavBreakpoint,
        hovered: isHovered,
        visible: isOverlayNavActive,
        scrolled: isVerticalNavScrolled,
      },
    ]"
  >
    <!-- 👉 Header -->
    <div class="nav-header">
      <slot name="nav-header">
        <div
          @click="router.get(route('home'))"
          type="button"
          class="app-logo app-title-wrapper"
        >
          <VNodeRenderer :nodes="layoutConfig.app.logo" />

          <Transition name="vertical-nav-app-title">
            <h1 v-show="!hideTitleAndIcon" class="app-logo-title">
              {{ layoutConfig.app.title }}
            </h1>
          </Transition>
        </div>
        <!-- 👉 Vertical nav actions -->
        <!-- Show toggle collapsible in >md and close button in <md -->
        <div class="header-action">
          <Component
            :is="layoutConfig.app.iconRenderer || 'div'"
            v-show="configStore.isVerticalNavCollapsed"
            class="d-none nav-unpin"
            :class="configStore.isVerticalNavCollapsed && 'd-lg-block'"
            v-bind="layoutConfig.icons.verticalNavUnPinned"
            @click="
              configStore.isVerticalNavCollapsed =
                !configStore.isVerticalNavCollapsed
            "
          />
          <Component
            :is="layoutConfig.app.iconRenderer || 'div'"
            v-show="!configStore.isVerticalNavCollapsed"
            class="d-none nav-pin"
            :class="!configStore.isVerticalNavCollapsed && 'd-lg-block'"
            v-bind="layoutConfig.icons.verticalNavPinned"
            @click="
              configStore.isVerticalNavCollapsed =
                !configStore.isVerticalNavCollapsed
            "
          />
          <Component
            :is="layoutConfig.app.iconRenderer || 'div'"
            class="d-lg-none"
            v-bind="layoutConfig.icons.close"
            @click="toggleIsOverlayNavActive(false)"
          />
        </div>
      </slot>
    </div>
    <slot name="before-nav-items">
      <div class="vertical-nav-items-shadow" />
    </slot>
    <slot
      name="nav-items"
      :update-is-vertical-nav-scrolled="updateIsVerticalNavScrolled"
    >
      <PerfectScrollbar
        :key="configStore.isAppRTL"
        tag="ul"
        class="nav-items"
        :options="{ wheelPropagation: false }"
        @ps-scroll-y="handleNavScroll"
      >
        <Component
          :is="resolveNavItemComponent(item)"
          v-for="(item, index) in navItems"
          :key="index"
          :item="item"
        />
        <Component
          :is="resolveNavItemComponent(item)"
          v-for="(item, index) in menuItems"
          :key="index"
          :item="item"
        />
      </PerfectScrollbar>
    </slot>
    <slot name="after-nav-items" />
  </Component>
</template>

<style lang="scss" scoped>
.app-logo {
  display: flex;
  align-items: center;
  column-gap: 0.75rem;

  .app-logo-title {
    font-size: 1.375rem;
    font-weight: 700;
    letter-spacing: 0.25px;
    line-height: 1.5rem;
    text-transform: capitalize;
  }
}
</style>

<style lang="scss">
@use "@configured-variables" as variables;
@use "@layouts/styles/mixins";

// 👉 Vertical Nav
.layout-vertical-nav {
  position: fixed;
  z-index: variables.$layout-vertical-nav-z-index;
  display: flex;
  flex-direction: column;
  block-size: 100%;
  inline-size: variables.$layout-vertical-nav-width;
  inset-block-start: 0;
  inset-inline-start: 0;
  transition: inline-size 0.25s ease-in-out, box-shadow 0.25s ease-in-out;
  will-change: transform, inline-size;

  .nav-header {
    display: flex;
    align-items: center;

    .header-action {
      cursor: pointer;

      @at-root {
        #{variables.$selector-vertical-nav-mini} .nav-header .header-action {
          &.nav-pin,
          &.nav-unpin {
            display: none !important;
          }
        }
      }
    }
  }

  .app-title-wrapper {
    margin-inline-end: auto;
  }

  .nav-items {
    block-size: 100%;

    // ℹ️ We no loner needs this overflow styles as perfect scrollbar applies it
    // overflow-x: hidden;Wenolonerneedsthisoverflowstylesasperfectscrollbarappliesitoverflow-x

    // // ℹ️ We used `overflow-y` instead of `overflow` to mitigate overflow x. Revert back if any issue found.
    // overflow-y: auto;Weused`overflow-y`insteadof`overflow`tomitigateoverflowx.Revertbackifanyissuefound.overflow-y
  }

  .nav-item-title {
    overflow: hidden;
    margin-inline-end: auto;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  // 👉 Collapsed
  .layout-vertical-nav-collapsed & {
    &:not(.hovered) {
      inline-size: variables.$layout-vertical-nav-collapsed-width;
    }
  }
}

// Small screen vertical nav transition
@media (max-width: 1279px) {
  .layout-vertical-nav {
    &:not(.visible) {
      transform: translateX(-#{variables.$layout-vertical-nav-width});

      @include mixins.rtl {
        transform: translateX(variables.$layout-vertical-nav-width);
      }
    }

    transition: transform 0.25s ease-in-out;
  }
}
</style>
