<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Detil Pengaduan</h2>
      </b-card-title>
    </b-card-header>
    <b-card-body class="text-center" v-if="isBusy">
      <b-spinner class="align-middle"></b-spinner>
      <strong>Loading...</strong>
    </b-card-body>
    <b-card-body v-else>
      detil pengaduan
    </b-card-body>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody, BSpinner } from 'bootstrap-vue'
export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BSpinner
  },
  data() {
    return {
      isBusy: true,
      pengaduan: ''
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    loadPostsData() {
      this.$http.get('/pengaduan/detil/'+this.$route.params.pengaduan_id).then(response => {
        //this.items = response.data.all_pd
        let getData = response.data
        this.isBusy = false
        console.log(getData);
      })
    },
  }
}
</script>
