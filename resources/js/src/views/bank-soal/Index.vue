<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Data Ujian</h2>
      </b-card-title>
      <div class="heading-elements">
        <b-button variant="primary" size="sm" v-b-modal.upload-soal>Tambah Data</b-button>
      </div>
    </b-card-header>
    <b-card-body>
      <datatable :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" @filter_rombel="handleRombel" @filter_mapel="handleMapel" />
    </b-card-body>
    <b-modal id="upload-soal" title="Unggah Soal">
      <template #modal-footer="{ ok, cancel }">
        <b-button variant="danger" @click="cancel()" v-if="!loading">
          Tutup
        </b-button>
        <b-button variant="success" @click="handleOkUpload" v-if="!loading">
          Unggah
        </b-button>
        <b-button variant="success" disabled v-if="loading">
          <b-spinner small></b-spinner>
          <span class="sr-only">Loading...</span>
        </b-button>
      </template>
      <form ref="form" @submit.stop.prevent="handleSubmitUpload">
        <b-form-group label="Nama Mata Ujian" label-for="nama" :invalid-feedback="feedback_nama" :state="namaState">
          <b-form-input id="nama" v-model="nama" :state="namaState"></b-form-input>
        </b-form-group>
        <b-form-group label="Mata Pelajaran" label-for="pembelajaran_id" :invalid-feedback="feedback_pembelajaran_id" :state="pembelajaran_idState">
          <b-form-select v-model="pembelajaran_id" :options="options" :state="pembelajaran_idState"></b-form-select>
        </b-form-group>
        <b-form-group label="Berkas ZIP" label-for="file_zip" :state="fileState">
          <b-form-file v-model="file_zip" :state="fileState" placeholder="Unggah berkas ZIP disini..." drop-placeholder="Unggah berkas ZIP disini..." @change="onFileChange"></b-form-file>
          <p v-show="feedback_file" v-html="feedback_file" class="text-danger"></p>
        </b-form-group>
      </form>
    </b-modal>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody, BButton, BFormFile, BSpinner, BFormGroup, BFormSelect, BFormInput } from 'bootstrap-vue'
import Datatable from './Datatable.vue' //IMPORT COMPONENT DATATABLENYA
export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BButton,
    BFormFile,
    BSpinner,
    BFormGroup, 
    BFormSelect,
    BFormInput,
    Datatable
  },
  data() {
    return {
      loading: false,
      options: [],
      feedback_nama: null,
      namaState: null,
      nama: '',
      feedback_pembelajaran_id: null,
      pembelajaran_idState: null,
      pembelajaran_id : '',
      file_zip: null,
      fileState: null,
      feedback_file: '',
      isBusy: true,
      fields: [
        {
          key: 'nama',
          label: 'Nama Mata Ujian',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'mapel',
          label: 'Mata Pelajaran',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'kelas',
          label: 'Kelas',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
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
      rombel_id: '',
      mapel_id: '',
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    loadPostsData() {
      this.isBusy = true
      //let current_page = this.search == '' ? this.current_page : this.current_page != 1 ? 1 : this.current_page
      let current_page = this.current_page//this.search == '' ? this.current_page : 1
      //LAKUKAN REQUEST KE API UNTUK MENGAMBIL DATA POSTINGAN
      this.$http.get('/bank-soal', {
        params: {
          rombel_id: this.rombel_id,
          mapel_id: this.mapel_id,
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
        let pembelajaran = response.data.pembelajaran
        let set_option = [];
        pembelajaran.forEach((item, index) => {
          set_option.push({
            value: item.pembelajaran_id,
            text: item.nama_mata_pelajaran+' ('+item.rombongan_belajar.nama+')'
          })
        })
        this.options = set_option
        this.meta = {
          total: getData.total,
          current_page: getData.current_page,
          per_page: getData.per_page,
          from: getData.from,
          to: getData.to,
          data_rombel: [],
          data_pembelajaran: this.options
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
    onFileChange(e) {
      this.file_zip = e.target.files[0];
    },
    handleOkUpload(bvModalEvent) {
      // Prevent modal from closing
      bvModalEvent.preventDefault()
      // Trigger submit handler
      this.handleSubmitUpload()
    },
    handleSubmitUpload() {
      this.loading = true
      const data = new FormData();
      data.append('nama', this.nama);
      data.append('file_zip', this.file_zip);
      data.append('pembelajaran_id', this.pembelajaran_id);
      data.append('user_id', this.user.user_id)
      var ini = this
      this.$http.post('/bank-soal/upload', data).then(function (response) {
        ini.loading = false
        let getData = response.data
        ini.namaState = null
        ini.feedback_nama = ''
        ini.fileState = null
        ini.feedback_file = ''
        ini.pembelajaran_idState = null
        ini.feedback_pembelajaran_id = ''
        if(getData.errors){
          ini.namaState = (getData.errors.nama) ? false : null
          ini.feedback_nama = (getData.errors.nama) ? getData.errors.nama.join('<br>') : ''
          ini.fileState = (getData.errors.file_zip) ? false : null
          ini.feedback_file = (getData.errors.file_zip) ? getData.errors.file_zip.join('<br>') : ''
          ini.pembelajaran_idState = (getData.errors.pembelajaran_id) ? false : null
          ini.feedback_pembelajaran_id = (getData.errors.pembelajaran_id) ? getData.errors.pembelajaran_id.join('<br>') : ''
        } else {
          ini.$swal({
            icon: getData.icon,
            title: getData.title,
            html: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            ini.$bvModal.hide('upload-soal')
            ini.loadPostsData()
          })
        }
      }).catch(function (error) {
        console.log(error);
        ini.fileState = false
        ini.feedback_file = 'Isian salah. Silahkan periksa kembali!!!'
      })
    },
    handleRombel(val){
      this.rombel_id = val //SET CURRENT PAGE YANG AKTIF
      this.loadPostsData()
    },
    handleMapel(val){
      this.mapel_id = val //SET CURRENT PAGE YANG AKTIF
      this.loadPostsData()
    },
  },
}
</script>