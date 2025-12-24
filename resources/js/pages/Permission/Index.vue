<template>
  <Head title="Permissions" />
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
            placeholder="Search Permission"
            style="inline-size: 15.625rem"
          />
          <VBtn
            density="default"
            prepend-icon="tabler-plus"
            @click="isAddPermissionDialogVisible = true"
          >
            Add Permission
          </VBtn>
        </div>
      </VCardText>

      <VDivider />

      <VDataTableServer
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :items-length="permissionData.total"
        :items-per-page-options="[
          { value: 5, title: '5' },
          { value: 10, title: '10' },
          { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' },
        ]"
        :headers="headers"
        :items="permissionData.data"
        item-value="id"
        class="text-no-wrap"
        @update:options="updateOptions"
      >
        <template #item.name="{ item }">
          <div class="text-high-emphasis text-body-1">
            {{ item.name }}
          </div>
        </template>

        <template #item.roles="{ item }">
          <div class="d-flex gap-4">
            <VChip
              v-for="text in item.roles"
              :key="text"
              label
              size="small"
              class="font-weight-medium"
            >
              {{ text }}
            </VChip>
          </div>
        </template>

        <template #item.created_at="{ item }">
          <div class="text-body-2 text-medium-emphasis">
            {{ formatDate(item.created_at) }}
          </div>
        </template>

        <template #item.actions="{ item }">
          <VBtn
            icon
            size="small"
            color="medium-emphasis"
            variant="text"
            @click="editPermission(item.name, item.id)"
          >
            <VIcon size="22" icon="tabler-edit" />
          </VBtn>
          <VBtn
            icon
            size="small"
            color="medium-emphasis"
            variant="text"
            @click="deletePermission(item.id)"
          >
            <VIcon size="22" icon="tabler-trash" />
          </VBtn>
        </template>

        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="itemsPerPage"
            :total-items="permissionData.total"
          />
        </template>
      </VDataTableServer>
    </VCard>
    <AddEditPermissionDialog
      v-model:is-dialog-visible="isPermissionDialogVisible"
      v-model:permission-name="permissionName"
      :permission-id="selectedPermissionId"
      @permissionUpdated="handlePermissionUpdated"
    />
    <AddEditPermissionDialog
      v-model:is-dialog-visible="isAddPermissionDialogVisible"
      @permissionUpdated="handlePermissionUpdated"
    />
  </Layout>
</template>

<script setup>
import { ref, watch } from "vue";
import { toast } from "vue3-toastify";
import { format } from "date-fns";
import axios from "axios";
import { Head, router } from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import { debounce } from "lodash";
import AddEditPermissionDialog from "./AddEditPermissionDialog.vue";
import Swal from "sweetalert2";

const props = defineProps({
  permissionData: Object,
  filters: Object,
});

const headers = [
  { title: "Name", key: "name", sortable: false },
  { title: "Assigned To", key: "roles", sortable: false },
  { title: "Module", key: "module", sortable: false },
  { title: "Created Date", key: "created_at", sortable: false },
  { title: "Actions", key: "actions", sortable: false },
];

const search = ref(props.filters.search || "");
const page = ref(props.permissionData.current_page);
const itemsPerPage = ref(props.permissionData.per_page);

const fetchData = () => {
  router.get(
    route("permission.index"),
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

const isAddPermissionDialogVisible = ref(false);
const isPermissionDialogVisible = ref(false);
const permissionName = ref("");
const selectedPermissionId = ref();

const formatDate = (dateString) => {
  if (!dateString) return "-";
  return format(new Date(dateString), "dd MMM yyyy, h:mm a");
};

const handlePermissionUpdated = async (updatedPermission) => {
  await fetchData();
};
const editPermission = (name, id) => {
  isPermissionDialogVisible.value = true;
  permissionName.value = name;
  selectedPermissionId.value = id;
};

const deletePermission = async (id) => {
  try {
    const response = await axios.delete(route("permission.destroy", id));

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
    toast.error("An error occurred while deleting the permission.", {
      theme: "colored",
      type: "error",
    });
    console.error(error);
  }
};
</script>
