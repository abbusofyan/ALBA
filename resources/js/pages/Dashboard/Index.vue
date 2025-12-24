<template>
  <Head title="Home" />
  <Layout>
	  <div id="app">
        <!-- Header -->
        <header class="app-header">
          <div class="header-content">
            <div class="logo-section">
              <div class="logo">🌱</div>
              <div class="brand-info">
                <h1 class="brand-name">EcoTracker</h1>
                <p class="brand-subtitle">Recycling Dashboard</p>
              </div>
            </div>
            <div class="last-updated">
              Last updated<br>
              <span class="timestamp">{{ currentTime }}</span>
            </div>
          </div>
        </header>

        <main class="main-content">
          <div class="container">
            <KeyStatsBar :keyStats="dashboardData.keyStats" />

            <RecyclingOverview :recyclingOverview="dashboardData.recyclingOverview" />

            <RewardsAndVouchers :rewardsAndVouchers="dashboardData.rewardsAndVouchers" />
          </div>
        </main>

        <footer class="app-footer">
          <div class="container">
            <p>&copy; 2025 EcoTracker. Streamlined recycling platform dashboard.</p>
          </div>
        </footer>
      </div>
  </Layout>
</template>

<script setup>
import Layout from "../../layouts/blank.vue";
import { Link, Head } from "@inertiajs/vue3";
import KeyStatsBar from '../../Components/dashboard_components/KeyStatsBar.vue'
import RecyclingOverview from '../../Components/dashboard_components/RecyclingOverview.vue'
import RewardsAndVouchers from '../../Components/dashboard_components/RewardsAndVouchers.vue'

const currentTime = ref('')

const props = defineProps({
    dashboardData: Array,
})

const updateTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

onMounted(() => {
  updateTime()
  setInterval(updateTime, 1000)
})

</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  background: #f8fafc;
  color: #1f2937;
  line-height: 1.6;
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.app-header {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  padding: 1rem 0;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo {
  width: 3rem;
  height: 3rem;
  background: #059669;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.brand-name {
  font-size: 1.5rem;
  font-weight: bold;
  color: #111827;
  margin: 0;
}

.brand-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.last-updated {
  text-align: right;
  font-size: 0.875rem;
  color: #6b7280;
}

.timestamp {
  font-weight: 600;
  color: #111827;
}

.main-content {
  flex: 1;
  padding: 2rem 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.app-footer {
  background: white;
  border-top: 1px solid #e5e7eb;
  padding: 1.5rem 0;
  margin-top: 2rem;
}

.app-footer p {
  text-align: center;
  color: #6b7280;
  font-size: 0.875rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .header-content {
    padding: 0 1rem;
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .container {
    padding: 0 1rem;
  }

  .main-content {
    padding: 1rem 0;
  }

  .logo-section {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .logo-section {
    flex-direction: column;
    gap: 0.5rem;
  }

  .brand-name {
    font-size: 1.25rem;
  }
}

/* Smooth transitions */
* {
  transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}

/* Focus styles for accessibility */
button:focus,
select:focus,
input:focus {
  outline: 2px solid #059669;
  outline-offset: 2px;
}

/* Loading animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.main-content > .container > * {
  animation: fadeIn 0.6s ease-out;
}
</style>
