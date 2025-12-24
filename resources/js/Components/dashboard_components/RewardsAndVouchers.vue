<template>
<div class="rewards-vouchers">
    <h2 class="section-title">Rewards & Vouchers</h2>

    <div class="rewards-grid">
        <!-- Most Redeemed Rewards -->
        <div class="rewards-card">
            <div class="card-header">
                <h3 class="card-title">⭐ Most Redeemed Rewards (This Month)</h3>
            </div>
            <div class="card-content">
                <div v-for="(reward, index) in mostRedeemedRewards" :key="reward.id" class="reward-item">
                    <div class="reward-rank">{{ index + 1 }}</div>
                    <div class="reward-info">
                        <div class="reward-name">
							<a :href="`#`" @click.prevent="detailReward(reward.id)" class="font-weight-medium text-link">
								{{ reward.name }}
							</a>
						</div>
                        <div class="reward-cost">{{ reward.pointsCost }} points each</div>
                    </div>
                    <div class="reward-stats">
                        <div class="redemption-count">{{ reward.redemptions }}</div>
                        <div class="redemption-label">redemptions</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Rewards -->
        <div class="rewards-card low-stock-card">
            <div class="card-header">
                <h3 class="card-title">⚠️ Rewards Low in Voucher Codes</h3>
            </div>
            <div class="card-content">
                <div v-for="reward in lowStockRewards" :key="reward.id" class="low-stock-item">
                    <div class="stock-indicator"></div>
                    <div class="reward-info">
                        <div class="reward-name">
							<a :href="`#`" @click.prevent="detailReward(reward.id)" class="font-weight-medium text-link">
								{{ reward.name }}
							</a>
						</div>
                    </div>
                    <div class="stock-stats">
                        <div :class="['remaining-count', reward.remaining <= 5 ? 'critical' : 'warning']">
                            {{ reward.remaining }}
                        </div>
                        <div class="remaining-label">remaining</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats - Only Total Redemptions -->
    <div class="summary-stats">
        <div class="summary-card">
            <div class="summary-icon">🎁</div>
            <div class="summary-content">
                <div class="summary-value">{{ totalRedemptions }}</div>
                <div class="summary-label">Total Redemptions</div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import {
    computed
} from "vue";
import {
    router
} from "@inertiajs/vue3";

// ✅ Props
const props = defineProps({
    rewardsAndVouchers: {
        type: Object,
        required: true,
    },
});

// ✅ Computed properties
const mostRedeemedRewards = computed(() => props.rewardsAndVouchers.mostRedeemedRewards);

const lowStockRewards = computed(() => props.rewardsAndVouchers.lowStockRewards);

const totalRedemptions = computed(() =>
    mostRedeemedRewards.value.reduce((sum, reward) => sum + reward.redemptions, 0)
);

const detailReward = (id) => {
    router.visit(route("rewards.show", id));
};
</script>


<style scoped>
.rewards-vouchers {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #111827;
    margin-bottom: 1.5rem;
}

.rewards-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.rewards-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.low-stock-card {
    border-color: #fed7aa;
}

.card-header {
    margin-bottom: 1rem;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-content {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.reward-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    transition: background-color 0.2s ease;
}

.reward-item:hover {
    background: #f3f4f6;
}

.reward-rank {
    width: 2rem;
    height: 2rem;
    background: #fbbf24;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: bold;
    margin-right: 0.75rem;
}

.reward-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.reward-name {
    font-weight: 500;
    color: #111827;
}

.reward-cost,
.threshold-info {
    font-size: 0.875rem;
    color: #6b7280;
}

.reward-stats,
.stock-stats {
    text-align: right;
}

.redemption-count {
    font-size: 1.125rem;
    font-weight: bold;
    color: #059669;
}

.redemption-label,
.remaining-label {
    font-size: 0.75rem;
    color: #6b7280;
}

.low-stock-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: #fef3cd;
    border: 1px solid #fed7aa;
    border-radius: 8px;
}

.stock-indicator {
    width: 0.75rem;
    height: 0.75rem;
    background: #f97316;
    border-radius: 50%;
    margin-right: 0.75rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}

.remaining-count {
    font-size: 1.125rem;
    font-weight: bold;
}

.remaining-count.warning {
    color: #f97316;
}

.remaining-count.critical {
    color: #dc2626;
}

.summary-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.summary-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.summary-icon {
    font-size: 2rem;
    width: 3rem;
    height: 3rem;
    background: #dcfce7;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.summary-content {
    flex: 1;
}

.summary-value {
    font-size: 1.875rem;
    font-weight: bold;
    color: #111827;
}

.summary-label {
    font-size: 0.875rem;
    color: #6b7280;
}

@media (max-width: 1200px) {
    .rewards-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .rewards-grid {
        grid-template-columns: 1fr;
    }

    .summary-stats {
        grid-template-columns: 1fr;
    }
}
</style>
