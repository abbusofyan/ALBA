<template>
<div class="recycling-overview">
    <h2 class="section-title">Recycling Overview</h2>

    <!-- Submissions Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">📈 Submissions This Month</h3>
        </div>
        <div class="chart-content">
            <div class="chart-summary">
                <div class="current-value">
                    <span class="value">{{ formatNumber(submissionsData.current) }}</span>
                    <span class="label">Previous month: {{ formatNumber(submissionsData.previous) }}</span>
                </div>
                <div class="trend-indicator positive">
                    ↗ {{ submissionsData.trend }}% vs last month
                </div>
            </div>

            <div class="chart-container">
                <Line :data="chartData" :options="chartOptions" />
            </div>
        </div>
    </div>

    <div class="overview-grid">
        <!-- Top 10 Active Bins -->
        <div class="overview-card">
            <div class="card-header">
                <h3 class="card-title">📍 Top 10 Active Bins</h3>
            </div>
            <div class="card-content">
                <div v-for="(bin, index) in topActiveBins" :key="bin.id" class="bin-item">
                    <div :class="['bin-rank', `rank-${index + 1}`]">{{ index + 1 }}</div>
                    <div class="bin-info">
                        <div class="bin-location">
                            <a :href="`#`" @click.prevent="detailBin(bin.id)" class="font-weight-medium text-link">
                                {{ bin.location }}
                            </a>
                        </div>
                        <div :class="['bin-type', `bin-type-${bin.type.toLowerCase()}`]">{{ bin.type }}</div>
                    </div>
                    <div class="bin-stats">
                        <div class="submissions-count">{{ bin.submissions }}</div>
                        <div class="submissions-label">submissions</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="overview-card">
            <div class="card-header">
                <h3 class="card-title">🏆 CO2 Leaderboard - Top 10 Users</h3>
                <div class="filter-dropdown">
                    <select v-model="selectedFilter" @change="updateLeaderboard" class="filter-select">
                        <option value="last30Days">Last 30 days</option>
                        <option value="last7Days">Last 7 days</option>
                    </select>
                </div>
            </div>
            <div class="card-content">
                <div v-for="(user, index) in currentLeaderboard" :key="user.id" class="user-item">
                    <div :class="['user-rank', `rank-${index + 1}`]">{{ index + 1 }}</div>
                    <div class="user-info">
                        <div class="user-name">
							<a :href="`#`" @click.prevent="detailUser(user.id)" class="font-weight-medium text-link">
								{{ user.name }}
							</a>
						</div>
                        <div class="user-submissions">{{ user.submissions }} submissions</div>
                    </div>
                    <div class="user-stats">
                        <div class="co2-points">{{ parseInt(user.co2Points) }}</div>
                        <div class="points-label">CO2 points</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import {
    computed,
    ref
} from "vue";
import {
    Line
} from "vue-chartjs";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
} from "chart.js";
import {
    router
} from "@inertiajs/vue3";

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);

// ✅ Props
const props = defineProps({
    recyclingOverview: {
        type: Array,
        required: true,
    },
});

const selectedFilter = ref("last30Days");

const detailBin = (id) => {
    router.visit(route("bins.show", id));
};

const detailUser = (id) => {
    router.visit(route("users.show", id));
};

// ✅ Computed properties
const submissionsData = computed(() => props.recyclingOverview.submissionsThisMonth);
const topActiveBins = computed(() => props.recyclingOverview.topActiveBins);
const currentLeaderboard = computed(
    () => props.recyclingOverview.co2Leaderboard[selectedFilter.value]
);

