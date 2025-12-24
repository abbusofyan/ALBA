<template>
  <VCardText>
    <VRow>
      <VCol>
        <h6 class="text-sm font-weight-regular">Weekly Recycling Overview</h6>
      </VCol>
    </VRow>
    <VRow>
      <VCol>
        <div class="chart-container" style="height: 350px">
          <canvas ref="chartCanvas" />
        </div>
      </VCol>
    </VRow>

    <VDivider class="my-4" />

    <VRow>
      <VCol>
        <div class="d-flex justify-space-between align-center">
          <h5 class="text-h5">Statistics</h5>
          <span class="text-sm text-disabled">Updated 1 minute ago</span>
        </div>
      </VCol>
    </VRow>

    <VRow>
      <VCol v-for="stat in statistics" :key="stat.name" cols="12" sm="6" md="3">
        <VCard>
          <VCardText>
            <div class="d-flex align-center gap-x-4">
              <VAvatar
                :style="{ backgroundColor: stat.backgroundColor }"
                rounded
                size="40"
                variant="flat"
              >
                <VIcon icon="tabler-recycle" size="24" color="white" />
              </VAvatar>
              <div>
                <h6 class="text-h6">
                  {{ stat.value }}
                </h6>
                <span class="text-sm text-capitalize">{{ stat.name }}</span>
              </div>
            </div>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </VCardText>
</template>

