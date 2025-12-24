// Mock data for the Vue.js dashboard
export const dashboardData = {
  // Top Bar KPIs
  keyStats: {
    totalUsers: 2847,
    totalCO2Points: 89456,
    totalRecyclingSubmissions: 15623,
    totalVouchersRedeemed: 234
  },

  // Recycling Overview
  recyclingOverview: {
    submissionsThisMonth: {
      current: 1247,
      previous: 1089,
      trend: 14.5, // percentage increase
      dailyData: [
        { day: 1, current: 35, previous: 28 },
        { day: 2, current: 42, previous: 31 },
        { day: 3, current: 38, previous: 35 },
        { day: 4, current: 45, previous: 40 },
        { day: 5, current: 52, previous: 38 },
        { day: 6, current: 48, previous: 42 },
        { day: 7, current: 55, previous: 45 },
        { day: 8, current: 41, previous: 39 },
        { day: 9, current: 47, previous: 36 },
        { day: 10, current: 53, previous: 41 },
        { day: 11, current: 49, previous: 44 },
        { day: 12, current: 58, previous: 47 },
        { day: 13, current: 44, previous: 40 },
        { day: 14, current: 51, previous: 43 },
        { day: 15, current: 46, previous: 38 },
        { day: 16, current: 54, previous: 46 },
        { day: 17, current: 50, previous: 42 },
        { day: 18, current: 57, previous: 49 },
        { day: 19, current: 43, previous: 37 },
        { day: 20, current: 48, previous: 41 },
        { day: 21, current: 52, previous: 44 },
        { day: 22, current: 59, previous: 48 },
        { day: 23, current: 45, previous: 39 },
        { day: 24, current: 53, previous: 45 },
        { day: 25, current: 47, previous: 41 },
        { day: 26, current: 56, previous: 47 },
        { day: 27, current: 49, previous: 43 },
        { day: 28, current: 61, previous: 50 },
        { day: 29, current: 44, previous: 38 },
        { day: 30, current: 52, previous: 44 }
      ]
    },
    topActiveBins: [
      { id: 1, location: 'Central Park East', type: 'Plastic', submissions: 156 },
      { id: 2, location: 'University Campus', type: 'E-waste', submissions: 142 },
      { id: 3, location: 'Downtown Mall', type: 'Paper', submissions: 128 },
      { id: 4, location: 'Riverside Plaza', type: 'Plastic', submissions: 115 },
      { id: 5, location: 'Tech District', type: 'E-waste', submissions: 98 },
      { id: 6, location: 'Green Valley', type: 'Glass', submissions: 87 },
      { id: 7, location: 'Metro Station', type: 'Paper', submissions: 76 },
      { id: 8, location: 'Shopping Center', type: 'Plastic', submissions: 65 },
      { id: 9, location: 'Business Park', type: 'E-waste', submissions: 54 },
      { id: 10, location: 'Community Center', type: 'Mixed', submissions: 43 }
    ],
    co2Leaderboard: {
      last7Days: [
        { id: 1, name: 'Sarah Chen', co2Points: 85, submissions: 17 },
        { id: 2, name: 'Mike Rodriguez', co2Points: 72, submissions: 14 },
        { id: 3, name: 'Emma Thompson', co2Points: 68, submissions: 13 },
        { id: 4, name: 'David Kim', co2Points: 61, submissions: 12 },
        { id: 5, name: 'Lisa Wang', co2Points: 58, submissions: 11 },
        { id: 6, name: 'Alex Johnson', co2Points: 55, submissions: 11 },
        { id: 7, name: 'Maria Garcia', co2Points: 52, submissions: 10 },
        { id: 8, name: 'James Wilson', co2Points: 49, submissions: 9 },
        { id: 9, name: 'Anna Brown', co2Points: 46, submissions: 9 },
        { id: 10, name: 'Tom Davis', co2Points: 43, submissions: 8 }
      ],
      last30Days: [
        { id: 1, name: 'Sarah Chen', co2Points: 445, submissions: 89 },
        { id: 2, name: 'Mike Rodriguez', co2Points: 380, submissions: 76 },
        { id: 3, name: 'Emma Thompson', co2Points: 340, submissions: 68 },
        { id: 4, name: 'David Kim', co2Points: 310, submissions: 62 },
        { id: 5, name: 'Lisa Wang', co2Points: 290, submissions: 58 },
        { id: 6, name: 'Alex Johnson', co2Points: 275, submissions: 55 },
        { id: 7, name: 'Maria Garcia', co2Points: 260, submissions: 52 },
        { id: 8, name: 'James Wilson', co2Points: 245, submissions: 49 },
        { id: 9, name: 'Anna Brown', co2Points: 230, submissions: 46 },
        { id: 10, name: 'Tom Davis', co2Points: 215, submissions: 43 }
      ]
    }
  },

  // Rewards & Vouchers
  rewardsAndVouchers: {
    mostRedeemedRewards: [
      { id: 1, name: 'Coffee Shop Voucher', pointsCost: 50, redemptions: 89 },
      { id: 2, name: 'Bookstore Credit', pointsCost: 100, redemptions: 67 },
      { id: 3, name: 'Public Transport Pass', pointsCost: 75, redemptions: 45 },
      { id: 4, name: 'Movie Theater Ticket', pointsCost: 120, redemptions: 34 },
      { id: 5, name: 'Restaurant Discount', pointsCost: 80, redemptions: 28 }
    ],
    lowStockRewards: [
      { id: 1, name: 'Premium Coffee Voucher', threshold: 10, remaining: 8 },
      { id: 2, name: 'Gym Membership Discount', threshold: 10, remaining: 5 },
      { id: 3, name: 'Spa Treatment Voucher', threshold: 10, remaining: 3 },
      { id: 4, name: 'Tech Store Credit', threshold: 10, remaining: 7 }
    ]
  }
};

