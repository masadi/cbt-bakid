<template>
  <div>
    <dashboard-rekap />
    <dashboard-cp />
  </div>
</template>

<script>
import { BRow, BCol, BCard, BSpinner } from 'bootstrap-vue'
import DashboardRekap from './DashboardRekap.vue'
import DashboardCp from './DashboardCp.vue'

export default {
  components: {
    BRow,
    BCol,
    BCard,
    BSpinner,
    DashboardRekap,
    DashboardCp,
  },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/pages/dashboard-ecommerce.scss';
@import '~@resources/scss/vue/libs/chart-apex.scss';
</style>
