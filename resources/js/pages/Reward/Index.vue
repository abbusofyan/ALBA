<template>
  <Head title="Rewards" />
  <Layout>
    <v-breadcrumbs :items="breadcrumbs">
      <template v-slot:prepend></template>
    </v-breadcrumbs>
    <section>
      <VCard class="mb-6" title="Search Filter">
        <v-row class="flex-wrap" no-gutters>
          <v-col cols="12" md="2">
            <v-sheet class="ma-2 pa-2">
              <AppSelect
                :model-value="itemsPerPage"
                :items="[
                  { value: 10, title: '10' },
                  { value: 25, title: '25' },
                  { value: 50, title: '50' },
                  { value: 100, title: '100' },
                  { value: -1, title: 'All' },
                ]"
                @update:model-value="itemsPerPage = parseInt($event, 10)"
              />
            </v-sheet>
          </v-col>

          <v-col cols="12" md="10">
            <v-row class="flex-wrap" no-gutters>
              <v-col cols="12" md="9">
                <v-sheet class="ma-2 pa-2">
                  <AppTextField
                    v-model="search"
                    placeholder="Search Reward"
                    class="w-100"
                  />
                </v-sheet>
              </v-col>
              <v-col cols="12" md="3">
                <v-sheet class="ma-2 pa-2">
                  <div class="d-flex align-center flex-wrap justify-end">
                    <v-btn
                      v-if="$filters.can('create-reward')"
                      @click="addReward"
                      color="primary"
                      class="mb-2"
                    >
                      <v-icon end icon="tabler-plus" class="me-2" />
                      Add New Reward
                    </v-btn>
                  </div>
                </v-sheet>
              </v-col>
            </v-row>
          </v-col>
        </v-row>

        <VDataTableServer
          v-model:items-per-page="itemsPerPage"
          v-model:page="page"
          :items-length="rewards.total"
          :items-per-page-options="[
            { value: 5, title: '5' },
            { value: 10, title: '10' },
            { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' },
          ]"
          v-model:sort-by="sortBy"
          :headers="headers"
          :items="rewards.data"
          item-value="id"
          class="text-no-wrap"
        >
          <template #item.code="{ item }">
            <div class="d-flex align-center gap-x-4">
              <div class="d-flex flex-column">
                <h6 class="text-base">
                  <a
                    :href="`#`"
                    @click.prevent="detailReward(item.id)"
                    class="font-weight-medium text-link"
                  >
                    {{ item.code }}
                  </a>
                </h6>
              </div>
            </div>
          </template>

          <template #item.image="{ item }">
            <div class="d-flex align-center gap-x-4">
              <div class="d-flex flex-column">
                <img
                  height="85"
                  :src="`/storage/images/reward/${item.image}`"
                  alt=""
                  class="cursor-pointer"
                  @click="showImage(`/storage/images/reward/${item.image}`)"
                />
              </div>
            </div>

            <v-dialog v-model="dialog" max-width="600px">
              <v-card>
                <img
                  :src="selectedImage"
                  alt=""
                  style="max-width: 100%; height: auto"
                />
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn text @click="dialog = false">Close</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </template>

          <template #item.status="{ item }">
            <VChip
              :color="$filters.resolveStatusVariant(item.status)"
              size="small"
              label
              class="text-capitalize"
            >
              {{ item.status_text }}
            </VChip>
          </template>

          <template #item.actions="{ item }">
            <VBtn icon variant="text" color="medium-emphasis">
              <VIcon icon="tabler-dots-vertical" />
              <VMenu activator="parent">
                <VList>
                  <VListItem @click="detailReward(item.id)">
                    <template #prepend>
                      <VIcon icon="tabler-eye" />
                    </template>

                    <VListItemTitle>View</VListItemTitle>
                  </VListItem>

                  <VListItem
                    v-if="$filters.can('update-reward')"
                    @click="editReward(item.id)"
                  >
                    <template #prepend>
                      <VIcon icon="tabler-pencil" />
                    </template>
                    <VListItemTitle>Edit</VListItemTitle>
                  </VListItem>

                  <VListItem
                    v-if="$filters.can('update-reward')"
                    @click="toggleStatusReward(item.id, item.status)"
                    variant="text"
                  >
                    <template #prepend>
                      <VIcon icon="tabler-trash" />
                    </template>
                    <VListItemTitle>{{
                      item.status ? "Deactivate" : "Activate"
                    }}</VListItemTitle>
                  </VListItem>
                </VList>
              </VMenu>
            </VBtn>
          </template>

          <template #bottom>
            <TablePagination
              v-model:page="page"
              :items-per-page="itemsPerPage"
              :total-items="rewards.total"
            />
          </template>
        </VDataTableServer>
      </VCard>
    </section>
  </Layout>
