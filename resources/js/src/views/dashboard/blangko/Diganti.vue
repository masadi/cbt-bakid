<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Data Blangko Ijazah Diganti</h2>
      </b-card-title>
      <!--div class="heading-elements">
        <b-button variant="danger" v-b-modal.modal-prevent-closing>Tambah Data</b-button>
      </div-->
    </b-card-header>
    <b-card-body>
      <table-ijazah :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" :title="'Hapus Sekolah'" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" />
    </b-card-body>
    <!--b-modal id="modal-prevent-closing" ref="modal" title="Tambah Data Penggantian Blangko Ijazah" @show="resetModal" @hidden="resetModal" @ok="handleOk">
      <form ref="form" @submit.stop.prevent="handleSubmit">
        <b-form-group label="Nomor Ijazah Lama" label-for="nomor_seri" :invalid-feedback="feedback_nomor_seri" :state="nomor_seriState">
          <b-form-input id="nomor_seri" placeholder="M-SMK/K13-x/23/xxxxxxx" v-model="form.nomor_seri" :state="nomor_seriState" required></b-form-input>
        </b-form-group>
        <b-form-group label="Nomor Ijazah Baru" label-for="nomor_seri_baru" :invalid-feedback="feedback_nomor_seri_baru" :state="nomor_seri_baruState">
          <b-form-input id="nomor_seri" placeholder="M-SMK/K13-x/23/xxxxxxx" v-model="form.nomor_seri_baru" :state="nomor_seri_baruState" required></b-form-input>
        </b-form-group>
        <b-form-group label="Jenis Pengembalian" label-for="jenis_pengembalian_id" :invalid-feedback="feedback_jenis_pengembalian_id" :state="jenis_pengembalian_id_baruState">
          <b-form-select id="jenis_pengembalian_id" v-model="form.jenis_pengembalian_id" :options="jenis_pengembalian"></b-form-select>
        </b-form-group>
        <b-form-group label="Keterangan" label-for="keterangan" :invalid-feedback="feedback_keterangan" :state="keteranganState">
          <b-form-input id="keterangan" v-model="form.keterangan" :state="keteranganState"></b-form-input>
        </b-form-group>
      </form>
    </b-modal-->
  </b-card>
</template>

<script>
import _ from 'lodash'
import { BCard, BCardHeader, BCardTitle, BCardBody, BSpinner, BButton, BForm, BFormGroup, BFormInput, BFormSelect } from 'bootstrap-vue'

import TableIjazah from './IjazahDiganti.vue'
//import UnduhanChangelog from './UnduhanChangelog.vue'

/* eslint-disable global-require */
export default {
  components: {
    TableIjazah,
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BSpinner,
    BButton,
    BForm,
    BFormGroup, 
    BFormInput,
    BFormSelect
  },
  data() {
    return {
      jenis_pengembalian: [
      { value: null, text: '== Pilih Jenis Pengembalian ==' },
      { value: 'rusak', text: 'Rusak' },
      { value: 'kesalahan-cetakan', text: 'Kesalahan Cetakan' },
      { value: 'kesalahan-penulisan', text: 'Kesalahan Penulisan' },
      { value: 'hilang', text: 'Hilang' },
      { value: 'lainnya', text: 'Lainnya' },
      ],
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
          key: 'nomor_seri',
          label: 'Nomor Ijazah Lama',
          sortable: true
        },
        {
          key: 'nomor_seri_baru',
          label: 'Nomor Ijazah Baru',
          sortable: true
        },
        {
          key: 'keterangan',
          label: 'Keterangan',
          sortable: false,
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
      sortBy: 'nomor_seri', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: false, //ASCEDING
      form: {
        nomor_seri: '',
        nomor_seri_baru: '',
        jenis_pengembalian_id: null,
        keterangan: '',
      },
      feedback_jenis_pengembalian_id: '',
      feedback_nomor_seri: 'Nomor Ijazah Lama tidak boleh kosong!',
      feedback_nomor_seri_baru: 'Nomor Ijazah Baru tidak boleh kosong!',
      feedback_keterangan: 'Keterangan tidak boleh kosong!',
      nomor_seriState: null,
      nomor_seri_baruState: null,
      jenis_pengembalian_id_baruState: null,
      keteranganState: null,
      kode_wilayah: null,
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    baseName(str){
      var base = new String(str).substring(str.lastIndexOf('/') + 1); 
        if(base.lastIndexOf(".") != -1)       
            base = base.substring(0, base.lastIndexOf("."));
      return base;
    },
    resetModal() {
      this.form.nomor_seri = ''
      this.form.nomor_seri_baru = ''
      this.nomor_seriState = null
      this.nomor_seri_baruState = null
      this.form.keterangan = ''
      this.keteranganState = null
    },
    handleOk(bvModalEvent) {
      // Prevent modal from closing
      bvModalEvent.preventDefault()
      // Trigger submit handler
      this.handleSubmit()
    },
    handleSubmit() {
      this.$http.post('/blangko/simpan', {
        permintaan: 'ganti',
        nomor_seri: this.form.nomor_seri,
        nomor_seri_baru: this.form.nomor_seri_baru,
        start: parseInt(this.baseName(this.form.nomor_seri_baru)),
        jenis_pengembalian_id: this.form.jenis_pengembalian_id, 
        keterangan: this.form.keterangan,
        sekolah_id: this.user.sekolah_id,
        kode_wilayah: this.kode_wilayah,
      }).then(response => {
        let data = response.data
        if(data.errors){
          this.nomor_seriState = (data.errors.nomor_seri) ? false : true
          this.nomor_seri_baruState = (data.errors.nomor_seri_baru) ? false : true
          this.jenis_pengembalian_id_baruState = (data.errors.jenis_pengembalian_id) ? false : true
          this.feedback_nomor_seri = (data.errors.nomor_seri) ? data.errors.nomor_seri[0] : null
          this.feedback_nomor_seri_baru = (data.errors.nomor_seri_baru) ? data.errors.nomor_seri_baru[0] : null
          this.feedback_jenis_pengembalian_id = (data.errors.jenis_pengembalian_id) ? data.errors.jenis_pengembalian_id[0] : null
        } else {
          this.$swal({
            icon: data.icon,
            title: data.title,
            text: data.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.$bvModal.hide('modal-prevent-closing')
            this.loadPostsData()
          })
        }
      })
    },
    loadPostsData() {
      //let current_page = this.search == '' ? this.current_page : this.current_page != 1 ? 1 : this.current_page
      let current_page = this.current_page//this.search == '' ? this.current_page : 1
      //LAKUKAN REQUEST KE API UNTUK MENGAMBIL DATA POSTINGAN
      this.$http.get('/blangko', {
        params: {
          status: 3,
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
        this.kode_wilayah = (response.data.sekolah) ? response.data.sekolah.provinsi_id : null
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