<template>
  <section>
    <VCard>
      <VCardText class="d-flex flex-wrap gap-4">
        <div class="d-flex gap-2 align-center">
          <p class="text-body-1 mb-0">Show</p>
          <AppSelect
            :model-value="itemsPerPage"
            :items="[
              { value: 10, title: '10' },
              { value: 25, title: '25' },
              { value: 50, title: '50' },
              { value: 100, title: '100' },
              { value: -1, title: 'All' },
            ]"
            style="inline-size: 5.5rem"
            @update:model-value="itemsPerPage = parseInt($event, 10)"
          />
        </div>

        <VSpacer />

        <div class="d-flex align-center flex-wrap gap-4">
          <AppTextField
            v-model="search"
            placeholder="Search User"
            style="inline-size: 15.625rem"
          />

          <AppSelect
            v-model="selectedRole"
            placeholder="Select Role"
            :items="processedRoles"
            clearable
            clear-icon="tabler-x"
            style="inline-size: 10rem"
          />
        </div>
      </VCardText>

      <VDivider />

      <VDataTableServer
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :items-per-page-options="[
          { value: 10, title: '10' },
          { value: 20, title: '20' },
          { value: 50, title: '50' },
          { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' },
        ]"
        :items="processedUsers"
        :items-length="totalUsers"
        :headers="headers"
        class="text-no-wrap"
        @update:options="updateOptions"
      >
        <template #item.user="{ item }">
          <div class="d-flex align-center gap-x-4">
            <div class="d-flex flex-column">
              <h6 class="text-base font-weight-medium text-link">
                {{ item.name }}
              </h6>
              <div class="text-sm">
                {{ item.email }}
              </div>
            </div>
          </div>
        </template>

        <template #item.roles="{ item }">
          <div class="d-flex align-center gap-x-2">
            <div class="text-capitalize text-high-emphasis text-body-1">
              {{ item.roles }}
            </div>
          </div>
        </template>

        <template #item.actions="{ item }">
          <IconBtn @click="deleteUser(item.id)">
            <VIcon icon="tabler-trash" />
          </IconBtn>

          <IconBtn @click="detailUser(item.id)">
            <VIcon icon="tabler-eye" />
          </IconBtn>
        </template>

        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="itemsPerPage"
            :total-items="totalUsers"
          />
        </template>
      </VDataTableServer>
    </VCard>
  </section>
</template>

<script setup>
import EventBus from "@/pages/Events/eventBus";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import { format } from "date-fns";
import { debounce } from "lodash";
import Swal from "sweetalert2";
import { onMounted, ref, watch } from "vue";
import { toast } from "vue3-toastify";
const itemsPerPage = ref(10);
const page = ref(1);
const totalUsers = ref(0);
const search = ref("");
const selectedRole = ref();
const debouncedSearch = ref("");
const users = ref([]);

const headers = [
  {
    title: "User",
    key: "user",
  },
  {
    title: "Roles",
    key: "roles",
    sortable: false,
  },

  {
    title: "Actions",
    key: "actions",
    sortable: false,
  },
];

const formatDate = (dateString) => {
  if (!dateString) return "-";
  return format(new Date(dateString), "dd MMM yyyy, h:mm a");
};

const processedUsers = computed(() => {
  return users.value.map((user) => ({
    ...user,
    roles: user.roles.map((role) => role.name).join(", "),
  }));
});

const processedRoles = computed(() => {
  return Array.isArray(roleItems.value)
    ? roleItems.value.map((item) => ({
        title: item.name,
        value: item.id,
      }))
    : [];
});

const roleItems = ref([]);

const fetchRoles = async () => {
  try {
    const response = await axios.get(route("role.getData"));
    roleItems.value = response.data.data;
  } catch (error) {
    console.error("Error fetching permissions:", error);
  }
};
const fetchUsers = async () => {
  try {
    const params = {
      search: debouncedSearch.value,
      roleId: selectedRole.value,
      page: page.value,
      itemsPerPage: itemsPerPage.value,
    };

    const getDataRoute = route("role.getUsers");
    const response = await axios.get(getDataRoute, { params });
    users.value = response.data.data;

    totalUsers.value = response.data.total;
  } catch (error) {
    console.error("Error fetching permissions:", error);
  }
};

onMounted(() => {
  EventBus.on("updateRole", () => {
    fetchRoles();
    fetchUsers();
  });
});
const handleSearchChange = debounce(() => {
  debouncedSearch.value = search.value;
  fetchUsers();
}, 300);

watch(search, handleSearchChange);

watch(selectedRole, () => fetchUsers());

onMounted(() => {
  fetchUsers();
  fetchRoles();
});

const updateOptions = ({ page: newPage, itemsPerPage: newItemsPerPage }) => {
  page.value = newPage;
  itemsPerPage.value = newItemsPerPage;
  fetchUsers();
};

const deleteUser = async (id) => {
  const result = await Swal.fire({
    title: "Are you sure? <br> <i class='fa-solid fa-trash-can'></i>",
    text: "This action cannot be undone! The data will be permanently deleted!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#ea5455",
    cancelButtonColor: "#6CC9CF",
    confirmButtonText: "Yes, Proceed!",
    cancelButtonText: "Cancel",
  });

  if (result.isConfirmed) {
    try {
      const response = await axios.delete(route("users.destroy", id));

      if (response.data.success) {
        toast.success(response.data.message, {
          theme: "colored",
          type: "success",
        });
        fetchUsers();
      } else {
        toast.error(response.data.message, {
          theme: "colored",
          type: "error",
        });
      }
    } catch (error) {
      toast.error("An error occurred while deleting the user.", {
        theme: "colored",
        type: "error",
      });
    }
  }
};

const detailUser = async (id) => {
  router.visit(route("users.show", id));
};
</script>
