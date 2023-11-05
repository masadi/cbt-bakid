<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Data Sekolah</h2>
      </b-card-title>
    </b-card-header>
    <b-card-body>
      <datatable :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" :title="'Hapus Sekolah'" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" />
    </b-card-body>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody } from 'bootstrap-vue'
import Datatable from './TableSekolah.vue' //IMPORT COMPONENT DATATABLENYA
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
        label: 'Nama',
        sortable: true,
        thClass: 'text-center',
      },
      {
        key: 'npsn',
        label: 'NPSN',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'kabupaten',
        label: 'Kabupaten/Kota',
        sortable: true,
        thClass: 'text-center',
      },
      {
        key: 'provinsi',
        label: 'Provinsi',
        sortable: true,
        thClass: 'text-center',
      },
      /*{
        key: 'blanko',
        label: 'Terima Blangko',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      
      {
        key: 'blangko_3',
        label: 'Jumlah Blangko K13-3',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'blangko_4',
        label: 'Jumlah Blangko K13-4',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'khusus',
        label: 'Sekolah Khusus',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },*/
      {
        key: 'actions',
        label: 'Aksi',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      ],
      items: [],
      meta: {},
      current_page: 1, //DEFAULT PAGE YANG AKTIF ADA PAGE 1
      per_page: 10, //DEFAULT LOAD PERPAGE ADALAH 10
      search: '',
      sortBy: 'nama', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: false, //ASCEDING
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    loadPostsData() {
      //let current_page = this.search == '' ? this.current_page : this.current_page != 1 ? 1 : this.current_page
      let current_page = this.current_page//this.search == '' ? this.current_page : 1
      //LAKUKAN REQUEST KE API UNTUK MENGAMBIL DATA POSTINGAN
      this.$http.get('/referensi/sekolah', {
        params: {
          semester_id: 20221,
          page: current_page,
          per_page: this.per_page,
          q: this.search,
          sortby: this.sortBy,
          sortbydesc: this.sortByDesc ? 'DESC' : 'ASC'
        }
      }).then(response => {
        //this.items = response.data.all_pd
        let getData = response.data.data
        this.isBusy = false
        this.items = getData.data//MAKA ASSIGN DATA POSTINGAN KE DALAM VARIABLE ITEMS
        //DAN ASSIGN INFORMASI LAINNYA KE DALAM VARIABLE META
        this.meta = {
          total: getData.total,
          current_page: getData.current_page,
          per_page: getData.per_page,
          from: getData.from,
          to: getData.to,
        }
      })
    },
    //JIKA ADA EMIT TERKAIT LOAD PERPAGE, MAKA FUNGSI INI AKAN DIJALANKAN
    handlePerPage(val) {
      this.per_page = val //SET PER_PAGE DENGAN VALUE YANG DIKIRIM DARI EMIT
      this.loadPostsData() //DAN REQUEST DATA BARU KE SERVER
    },
    //JIKA ADA EMIT PAGINATION YANG DIKIRIM, MAKA FUNGSI INI AKAN DIEKSEKUSI
    handlePagination(val) {
      this.current_page = val //SET CURRENT PAGE YANG AKTIF
      this.loadPostsData()
    },
    //JIKA ADA DATA PENCARIAN
    handleSearch(val) {
      this.search = val //SET VALUE PENCARIAN KE VARIABLE SEARCH
      this.loadPostsData() //REQUEST DATA BARU
    },
    //JIKA ADA EMIT SORT
    handleSort(val) {
      if (val.sortBy) {
        this.sortBy = val.sortBy
        this.sortByDesc = val.sortDesc
        this.loadPostsData() //DAN LOAD DATA BARU BERDASARKAN SORT
      }
    },
  }
}
</script>
