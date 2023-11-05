<template>
  <div>
    <pusdatin-rekap @status="handleStatus"/>
    <pusdatin-sp :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" />
  </div>
</template>

<script>
import PusdatinRekap from './PusdatinRekap.vue'
import PusdatinSp from './PusdatinSp.vue'

export default {
  components: {
    PusdatinRekap,
    PusdatinSp,
  },
  data() {
    return {
      isBusy: true,
      fields: [
        {
          key: 'nama',
          label: 'Nama',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'npsn',
          label: 'NPSN',
          sortable: true,
          tdClass: 'text-center',
          thClass: 'text-center',
        },
        {
          key: 'provinsi',
          label: 'Provinsi',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'kabupaten',
          label: 'Kabupaten',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'kecamatan',
          label: 'Kecamatan',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'program_3_tahun',
          label: 'PD 3 Tahun',
          sortable: true,
          tdClass: 'text-center',
          thClass: 'text-center',
        },
        {
          key: 'program_4_tahun',
          label: 'PD 4 Tahun',
          sortable: true,
          tdClass: 'text-center',
          thClass: 'text-center',
        },
        {
          key: 'total',
          label: 'Total PD',
          sortable: false,
          tdClass: 'text-center',
          thClass: 'text-center',
        }
      ],
      items: [],
      meta: {},
      current_page: 1, //DEFAULT PAGE YANG AKTIF ADA PAGE 1
      per_page: 10, //DEFAULT LOAD PERPAGE ADALAH 10
      status: '',
      search: '',
      sortBy: 'id', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: false, //ASCEDING
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    loadPostsData() {
      this.isBusy = true
      let current_page = this.current_page
      this.$http.get('/pusdatin/sp', {
        params: {
          status: this.status,
          semester_id: 20221,
          page: current_page,
          per_page: this.per_page,
          q: this.search,
          sortby: this.sortBy,
          sortbydesc: this.sortByDesc ? 'DESC' : 'ASC'
        }
      }).then(response => {
        let getData = response.data.data
        this.isBusy = false
        this.items = getData.data
        this.meta = {
          total: getData.total,
          current_page: getData.current_page,
          per_page: getData.per_page,
          from: getData.from,
          to: getData.to,
        }
      })
    },
    handlePerPage(val) {
      this.per_page = val
      this.loadPostsData()
    },
    handlePagination(val) {
      this.current_page = val
      this.loadPostsData()
    },
    handleSearch(val) {
      this.search = val
      this.loadPostsData()
    },
    handleSort(val) {
      if (val.sortBy) {
        this.sortBy = val.sortBy
        this.sortByDesc = val.sortDesc
        this.loadPostsData()
      }
    },
    handleStatus(val){
      this.status = val
      this.loadPostsData()
    }
  }
}
</script>