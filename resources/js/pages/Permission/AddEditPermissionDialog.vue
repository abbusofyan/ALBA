<template>
  <VDialog
    :width="$vuetify.display.smAndDown ? 'auto' : 600"
    :model-value="props.isDialogVisible"
    @update:model-value="onReset"
  >
    <DialogCloseBtn @click="onReset" />

    <VCard class="pa-2 pa-sm-10">
      <VCardText>
        <h4 class="text-h4 text-center mb-2">
          {{ props.permissionName ? "Edit" : "Add" }} Permission
        </h4>
        <p class="text-body-1 text-center mb-6">
          {{ props.permissionName ? "Edit" : "Add" }} permission as per your
          requirements.
        </p>

        <VForm @submit.prevent="onSubmit">
          <VAlert v-if="errorMessage" type="error" class="mb-4">
            {{ errorMessage }}
          </VAlert>

          <div class="d-flex gap-4 mb-6 flex-wrap flex-column flex-sm-row">
            <AppSelect
              v-if="!props.permissionId"
              v-model="selectedModule"
              :items="processedModules"
              placeholder="Select Modules"
            />
            <AppTextField
              v-model="currentPermissionName"
              placeholder="Permission Name"
            />
            <VBtn :loading="isSubmitting" type="submit">
              {{ props.permissionName ? "Update" : "Add" }}
            </VBtn>
          </div>
        </VForm>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  permissionName: {
    type: String,
    required: false,
    default: "",
  },
  permissionId: {
    type: Number,
    required: false,
  },
});

const emit = defineEmits([
  "update:isDialogVisible",
  "update:permissionName",
  "permissionUpdated",
]);

const currentPermissionName = ref("");
const isSubmitting = ref(false);
const errorMessage = ref("");
const selectedModule = ref();
const onReset = () => {
  emit("update:isDialogVisible", false);
  currentPermissionName.value = "";
  errorMessage.value = null;
  selectedModule.value = null;
};

const modules = ref([]);

const fetchModules = async () => {
  try {
    const response = await axios.get(route("menu.getData"));
    modules.value = response.data.data;
  } catch (error) {
    console.error("Error fetching permissions:", error);
  }
};

const processedModules = computed(() => {
  return Array.isArray(modules.value)
    ? modules.value.map((item) => ({
        title: item.name,
        value: item.id,
      }))
    : [];
});
onMounted(() => {
  fetchModules();
});
const onSubmit = async () => {
  if (!currentPermissionName.value) {
    errorMessage.value = "Permission name is required.";
    return;
  }

  isSubmitting.value = true;
  errorMessage.value = "";

  try {
    let response;

    if (props.permissionName) {
      response = await axios.put(
        route("permission.update", { id: props.permissionId }),
        {
          name: currentPermissionName.value,
        }
      );

      if (response.data.success) {
        toast.info(response.data.message, {
          theme: "colored",
          type: "info",
        });
      }
    } else {
      response = await axios.post(route("permission.store"), {
        name: currentPermissionName.value,
        module_id: selectedModule.value,
      });

      currentPermissionName.value = null;
      selectedModule.value = null;

      if (response.data.success) {
        toast.success(response.data.message, {
          theme: "colored",
          type: "success",
        });
      }
    }

    emit("update:permissionName", currentPermissionName.value);
    emit("update:isDialogVisible", false);

    currentPermissionName.value = "";

    emit("permissionUpdated", response.data);
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
  } finally {
    isSubmitting.value = false;
  }
};

watch(
  () => props.isDialogVisible,

  (isVisible) => {
    if (isVisible) {
      currentPermissionName.value = props.permissionName;
    }
  }
);
</script>

<style lang="scss">
.permission-table {
  td {
    border: none;
  }
}
</style>
