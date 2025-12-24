<template>
  <Head title="App Data"/>
  <Layout>
    <VCard>
      <VCardText class="d-flex align-center justify-space-between flex-wrap gap-4">
        <div class="d-flex gap-2 align-center">
          <p class="text-body-1 mb-0">Show</p>
          <AppSelect
            :model-value="itemsPerPage"
            :items="[
              { value: 5, title: '5' },
              { value: 25, title: '25' },
              { value: 50, title: '50' },
              { value: 100, title: '100' },
              { value: -1, title: 'All' }
            ]"
            style="inline-size: 5.5rem"
            @update:model-value="itemsPerPage = parseInt($event, 10)"
          />
        </div>

        <div class="d-flex align-center gap-4 flex-wrap">
          <AppTextField
            v-model="search"
            placeholder="Search Notification"
            style="inline-size: 15.625rem"
          />
          <Link href="/app-data/create">
            <VBtn density="default" prepend-icon="tabler-plus">
              Add Data
            </VBtn>
          </Link>
        </div>
      </VCardText>

      <VDivider/>
 
      <!-- Data Table -->
      <VDataTableServer
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :items-length="appData.total"
        :items-per-page-options="[
          { value: 5, title: '5' },
          { value: 10, title: '10' },
          { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' }
        ]"
        :headers="headers"
        :items="appData.data"
        item-value="id"
        class="text-no-wrap"
        @update:options="updateOptions"
      >
        <template #item.value="{ item }">
          <template v-if="item.type == 'Text'">{{ item.value }}</template>
          <template v-else-if="item.type == 'Image'">
            <VImg class="d-inline-block mx-1 my-1" :src="`/storage/${item.value}`" :height="120" :width="120" :rounded="true" :cover="true"></VImg>
          </template>
          <template v-else-if="item.type == 'Repeater Text'">
            <ul>
              <li v-for="(c, index) in JSON.parse(item.value)" :key="index">
                {{ c }}
              </li>
            </ul>
          </template>
          <template v-else-if="item.type == 'Repeater Image'">
            <template v-for="(path, index) in JSON.parse(item.value)" :key="index">
              <VImg class="d-inline-block mx-1 my-1" :src="`/storage/${path}`" :height="120" :width="120" :rounded="true" :cover="true"></VImg>
            </template>
          </template>
        </template>

        <template #item.actions="{ item }">
          <VBtn icon size="small" color="medium-emphasis" variant="text">
            <Link :href="`/app-data/${item.id}/edit`">
              <VIcon size="22" icon="tabler-edit"/>
            </Link>
          </VBtn>
          <VBtn icon size="small" color="medium-emphasis" variant="text" @click="destroy(item.id)">
            <VIcon size="22" icon="tabler-trash"/>
          </VBtn>
        </template>

        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="itemsPerPage"
            :total-items="appData.total"
          />
        </template>
      </VDataTableServer>
    </VCard>
  </Layout>
</template>

<script setup>
import {ref, watch} from "vue";
import {Head, Link, router} from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import Layout from "../../layouts/blank.vue";
import TablePagination from "@core/components/TablePagination.vue";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";
import AppSelect from "@core/components/app-form-elements/AppSelect.vue";
import Swal from "sweetalert2";

const props = defineProps({
  appData: Object,
  filters: Object
});

const headers = [
  {title: "Key", key: "key", sortable: false},
  {title: "Type", key: "type", sortable: false},
  {title: "Value", key: "value", sortable: false},
  {title: "Actions", key: "actions", sortable: false}
];

const search = ref(props.filters.search || "");
const page = ref(props.appData.current_page);
const itemsPerPage = ref(props.appData.per_page);

const fetchData = () => {
  router.get(
    "/app-data",
    {
      search: search.value,
      page: page.value,
      paginate: itemsPerPage.value
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true
    }
  );
};

watch(search, debounce(fetchData, 300));
watch([page, itemsPerPage], fetchData);

const updateOptions = options => {
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
      router.delete(`/app-data/${id}`, {
        preserveScroll: true,
      });
    }
  });
};
</script>
