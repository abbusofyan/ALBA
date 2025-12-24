<script setup>
import Layout from "@/layouts/blank.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import { toast } from "vue3-toastify";

const props = defineProps({
	setting: Object,
	errors: Object,
});

const form = useForm({
	user_max_daily_reward: null,
	user_max_monthly_redemption: null
});

onMounted(() => {
	form.user_max_daily_reward = props.setting.user_max_daily_reward ? props.setting.user_max_daily_reward : 0;
	form.user_max_monthly_redemption = props.setting.user_max_monthly_redemption ? props.setting.user_max_monthly_redemption : 0;
});

const currentTab = ref("points");

const saveSetting = () => {
	form.post(route("settings.points.store"), {
		onSuccess: () => {
			toast.created("Setting saved succesfully.");
		},
		onError: () => {
			toast.error("An error occurred while saving settings.");
		},
	});
};

watch(
	() => usePage().props.flash,
	(flash) => {
		if (flash.success) {
			toast.success(flash.success, {
				theme: "colored",
				type: "success",
			});
		} else if (flash.created) {
			toast.success(flash.created, {
				theme: "colored",
				type: "success",
			});
		} else if (flash.error) {
			toast.error(flash.error, {
				theme: "colored",
				type: "error",
			});
		}
	},
	{
		deep: true,
	}
);
</script>

<template>
	<Head title="Settings" />
	<Layout>
		<v-breadcrumbs
			class="pt-0"
			:items="[
				{
					title: 'Setting',
					disabled: false,
					href: '/settings',
				},
			]"
		>
			<template v-slot:prepend></template>
		</v-breadcrumbs>

		<VCard>
			<VForm @submit.prevent="saveSetting">
				<VCardItem>
					<VCardTitle>Points Settings</VCardTitle>
					<VCardSubtitle>
						How much point can a user get in a single day.
					</VCardSubtitle>
				</VCardItem>
				<VCardText>
					<VRow>
						<VCol cols="12">
							<label for="user_max_daily_reward">Daily Cap Points Value<span class="text-error">*</span></label>
							<AppTextField
								v-model="form.user_max_daily_reward"
								placeholder="600"
								type="number"
							/>
							<div v-if="form.errors.user_max_daily_reward" class="text-error">
								{{ form.errors.user_max_daily_reward }}
							</div>
						</VCol>
					</VRow>
				</VCardText>

				<VCardItem>
					<VCardTitle>Redemption Settings</VCardTitle>
					<VCardSubtitle>
						Limit on how many points a user can redeem for vouchers each month
					</VCardSubtitle>
				</VCardItem>
				<VCardText>
					<VRow>
						<VCol cols="12">
							<label for="user_max_daily_reward">Monthly Points Redemtion Cap<span class="text-error">*</span></label>
							<AppTextField
								v-model="form.user_max_monthly_redemption"
								placeholder="600"
								type="number"
							/>
							<div v-if="form.errors.user_max_monthly_redemption" class="text-error">
								{{ form.errors.user_max_monthly_redemption }}
							</div>
						</VCol>
					</VRow>
				</VCardText>

				<VCardText v-if="$filters.can('update-setting')">
					<div class="d-flex gap-4 mt-5">
						<VBtn type="submit" :loading="form.processing">
							Save Changes
						</VBtn>
						<VBtn color="secondary" variant="tonal" type="button" @click="form.reset()" >
							Cancel
						</VBtn>
					</div>
				</VCardText>

			</VForm>
		</VCard>
	</Layout>
</template>
