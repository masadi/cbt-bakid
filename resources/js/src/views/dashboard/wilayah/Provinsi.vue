<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Rekapitulasi Wilayah {{nama}}</h2>
      </b-card-title>
    </b-card-header>
    <b-card-body>
      <datatable :items="items" :fields="fields" :isBusy="isBusy"></datatable>
    </b-card-body>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody } from 'bootstrap-vue'
import Datatable from './TableProvinsi.vue' //IMPORT COMPONENT DATATABLENYA
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
      nama: '',
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
        key: 'pd',
        label: 'Total PD',
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
      ],
      items: []
    }
  },
  created() {
    var kode_wilayah;
    if(this.hasRole('administrator')){
      kode_wilayah = this.$route.params.kode_wilayah;
    } else {
      kode_wilayah = this.user.username;
    }
    this.$http.get('/dashboard/wilayah/'+kode_wilayah).then(response => {
      this.isBusy = false
      this.nama = response.data.nama
      this.items = response.data.all_wilayah
    })
  },
}
</script>
