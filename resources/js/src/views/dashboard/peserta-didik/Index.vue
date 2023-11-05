<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Data Peserta Didik Aktif</h2>
      </b-card-title>
      <div class="heading-elements">
        <!--b-button variant="danger" v-b-modal.ambilDapo>Ambil Data Dapodik</b-button-->
        <b-button variant="success" @click="luluskan">Atur Penerima</b-button>
      </div>
    </b-card-header>
    <b-card-body>
      <table-peserta-didik :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" :title="'Hapus Sekolah'" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" />
    </b-card-body>
    <b-modal id="ambilDapo" title="Ambil Data Peserta Didik" no-close-on-esc no-close-on-backdrop>
      <p v-show="show">Ambil Data Peserta Didik</p>
      <b-alert v-show="alert" show variant="danger">Pengambilan data Dapodik gagal. Silahkan coba beberapa saat lagi!</b-alert>
      <b-progress v-show="!show" height="2rem" :value="value" :max="max" show-progress animated></b-progress>
      <template #modal-footer="{ ok, cancel }">
        <b-button size="sm" variant="danger" v-show="show" @click="cancel()">Batal</b-button>
        <b-button size="sm" variant="success" v-show="show" @click="ambilDapo()">Ambil Data</b-button>
        <b-spinner label="Loading..." v-show="!show"></b-spinner>
    </template>
    </b-modal>
  </b-card>
</template>

<script>
import _ from 'lodash'
import { BCard, BCardHeader, BCardTitle, BCardBody, BButton, BModal, BSpinner, BProgress, BAlert } from 'bootstrap-vue'

import TablePesertaDidik from './TablePesertaDidik.vue'
//import UnduhanChangelog from './UnduhanChangelog.vue'

/* eslint-disable global-require */
export default {
  components: {
    TablePesertaDidik,
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BButton,
    BModal,
    BSpinner,
    BProgress,
    BAlert,
  },
  data() {
    return {
      alert: false,
      value: 0,
      max: 100,
      show: true,
      isBusy: true,
      fields: [
        {
          key: 'no',
          label: 'NO',
          sortable: false
        },
        {
          key: 'nama',
          label: 'Nama Lengkap',
          sortable: false
        },
        {
          key: 'no_induk',
          label: 'NIS/NISN',
          sortable: false,
          class: 'text-center',
          thClass: 'text-center',
        },
        {
          key: 'ttl',
          label: 'Tempat, Tanggal Lahir',
          sortable: false
        },
        {
          key: 'nama_ayah',
          label: 'Nama Orang Tua',
          sortable: false
        },
        {
          key: 'kurikulum',
          label: 'Kurikulum',
          sortable: false,
          class: 'text-center',
          thClass: 'text-center',
        },
        {
          key: 'kompetensi',
          label: 'Kompetensi',
          sortable: false,
          thClass: 'text-center',
        },
        {
          key: 'actions',
          label: 'Aksi',
          sortable: false
        },
      ],
      items: [],
      meta: {},
      current_page: 1, //DEFAULT PAGE YANG AKTIF ADA PAGE 1
      per_page: 10, //DEFAULT LOAD PERPAGE ADALAH 10
      search: '',
      sortBy: 'nama', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: false, //ASCEDING
      polling: null,
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    pollData () {
      this.polling = setInterval(() => {
        this.$http.get('/referensi/hitung-pd', {
          params: {
            sekolah_id: this.user.sekolah_id,
          }
        }).then(response => {
          this.value = response.data.persentase
        });
      }, 1000)
    },
    ambilDapo(){
      this.show = false
      this.alert = false
      this.pollData()
      this.$http.post('/referensi/ambil-pd', {
        sekolah_id: this.user.sekolah_id,
      }).then(response => {
        if(response.data.successful){
          this.alert = false
          this.prosesDapo()
        } else {
          this.alert = true
          this.show = true
          clearInterval(this.polling)
        }
      });
    },
    prosesDapo(){
      this.$http.post('/referensi/proses-pd', {
        sekolah_id: this.user.sekolah_id,
        email: this.user.email,
        npsn: this.user.username,
        semester_id: 20221,
      }).then(response => {
        this.show = true
        this.value = 100
        clearInterval(this.polling)
        this.$nextTick(() => {
          this.$bvModal.hide('ambilDapo')
        })
        this.loadPostsData()
      });
    },
    loadPostsData() {
      //let current_page = this.search == '' ? this.current_page : this.current_page != 1 ? 1 : this.current_page
      let current_page = this.current_page//this.search == '' ? this.current_page : 1
      //LAKUKAN REQUEST KE API UNTUK MENGAMBIL DATA POSTINGAN
      this.$http.get('/referensi/pd', {
        params: {
          status: 'aktif',
          sekolah_id: this.user.sekolah_id,
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
    luluskan(){
      this.$swal({
            title: 'Apakah Anda yakin?',
            text: "Aksi ini tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yakin!',
            customClass: {
              confirmButton: 'btn btn-primary',
              cancelButton: 'btn btn-outline-danger ml-1',
            },
            buttonsStyling: false,
          }).then(result => {
            if (result.value) {
              this.isBusy = true
              this.$http.post('/referensi/luluskan', {
                sekolah_id: this.user.sekolah_id
              }).then(response => {
                this.isBusy = false
                let data = response.data
                this.$swal({
                  icon: data.icon,
                  title: data.title,
                  text: data.text,
                  customClass: {
                    confirmButton: 'btn btn-success',
                  },
                }).then(result => {
                  this.loadPostsData();
                })
              });
            }
          })
    }
  },
}
/* eslint-disable global-require */
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>