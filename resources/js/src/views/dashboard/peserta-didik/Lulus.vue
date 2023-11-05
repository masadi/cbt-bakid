<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Data Peserta Didik Penerima Ijazah</h2>
      </b-card-title>
      <div class="heading-elements" v-show="isLulus">
        <b-button variant="success" v-b-modal.modal-ijazah>Import Nomor Ijazah</b-button>
      </div>
    </b-card-header>
    <b-card-body>
      <table-peserta-didik :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" :title="'Hapus Sekolah'" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" />
    </b-card-body>
    <b-modal id="modal-ijazah" title="Import Nomor Ijazah" cancel-title="Tutup" ok-title="Upload" @ok="handleOk">
      <form ref="form" @submit.stop.prevent="handleSubmit">
        <b-button block variant="primary" class="mb-2" :href="'/downloads/template-excel/'+user.sekolah_id" target="_blank">Download Template Excel</b-button>
        <b-form-file v-model="file_excel" :state="fileState" placeholder="Upload file excel disini..." drop-placeholder="Upload file excel disini..." @change="onFileChange"></b-form-file>
        <p v-show="feedback_file" class="text-danger">{{feedback_file}}</p>
      </form>
    </b-modal>
  </b-card>
</template>

<script>
import _ from 'lodash'
import { BCard, BCardHeader, BCardTitle, BCardBody, BSpinner, BButton, BModal, BFormFile } from 'bootstrap-vue'

import TablePesertaDidik from './TablePesertaDidikLulus.vue'
//import UnduhanChangelog from './UnduhanChangelog.vue'

/* eslint-disable global-require */
export default {
  components: {
    TablePesertaDidik,
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BSpinner,
    BButton,
    BModal,
    BFormFile,
  },
  data() {
    return {
      fileState: null,
      isLulus: false,
      file_excel: null,
      feedback_file: '',
      alert: false,
      value: 0,
      max: 100,
      show: true,
      isBusy: true,
      fields: [
        {
          key: 'no',
          label: 'NO',
          sortable: false,
          tdClass: 'text-center',
          thClass: 'text-center',
        },
        {
          key: 'nama',
          label: 'Nama Lengkap',
          sortable: false,
          thClass: 'text-center',
        },
        {
          key: 'no_induk',
          label: 'NISN',
          sortable: false,
          tdClass: 'text-center',
          thClass: 'text-center',
        },
        /*{
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
        },*/
        {
          key: 'kurikulum',
          label: 'Kurikulum',
          sortable: false,
          class: 'text-center',
          thClass: 'text-center',
        },
        {
          key: 'ijazah',
          label: 'No Ijazah',
          sortable: false,
          thClass: 'text-center',
        },
        {
          key: 'tanggal',
          label: 'Tanggal Terbit',
          sortable: false,
          thClass: 'text-center',
        },
        {
          key: 'actions',
          label: 'Aksi',
          sortable: false,
          tdClass: 'text-center',
          thClass: 'text-center',
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
    onFileChange(e) {
      console.log(e.target.files[0]);
      //this.filename = "Selected File: " + e.target.files[0].name;
      this.file_excel = e.target.files[0];
      console.log(this.file_excel);
    },
    handleOk(bvModalEvent) {
      // Prevent modal from closing
      bvModalEvent.preventDefault()
      // Trigger submit handler
      this.handleSubmit()
    },
    handleSubmit() {
      const data = new FormData();
      data.append('file_excel', this.file_excel);
      var ini = this
      this.$http.post('/uploads/file-excel', data).then(function (response) {
        let data = response.data
        ini.fileState = null
        if(data.errors){
          ini.fileState = (data.errors.file_excel) ? false : null
          ini.feedback_file = (data.errors.file_excel) ? data.errors.file_excel[0] : ''
        } else {
          ini.$swal({
            icon: 'success',
            title: 'Berhasil',
            html: data.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            ini.$bvModal.hide('modal-ijazah')
            ini.loadPostsData()
          })
        }
      }).catch(function (error) {
        console.log(error);
        ini.fileState = false
        ini.feedback_file = 'Isian salah. Silahkan periksa kembali!!!'
      })
    },
    loadPostsData() {
      //let current_page = this.search == '' ? this.current_page : this.current_page != 1 ? 1 : this.current_page
      let current_page = this.current_page//this.search == '' ? this.current_page : 1
      //LAKUKAN REQUEST KE API UNTUK MENGAMBIL DATA POSTINGAN
      this.$http.get('/referensi/pd', {
        params: {
          status: 'lulus',
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
        if(getData.total){
          this.isLulus = true
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
  },
}
/* eslint-disable global-require */
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>