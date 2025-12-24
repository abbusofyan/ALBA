<template>
  <Head title="Menu" />
  <Layout>
    <VCard>
      <VCardText
        class="d-flex align-center justify-space-between flex-wrap gap-4"
      >
        <div class="d-flex gap-2 align-center">
          <p class="text-body-1 mb-0">Show</p>
          <AppSelect
            :model-value="itemsPerPage"
            :items="[
              { value: 5, title: '5' },
              { value: 25, title: '25' },
              { value: 50, title: '50' },
              { value: 100, title: '100' },
              { value: -1, title: 'All' },
            ]"
            style="inline-size: 5.5rem"
            @update:model-value="itemsPerPage = parseInt($event, 10)"
          />
        </div>

        <div class="d-flex align-center gap-4 flex-wrap">
          <AppTextField
            v-model="search"
            placeholder="Search Menu"
            style="inline-size: 15.625rem"
          />
          <Link :href="route('menu.create')">
            <VBtn density="default" prepend-icon="tabler-plus"> Add Menu </VBtn>
          </Link>
        </div>
      </VCardText>

      <VDivider />

      <VDataTableServer
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :items-length="menuData.total"
        :items-per-page-options="[
          { value: 5, title: '5' },
          { value: 10, title: '10' },
          { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' },
        ]"
        :headers="headers"
        :items="menuData.data"
        item-value="id"
        class="text-no-wrap"
        @update:options="updateOptions"
      >
        <template #item.parent="{ item }">
          <div class="text-high-emphasis text-body-1">
            {{ item.parent ? item.parent.name : "No Parent" }}
          </div>
        </template>
        <template #item.actions="{ item }">
          <Link :href="route('menu.edit', item.id)">
            <VBtn icon size="small" color="medium-emphasis" variant="text">
              <VIcon size="22" icon="tabler-edit" />
            </VBtn>
          </Link>
          <VBtn
            icon
            size="small"
            color="medium-emphasis"
            variant="text"
            @click="destroy(item.id)"
          >
            <VIcon size="22" icon="tabler-trash" />
          </VBtn>
        </template>

        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="itemsPerPage"
            :total-items="menuData.total"
          />
        </template>
      </VDataTableServer>
    </VCard>
  </Layout>
</template>

<script setup>
import { ref, watch } from "vue";
import { Link, router, Head } from "@inertiajs/vue3";
import { toast } from "vue3-toastify";
import axios from "axios";
import Layout from "../../layouts/blank.vue";
import { debounce } from "lodash";
import Swal from "sweetalert2";

const props = defineProps({
  menuData: Object,
  filters: Object,
});

const headers = [
  { title: "NAME", key: "name", sortable: false },
  { title: "Link", key: "link", sortable: false },
  { title: "Order", key: "order", sortable: false },
  { title: "Parent", key: "parent", sortable: false },
  { title: "Is Heading", key: "is_heading", sortable: false },
  { title: "Actions", key: "actions", sortable: false },
];

const search = ref(props.filters.search || "");
const page = ref(props.menuData.current_page);
const itemsPerPage = ref(props.menuData.per_page);

const fetchData = () => {
  router.get(
    route("menu.index"),
    {
      search: search.value,
      page: page.value,
      paginate: itemsPerPage.value,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    }
  );
};

watch(search, debounce(fetchData, 300));
watch([page, itemsPerPage], fetchData);

const updateOptions = (options) => {
  page.value = options.page;
  itemsPerPage.value = options.itemsPerPage;
};

const destroy = (id) => {
  Swal.fire({
    title: "Are you sure? <br> <i class='fa-solid fa-trash-can'></i>",
    text: "You won't be able to revert this",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#ea5455",
    cancelButtonColor: "#6CC9CF",
    confirmButtonText: "Yes, Proceed!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route("menu.destroy", { id }), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            title: "Deleted!",
            text: "The menu item has been deleted successfully.",
            icon: "success",
            confirmButtonColor: "#34c38f",
          });
        },
        onError: (errors) => {
          Swal.fire({
            title: "Error!",
            text:
              errors.message ||
              "An error occurred while deleting the menu item.",
            icon: "error",
            confirmButtonColor: "#ea5455",
          });
        },
      });
    }
  });
};
</script>
