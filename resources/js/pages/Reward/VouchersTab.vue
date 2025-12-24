<script setup>
import { debounce } from "lodash";
import { onMounted, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  rewardData: {
    type: Object,
    required: true,
  },
});

const vouchers = ref({
  data: [],
  total: 0,
});
const loading = ref(true);
const downloading = ref(false);
const search = ref("");
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref([]);

const headers = [
  { title: "Date Created", key: "created_at" },
  { title: "Code", key: "code" },
  { title: "Status", key: "status", sortable: false },
  { title: "User", key: "user" },
];

const fetchData = async () => {
  loading.value = true;
  try {
    const response = await axios.get(
      route("rewards.vouchers", props.rewardData.id),
      {
        params: {
          page: page.value,
          paginate: itemsPerPage.value,
          sort_by: sortBy.value[0]?.key,
          sort_order: sortBy.value[0]?.order,
          search: search.value,
        },
      }
    );
    vouchers.value = response.data;
  } catch (error) {
    console.error("Error fetching vouchers:", error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchData);

watch(search, debounce(fetchData, 300));
watch([page, itemsPerPage, sortBy], fetchData, { deep: true });

const detailUser = async (id) => {
  router.visit(route("users.show", id));
};

const downloadVouchers = async () => {
  try {
    downloading.value = true;
    window.location.href = `/rewards/download-vouchers/${props.rewardData.id}`;
  } finally {
    // Reset after a short delay, since we can't detect exact download finish
    setTimeout(() => {
      downloading.value = false;
    }, 2000);
  }
};
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Voucher Assignments :">
        <VCardText>
          <VRow>
            <VCol md="10">
              <AppTextField v-model="search" placeholder="Search Code..." />
            </VCol>
            <VCol md="2">
              <v-btn
                color="info"
                class="me-2 mb-2"
                :loading="downloading"
                @click="downloadVouchers"
              >
                <v-icon end icon="tabler-download" class="me-2" />
                Download
              </v-btn>
            </VCol>
          </VRow>

          <VDataTableServer
            v-model:items-per-page="itemsPerPage"
            v-model:page="page"
            :items-length="vouchers.total"
            :headers="headers"
            :items="vouchers.data"
            :loading="loading"
            class="text-no-wrap"
            v-model:sort-by="sortBy"
            @update:options="fetchData"
          >
            <template #item.created_at="{ item }">
              {{ $filters.formatDate(item.created_at) }}
            </template>
            <template #item.status="{ item }">
              <VChip
                :color="$filters.resolveStatusVariant(item.status_text)"
                size="small"
                label
                class="text-capitalize"
              >
                {{ item.status_text }}
              </VChip>
            </template>
            <template #item.user="{ item }">
              <a
                v-if="item.user"
                href="#"
                @click.prevent="detailUser(item.user.id)"
                class="font-weight-medium text-link"
              >
                {{ item.user?.name }}
              </a>
            </template>
            <template #bottom>
              <TablePagination
                v-model:page="page"
                :items-per-page="itemsPerPage"
                :total-items="vouchers.total"
              />
            </template>
          </VDataTableServer>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>
