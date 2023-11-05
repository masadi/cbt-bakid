<template>
  <div>
    <section v-if="hasRole('administrator')">
        <users-administrator />
      </section>
      <section v-if="hasRole('disprov')">
        <users-disprov />
      </section>
      <section v-if="hasRole('diskab')">
        <users-diskab />
      </section>
  </div>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody } from 'bootstrap-vue'
import UsersAdministrator from './UsersAdministrator.vue'
import UsersDisprov from './UsersDisprov.vue'
import UsersDiskab from './UsersDiskab.vue'

export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    UsersAdministrator,
    UsersDisprov,
    UsersDiskab,
  },
  data() {
    return {
      isBusy: true,
      data: [],
    }
  },
  created() {
    console.log(this.user);
    //this.$http.get('/dashboard/index').then(response => {
      //this.data = response.data
      //this.isBusy = false
      //console.log(this.data);
    //})
    /*this.$ability.update([
      { 'action': 'manage' },
      { 'subject': 'all' }
    ])*/
  },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/pages/dashboard-ecommerce.scss';
@import '~@resources/scss/vue/libs/chart-apex.scss';
</style>
