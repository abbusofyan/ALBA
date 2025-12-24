<template>
  <VDialog
    :width="$vuetify.display.smAndDown ? 'auto' : 900"
    :model-value="props.isDialogVisible"
    @update:model-value="onReset"
  >
    <DialogCloseBtn @click="onReset" />

    <VCard class="pa-sm-10 pa-2">
      <VCardText>
        <h4 class="text-h4 text-center mb-2">
          {{ props.rolePermissions ? "Edit" : "Add New" }} Role
        </h4>
        <p class="text-body-1 text-center mb-6">Set Role Permissions</p>

        <VForm ref="refPermissionForm">
          <AppTextField
            v-model="role"
            label="Role Name"
            placeholder="Enter Role Name"
          />

          <h5 class="text-h5 my-6">Role Permissions</h5>

          <VTable class="permission-table text-no-wrap mb-6">
            <tr>
              <td>
                <h6 class="text-h6">Administrator Access</h6>
              </td>
              <td colspan="10">
                <div class="d-flex justify-end">
                  <VCheckbox
                    v-model="isSelectAll"
                    v-model:indeterminate="isIndeterminate"
                    label="Select All"
                    @change="selectAllPermissions"
                  />
                </div>
              </td>
            </tr>

            <template v-for="group in permissions" :key="group.name">
              <tr>
                <td>
                  <h6 class="text-h6">
                    {{ group.name }}
                  </h6>
                </td>
                <td
                  v-for="permission in group.permissions"
                  :key="permission.id"
                >
                  <div class="d-flex">
                    <VCheckbox
                      v-model="permission.selected"
                      :label="permission.name"
                      @change="updateSelectAllOnInit"
                    />
                  </div>
                </td>
              </tr>
            </template>
          </VTable>

          <VAlert v-if="errorMessage" type="error" class="mb-4">
            {{ errorMessage }}
          </VAlert>

          <div class="d-flex align-center justify-center gap-4">
            <VBtn @click="onSubmit">Submit</VBtn>
            <VBtn color="secondary" variant="tonal" @click="onReset">
              Cancel
            </VBtn>
          </div>
        </VForm>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<script setup>
import { VForm } from "vuetify/components/VForm";
import { ref, watch, onMounted } from "vue";
import { toast } from "vue3-toastify";
import EventBus from "@/pages/Events/eventBus";
import axios from "axios";
const props = defineProps({
  rolePermissions: {
    type: Object,
    required: false,
  },
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  roleName: String,
  roleId: Number,
});

const isSelectAll = ref(false);
const isIndeterminate = ref(false);

const updateSelectAllOnInit = () => {
  const allPermissions = permissions.value.flatMap((group) =>
    group.permissions.map((p) => p.selected)
  );

  isSelectAll.value = allPermissions.every((selected) => selected);
  isIndeterminate.value =
    !isSelectAll.value && allPermissions.some((selected) => selected);
};

const selectAllPermissions = () => {
  permissions.value.forEach((group) => {
    group.permissions.forEach((permission) => {
      permission.selected = isSelectAll.value;
    });
  });
  updateSelectAllOnInit();
};

const emit = defineEmits([
  "update:isDialogVisible",
  "update:rolePermissions",
  "updateRolePermissions",
]);

const permissions = ref([]);

const fetchPermissions = async () => {
  try {
    const response = await axios.get(route("permission.getPermissions"));
    permissions.value = response.data.data;
  } catch (error) {
    console.error("Error fetching permissions:", error);
  }
};

onMounted(() => {
  fetchPermissions();
});

watch(() => permissions.value, updateSelectAllOnInit, {
  deep: true,
  immediate: true,
});
const role = ref("");
const refPermissionForm = ref();

const initializePermissions = () => {
  role.value = props.roleName;
  permissions.value = permissions.value.map((group) => {
    return {
      ...group,
      permissions: group.permissions.map((permission) => {
        const matchedPermission = props.rolePermissions?.find(
          (rolePermission) => rolePermission.id === permission.id
        );
        return {
          ...permission,
          selected: matchedPermission ? matchedPermission.selected : false,
        };
      }),
    };
  });

  updateSelectAllOnInit();
};

watch(
  () => props.isDialogVisible,
  (isVisible) => {
    if (isVisible) {
      initializePermissions();
    }
  }
);
const errorMessage = ref("");
const getSelectedPermissions = () =>
  permissions.value
    .flatMap((group) => group.permissions)
    .filter((permission) => permission.selected)
    .map((permission) => permission.id);

const onSubmit = async () => {
  const payload = {
    name: role.value,
    permissions: getSelectedPermissions(),
  };

  try {
    let response;
    if (props.rolePermissions && props.roleName) {
      response = await axios.put(
        route("role.update", { id: props.roleId }),
        payload
      );
      if (response.data.success) {
        toast.info(response.data.message, {
          theme: "colored",
          type: "info",
        });
      }
      EventBus.emit("updateRole", response.data);
    } else {
      response = await axios.post(route("role.store"), payload);
      if (response.data.success) {
        toast.success(response.data.message, {
          theme: "colored",
          type: "success",
        });
      }
      EventBus.emit("updateRole", response.data);
    }
    errorMessage.value = null;
    emit("updateRolePermissions", response.data);
    emit("update:isDialogVisible", false);
    refPermissionForm.value?.reset();
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

  isSelectAll.value = false;
  refPermissionForm.value?.reset();
};

const onReset = () => {
  emit("update:isDialogVisible", false);
  isSelectAll.value = false;
  refPermissionForm.value?.reset();
};
</script>
<style lang="scss">
.permission-table {
  td {
    padding-block: 0.5rem;

    .v-checkbox {
      min-inline-size: 4.75rem;
    }

    &:not(:first-child) {
      padding-inline: 0.5rem;
    }

    .v-label {
      white-space: nowrap;
    }
  }
}
</style>