const chartData = computed(() => {
    const dailyData = submissionsData.value.dailyData;
    return {
        labels: dailyData.map((d) => d.day),
        datasets: [{
                label: "Current Month",
                data: dailyData.map((d) => d.current),
                borderColor: "#059669",
                backgroundColor: "rgba(5, 150, 105, 0.1)",
                borderWidth: 3,
                pointBackgroundColor: "#059669",
                pointBorderColor: "#059669",
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.1,
            },
            {
                label: "Previous Month",
                data: dailyData.map((d) => d.previous),
                borderColor: "#9ca3af",
                backgroundColor: "rgba(156, 163, 175, 0.1)",
                borderWidth: 2,
                borderDash: [5, 5],
                pointBackgroundColor: "#9ca3af",
                pointBorderColor: "#9ca3af",
                pointRadius: 3,
                pointHoverRadius: 5,
                tension: 0.1,
            },
        ],
    };
});

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "top",
            labels: {
                usePointStyle: true,
                padding: 20,
            },
        },
        tooltip: {
            mode: "index",
            intersect: false,
            backgroundColor: "white",
            titleColor: "#374151",
            bodyColor: "#374151",
            borderColor: "#e5e7eb",
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: true,
        },
    },
    scales: {
        x: {
            display: true,
            title: {
                display: true,
                text: "Day of Month",
            },
            grid: {
                color: "#f3f4f6",
            },
        },
        y: {
            display: true,
            title: {
                display: true,
                text: "Submissions",
            },
            grid: {
                color: "#f3f4f6",
            },
        },
    },
    interaction: {
        mode: "nearest",
        axis: "x",
        intersect: false,
    },
}));

// ✅ Helpers
const formatNumber = (num) => num.toLocaleString();
const updateLeaderboard = () => {
    // No logic needed, computed handles reactivity
};
</script>


<style scoped>
.recycling-overview {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #111827;
    margin-bottom: 1.5rem;
}

.chart-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
}

.chart-header {
    margin-bottom: 1rem;
}

.chart-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.chart-summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.current-value .value {
    font-size: 2rem;
    font-weight: bold;
    color: #111827;
    display: block;
}

.current-value .label {
    font-size: 0.875rem;
    color: #6b7280;
}

.trend-indicator {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-weight: 600;
}

.trend-indicator.positive {
    color: #059669;
}

.chart-container {
    height: 300px;
    position: relative;
}

.overview-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.overview-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-dropdown {
    position: relative;
}

.filter-select {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    font-size: 0.875rem;
    color: #374151;
    cursor: pointer;
    transition: border-color 0.2s ease;
}

.filter-select:hover {
    border-color: #9ca3af;
}

.filter-select:focus {
    outline: none;
    border-color: #059669;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}

.card-content {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.bin-item,
.user-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 8px;
    transition: background-color 0.2s ease;
}

.bin-item:hover,
.user-item:hover {
    background: #f3f4f6;
}

.bin-rank,
.user-rank {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: bold;
    color: white;
    margin-right: 0.75rem;
}

.rank-1 {
    background: #fbbf24;
}

.rank-2 {
    background: #9ca3af;
}

.rank-3 {
    background: #f97316;
}

.rank-1,
.rank-2,
.rank-3 {
    color: white;
}

.bin-rank:not(.rank-1):not(.rank-2):not(.rank-3),
.user-rank:not(.rank-1):not(.rank-2):not(.rank-3) {
    background: #374151;
}

.bin-info,
.user-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.bin-location,
.user-name {
    font-weight: 500;
    color: #111827;
}

.bin-type {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
    width: fit-content;
}

.bin-type-plastic {
    background: #dbeafe;
    color: #1e40af;
}

.bin-type-e-waste {
    background: #fed7aa;
    color: #ea580c;
}

.bin-type-paper {
    background: #dcfce7;
    color: #166534;
}

.bin-type-glass {
    background: #e9d5ff;
    color: #7c3aed;
}

.bin-type-mixed {
    background: #f3f4f6;
    color: #374151;
}

.user-submissions {
    font-size: 0.75rem;
    color: #6b7280;
}

.bin-stats,
.user-stats {
    text-align: right;
}

.submissions-count,
.co2-points {
    font-size: 1.125rem;
    font-weight: bold;
    color: #111827;
}

.co2-points {
    color: #059669;
}

.submissions-label,
.points-label {
    font-size: 0.75rem;
    color: #6b7280;
}

@media (max-width: 1200px) {
    .overview-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .overview-grid {
        grid-template-columns: 1fr;
    }

    .chart-summary {
        flex-direction: column;
        align-items: flex-start;
    }

    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