</template>
<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import { debounce } from "lodash";
import Swal from "sweetalert2";
import { ref } from "vue";
import { toast } from "vue3-toastify";
import Layout from "../../layouts/blank.vue";

const props = defineProps({
  rewards: Object,
  filters: Object,
  roles: Object,
  qty_reward: Number,
});

const breadcrumbs = [
  {
    title: "Rewards",
    disabled: false,
    href: "/rewards",
  },
  {
    title: "Rewards List",
    disabled: true,
  },
];

const headers = [
  {
    title: "ID",
    key: "code",
  },
  {
    title: "ASSET",
    key: "image",
  },
  {
    title: "Name",
    key: "name",
  },
  {
    title: "CO2 Points",
    key: "price",
  },
  {
    title: "Quota",
    key: "remaining_vouchers",
  },
  {
    title: "Usage",
    key: "usage",
  },
  {
    title: "Status",
    key: "status",
  },
  {
    title: "Actions",
    key: "actions",
    sortable: false,
  },
];

const search = ref(props.filters.search || "");
const page = ref(props.rewards.current_page);
const itemsPerPage = ref(props.rewards.per_page);
const sortBy = ref([]);
const filterStatus = ref(props.rewards.status);

const fetchData = async () => {
  router.get(
    route("rewards.index"),
    {
      search: search.value,
      page: page.value,
      paginate: itemsPerPage.value,
      sort_by: sortBy.value[0]?.key,
      sort_order: sortBy.value[0]?.order,
      status: filterStatus.value,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    }
  );
};

const showImage = (src) => {
  selectedImage.value = src;
  dialog.value = true;
};

const dialog = ref(false);
const selectedImage = ref("");

onMounted(() => {
  const flash = usePage().props.flash;

  if (flash.success) {
    toast.success(flash.success, {
      theme: "colored",
      type: "success",
    });
  } else if (flash.error) {
    toast.error(flash.error, {
      theme: "colored",
      type: "error",
    });
  }
});

watch(search, debounce(fetchData, 300));
watch([page, itemsPerPage, sortBy, filterStatus], fetchData);

const updateOptions = (options) => {
  page.value = options.page;
  itemsPerPage.value = options.itemsPerPage;
};

const toggleStatusReward = async (id, isActive) => {
  let text = "This reward will be activated";
  if (isActive) {
    text =
      "This reward will be inactivated.";
  }

  const result = await Swal.fire({
    title: "Are you sure? <br> <i class='fa-solid fa-trash-can'></i>",
    text: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#ea5455",
    cancelButtonColor: "#6CC9CF",
    confirmButtonText: "Yes, Proceed!",
    cancelButtonText: "Cancel",
  });

  if (result.isConfirmed) {
    try {
      const response = await axios.post(`/rewards/${id}/toggleStatus`);
      if (response.data.success) {
        toast.success(response.data.message, {
          theme: "colored",
          type: "success",
        });
        fetchData();
      } else {
        toast.error(response.data.message, {
          theme: "colored",
          type: "error",
        });
      }
    } catch (error) {
      toast.error("An error occurred.", {
        theme: "colored",
        type: "error",
      });
    }
  }
};

const detailReward = async (id) => {
  router.visit(route("rewards.show", id));
};

const editReward = async (id) => {
  router.visit(route("rewards.edit", id));
};

const addReward = async (id) => {
  router.visit(route("rewards.create"));
};
</script>
