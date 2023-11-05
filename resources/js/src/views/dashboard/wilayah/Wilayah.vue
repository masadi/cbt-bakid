<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Rekapitulasi Wilayah</h2>
      </b-card-title>
    </b-card-header>
    <b-card-body>
      <datatable :items="items" :fields="fields" :isBusy="isBusy"></datatable>
    </b-card-body>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody } from 'bootstrap-vue'
import Datatable from './TableWilayah.vue' //IMPORT COMPONENT DATATABLENYA
export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    Datatable
  },
  data() {
    return {
      isBusy: true,
      fields: [{
        key: 'nama',
        label: 'Nama Wilayah',
        sortable: false
      },
      {
        key: 'nasional',
        label: 'JUMLAH SMK',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'negeri_nasional',
        label: 'SMK Negeri',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'swasta_nasional',
        label: 'SMK Swasta',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'negeri_penerima',
        label: 'SMK Negeri Telah Terima',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      
      {
        key: 'swasta_penerima',
        label: 'SMK Swasta Telah Terima',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'persentase',
        label: 'Persentase',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'pd',
        label: 'Total PD',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'action',
        label: 'Detil',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      ],
      items: []
    }
  },
  created() {
    this.$http.get('/dashboard/provinsi').then(response => {
      this.isBusy = false
      this.items = response.data.all_wilayah
    })
  },
}
</script>
