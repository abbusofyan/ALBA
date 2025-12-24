<template>
<div class="key-stats-bar">
	<a :href="`#`" @click.prevent="userIndex()" class="font-weight-medium text-link">
	    <div class="stat-card">
            <div class="stat-icon user-icon">👥</div>
            <div class="stat-content">
                <div class="stat-label">Total Users</div>
                <div class="stat-value">{{ formatNumber(keyStats.totalUsers) }}</div>
            </div>
	    </div>
	</a>


	<a :href="`#`" class="font-weight-medium text-link">
		<div class="stat-card">
			<div class="stat-icon co2-icon">🌱</div>
			<div class="stat-content">
				<div class="stat-label">Total CO2 Points Earned</div>
				<div class="stat-value">{{ formatNumber(keyStats.totalCO2Points) }}</div>
			</div>
		</div>
	</a>


	<a :href="`#`" @click.prevent="recyclingIndex()" class="font-weight-medium text-link">
		<div class="stat-card">
			<div class="stat-icon recycling-icon">♻️</div>
			<div class="stat-content">
				<div class="stat-label">Total Recycling Submissions</div>
				<div class="stat-value">{{ formatNumber(keyStats.totalRecyclingSubmissions) }}</div>
			</div>
		</div>
	</a>


	<a :href="`#`" @click.prevent="rewardIndex()" class="font-weight-medium text-link">
		<div class="stat-card">
			<div class="stat-icon voucher-icon">🎁</div>
			<div class="stat-content">
				<div class="stat-label">Vouchers Redeemed (This Month)</div>
				<div class="stat-value">{{ formatNumber(keyStats.totalVouchersRedeemed) }}</div>
			</div>
		</div>
	</a>

</div>
</template>

<script setup>
import {
    router
} from "@inertiajs/vue3";
const props = defineProps({
    keyStats: {
        type: Array,
        required: true,
    },
});

const formatNumber = (num) => num.toLocaleString();

const userIndex = () => {
    router.visit(route("users.index"));
};

const recyclingIndex = () => {
    router.visit(route("recyclings.index"));
};

const rewardIndex = () => {
    router.visit(route("rewards.index"));
};
</script>


<style scoped>
.key-stats-bar {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    font-size: 2rem;
    width: 3rem;
    height: 3rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-icon {
    background: #dbeafe;
}

.co2-icon {
    background: #dcfce7;
}

.recycling-icon {
    background: #ecfdf5;
}

.voucher-icon {
    background: #faf5ff;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.stat-value {
    font-size: 1.875rem;
    font-weight: bold;
    color: #111827;
}

@media (max-width: 1200px) {
    .key-stats-bar {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .key-stats-bar {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .stat-card {
        padding: 1rem;
    }

    .stat-value {
        font-size: 1.5rem;
    }
}
</style>
