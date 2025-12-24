<template>
  <VRow>
    <!-- 👉 Roles -->
    <VCol v-for="item in roles" :key="item.role" cols="12" sm="6" lg="4">
      <VCard>
        <VCardText class="d-flex align-center pb-4">
          <div class="text-body-1">Total {{ item.users.length }} users</div>
          <VSpacer />
        </VCardText>

        <VCardText>
          <div class="d-flex justify-space-between align-center">
            <div>
              <h5 class="text-h5">
                {{ item.name }}
              </h5>

              <div class="d-flex align-center">
                <a
                  class="mr-2"
                  href="javascript:void(0)"
                  @click="editPermission(item.permissions, item.name, item.id)"
                >
                  Edit
                </a>

                <a href="javascript:void(0)" @click="toggleshowRemove(item.id)">
                  Delete
                </a>
              </div>
            </div>
          </div>
        </VCardText>
      </VCard>
    </VCol>

    <!-- 👉 Add New Role -->
    <VCol cols="12" sm="6" lg="4">
      <VCard class="h-100" :ripple="false">
        <VRow no-gutters class="h-100">
          <VCol
            cols="5"
            class="d-flex flex-column justify-end align-center mt-5"
          >
            <img width="85" :src="girlUsingMobile" />
          </VCol>

          <VCol cols="7">
            <VCardText class="d-flex flex-column align-end justify-end gap-4">
              <VBtn size="small" @click="isAddRoleDialogVisible = true">
                Add New Role
              </VBtn>
              <div class="text-end">
                Add new role,<br />
                if it doesn't exist.
              </div>
            </VCardText>
          </VCol>
        </VRow>
      </VCard>
      <AddEditRoleDialog
        v-model:is-dialog-visible="isAddRoleDialogVisible"
        @updateRolePermissions="handleRolePermissionUpdated"
      />
    </VCol>
  </VRow>

  <AddEditRoleDialog
    v-model:is-dialog-visible="isRoleDialogVisible"
    v-model:role-permissions="roleDetail"
    v-model:role-name="roleName"
    v-model:role-id="roleId"
    @updateRolePermissions="handleRolePermissionUpdated"
  />
  <v-dialog v-model="showRemove" max-width="500">
    <v-card class="p-6">
      <div class="text-xl text-[#FF9F43] pt-6 pl-6 pr-6">Are you sure ?</div>
      <v-card-text> You won't be able to revert this! </v-card-text>
      <v-card-text>
        <VAlert v-if="errorMessage" color="error" class="mb-4">
          {{ errorMessage }}
        </VAlert>
      </v-card-text>
      <v-card-actions>
        <button
          type="button"
          class="text-center w-fit mb-6 mr-2 border-0"
          @click="toggleshowRemove"
        >
          Cancel
        </button>
        <form @submit.prevent="Remove">
          <button type="submit" class="justify-center mb-6 mr-5 float-right">
            Yes, Proceed
          </button>
        </form>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<script setup>
import { ref, watch, onMounted } from "vue";
import { toast } from "vue3-toastify";
import AddEditRoleDialog from "../Role/AddEditRoleDialog.vue";
import girlUsingMobile from "@images/pages/girl-using-mobile.png";
import EventBus from "@/pages/Events/eventBus";
const roles = ref([]);

const handleRolePermissionUpdated = async (updatedPermission) => {
  await fetchRoles();
};

const fetchRoles = async () => {
  try {
    const response = await axios.get(route("role.getData"));
    roles.value = response.data.data;
  } catch (error) {
    console.error("Error fetching permissions:", error);
  }
};

onMounted(fetchRoles);

const isRoleDialogVisible = ref(false);
const roleDetail = ref();
const roleName = ref();
const roleId = ref();
const isAddRoleDialogVisible = ref(false);

const editPermission = (permissions, name, id) => {
  isRoleDialogVisible.value = true;
  roleDetail.value = permissions;
  roleName.value = name;
  roleId.value = id;
};

const selectedItemId = ref(null);
const showRemove = ref(false);
const toggleshowRemove = (id) => {
  selectedItemId.value = id;
  showRemove.value = !showRemove.value;
  errorMessage.value = null;
};
const errorMessage = ref("");
const Remove = async () => {
  if (!selectedItemId.value) {
    toast.error("Item ID is missing", {
      theme: "colored",
      type: "error",
    });

    return;
  }
  try {
    const deleteRoleRoute = route("role.destroy", {
      id: selectedItemId.value,
    });

    const response = await axios.delete(deleteRoleRoute);
    if (response.data.success) {
      toast.error(response.data.message, {
        theme: "colored",
        type: "success",
      });
    }
    fetchRoles();
    showRemove.value = false;
    EventBus.emit("updateRole", response.data);
  } catch (error) {
    if (error.response) {
      errorMessage.value =
        error.response.data.message || "An error occurred on the server.";
    } else if (error.request) {
      errorMessage.value = "No response received from server.";
    } else {
      errorMessage.value =
        error.message || "Something went wrong. Please try again.";
    }
  }
};
</script>