<script setup>
import {
  BarController,
  BarElement,
  CategoryScale,
  Chart,
  Legend,
  LinearScale,
  Title,
  Tooltip,
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";

Chart.register(
  CategoryScale,
  LinearScale,
  BarElement,
  BarController,
  Title,
  Tooltip,
  Legend,
  ChartDataLabels
);

const props = defineProps({
  chartData: {
    type: Array,
    default: () => []
  }
});

const chartCanvas = ref(null);
let chartInstance = null;

// Enhanced color mapping with direct hex colors for better compatibility
const getBinTypeColors = () => {
  return {
    // Main bin types with soft, modern colors
    "E-waste Bin": {
      background: "#7C9CBF", // Soft blue
      border: "#5A7BA3",
      vuetifyColor: "primary",
      icon: "mdi-recycle"
    },
    "I-bin": {
      background: "#4DD0E1", // Cyan
      border: "#26C6DA",
      vuetifyColor: "cyan",
      icon: "mdi-recycle"
    },
    "RVM": {
      background: "#FFB74D", // Orange
      border: "#FF9800",
      vuetifyColor: "orange",
      icon: "mdi-recycle"
    },
    "Recycling Bin": {
      background: "#81C784", // Green
      border: "#66BB6A",
      vuetifyColor: "success",
      icon: "mdi-recycle"
    },

    // I-Bin variations
    "I-Bin (Fabrics)": {
      background: "#7C9CBF", // Soft blue
      border: "#5A7BA3",
      vuetifyColor: "primary",
      icon: "mdi-recycle"
    },
    "I-Bin (Metals & E-waste)": {
      background: "#4DD0E1", // Cyan
      border: "#26C6DA",
      vuetifyColor: "cyan",
      icon: "mdi-recycle"
    },
    "I-Bin (Paper)": {
      background: "#FFB74D", // Orange
      border: "#FF9800",
      vuetifyColor: "orange",
      icon: "mdi-recycle"
    },
    "ICT, Battery & Bulb": {
      background: "#81C784", // Green
      border: "#66BB6A",
      vuetifyColor: "success",
      icon: "mdi-recycle"
    },
    "Battery, Bulb & Tube": {
      background: "#BA68C8", // Purple
      border: "#AB47BC",
      vuetifyColor: "purple",
      icon: "mdi-recycle"
    },
    "Battery": {
      background: "#F06292", // Pink
      border: "#EC407A",
      vuetifyColor: "pink",
      icon: "mdi-recycle"
    },

    // Default fallback
    "default": {
      background: "#B0BEC5", // Light gray
      border: "#90A4AE",
      vuetifyColor: "grey",
      icon: "mdi-recycle"
    }
  };
};

// Transform the data from getChartData API response
const transformedChartData = computed(() => {
  if (!props.chartData || props.chartData.length === 0) {
    return {
      labels: [],
      datasets: []
    };
  }

  // Get unique dates and bin types
  const dates = [...new Set(props.chartData.map(item => {
    const date = new Date(item.date);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit' });
  }))].sort();

  const binTypes = [...new Set(props.chartData.map(item => item.bin_type))];
  const colorMap = getBinTypeColors();

  // Create datasets for each bin type
  const datasets = binTypes.map(binType => {
    const colors = colorMap[binType] || colorMap.default;
    const data = dates.map(date => {
      const item = props.chartData.find(d => {
        const itemDate = new Date(d.date);
        const formattedDate = itemDate.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit' });
        return formattedDate === date && d.bin_type === binType;
      });
      return item ? parseInt(item.total_recyclings) : 0;
    });

    return {
      label: binType,
      data: data,
      backgroundColor: colors.background,
      borderColor: colors.border,
      borderWidth: 2,
      borderRadius: 6,
      borderSkipped: false,
      hoverBackgroundColor: colors.border,
      hoverBorderColor: colors.background,
    };
  });

  return {
    labels: dates,
    datasets: datasets
  };
});

// Calculate statistics from the chart data with proper color mapping
const statistics = computed(() => {
  const colorMap = getBinTypeColors();

  if (!props.chartData || props.chartData.length === 0) {
    return [];
  }

  // Calculate total recyclings per bin type
  const totals = {};
  props.chartData.forEach(item => {
    if (!totals[item.bin_type]) {
      totals[item.bin_type] = 0;
    }
    totals[item.bin_type] += parseInt(item.total_recyclings);
  });

  return Object.entries(totals).map(([name, value]) => ({
    name,
    value,
    backgroundColor: (colorMap[name] || colorMap.default).background,
  }));
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'bottom',
      labels: {
        usePointStyle: true,
        pointStyle: 'rect',
        padding: 15,
        font: {
          size: 12
        },
        generateLabels: function(chart) {
          const datasets = chart.data.datasets;
          return datasets.map((dataset, i) => ({
            text: dataset.label,
            fillStyle: dataset.backgroundColor,
            strokeStyle: dataset.borderColor,
            lineWidth: dataset.borderWidth,
            hidden: !chart.isDatasetVisible(i),
            datasetIndex: i
          }));
        }
      }
    },
    tooltip: {
      mode: "index",
      intersect: false,
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#fff',
      bodyColor: '#fff',
      borderColor: '#fff',
      borderWidth: 1,
      callbacks: {
        label: function(context) {
          return `${context.dataset.label}: ${context.parsed.y} recyclings`;
        }
      }
    },
    datalabels: {
      anchor: "end",
      align: "top",
      formatter: (value, context) => {
        return value > 0 ? value : '';
      },
      font: {
        weight: "bold",
        size: 10
      },
      color: '#374151'
    },
  },
  scales: {
    x: {
      title: {
        display: true,
        text: "Date",
        font: {
          size: 14,
          weight: 'bold'
        }
      },
      grid: {
        display: false,
      },
      ticks: {
        font: {
          size: 12
        }
      }
    },
    y: {
      title: {
        display: true,
        text: "Number of Recyclings",
        font: {
          size: 14,
          weight: 'bold'
        }
      },
      beginAtZero: true,
      grid: {
        display: true,
        color: 'rgba(0, 0, 0, 0.1)'
      },
      ticks: {
        stepSize: 1,
        font: {
          size: 12
        }
      }
    },
  },
  interaction: {
    intersect: false,
    mode: 'index'
  }
};

const initChart = () => {
  if (chartCanvas.value && transformedChartData.value.labels.length > 0) {
    const ctx = chartCanvas.value.getContext("2d");
    chartInstance = new Chart(ctx, {
      type: "bar",
      data: transformedChartData.value,
      options: chartOptions,
    });
  }
};

const destroyChart = () => {
  if (chartInstance) {
    chartInstance.destroy();
    chartInstance = null;
  }
};

const updateChart = () => {
  destroyChart();
  initChart();
};

// Watch for changes in chartData prop
watch(() => props.chartData, () => {
  updateChart();
}, { deep: true });

onMounted(() => {
  initChart();
});

onUnmounted(() => {
  destroyChart();
});
</script>
