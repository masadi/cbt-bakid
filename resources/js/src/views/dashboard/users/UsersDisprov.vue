<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Data Pengguna Cabang Dinas</h2>
      </b-card-title>
      <div class="heading-elements">
        <b-button variant="success" v-b-modal.add-user>Tambah Data</b-button>
      </div>
    </b-card-header>
    <b-card-body>
      <datatable :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" :title="'Hapus Sekolah'" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" />
    </b-card-body>
    <b-modal id="add-user" ref="modal" size="lg" title="Tambah Data Pengguna Cabang Dinas" @show="resetModal" @hidden="resetModal" @ok="handleOk">
      <b-tabs content-class="mt-1">
        <b-tab title="Manual" active>
          <form ref="form" @submit.stop.prevent="handleSubmit">
            <b-form-group label="Nama Lengkap" label-for="name" :invalid-feedback="name_feedback" :state="name_state">
              <b-form-input id="name" v-model="name" :state="name_state"></b-form-input>
            </b-form-group>
            <b-form-group label="Email" label-for="email" :invalid-feedback="email_feedback" :state="email_state">
              <b-form-input id="email" v-model="email" :state="email_state"></b-form-input>
            </b-form-group>
            <b-form-group label="Kabupaten/Kota" v-slot="{ ariaDescribedby }">
              <b-form-checkbox v-for="option in data_wilayah" v-model="kode_wilayah" :key="option.kode_wilayah" :value="option.kode_wilayah" :aria-describedby="ariaDescribedby" :state="kode_wilayah_state" name="flavour-3a" class="mb-1">
                {{ option.nama }}
              </b-form-checkbox>
              <b-form-invalid-feedback :state="kode_wilayah_state">{{kode_wilayah_feedback}}</b-form-invalid-feedback>
            </b-form-group>
            <b-form-group label="Password" label-for="password" :invalid-feedback="password_feedback" :state="password_state">
              <b-form-input id="password" v-model="password" :state="password_state"></b-form-input>
            </b-form-group>
          </form>
        </b-tab>
        <b-tab title="Import">
          <b-row>
            <b-col>
              <b-form-file v-model="file1" :state="Boolean(file1)" placeholder="Upload file excel..." drop-placeholder="Drop file here..."></b-form-file>
            </b-col>
            <b-col>
              <b-button variant="primary" href="/downloads/template-pengguna.xlsx" target="_blank">Unduh Template</b-button>
            </b-col>
          </b-row>
        </b-tab>
      </b-tabs>
    </b-modal>
  </b-card>
</template>

<script>
import _ from 'lodash'
import { BCard, BCardHeader, BCardTitle, BCardBody, BSpinner, BFormGroup, BFormInput, BFormSelect, BFormCheckboxGroup, BFormCheckbox, BFormInvalidFeedback, BButton, BTabs, BTab, BFormFile, BRow, BCol } from 'bootstrap-vue'

import Datatable from './TableUsersDisprov.vue'
//import UnduhanChangelog from './UnduhanChangelog.vue'

/* eslint-disable global-require */
export default {
  components: {
    Datatable,
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BSpinner,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BFormCheckboxGroup,
    BFormCheckbox,
    BFormInvalidFeedback,
    BButton,
    BTabs, 
    BTab,
    BFormFile,
    BRow, 
    BCol
  },
  data() {
    return {
      file1: null,
      name : '',
      name_state: null,
      name_feedback: '',
      email: '',
      email_state: null,
      email_feedback: '',
      kode_wilayah: [],
      kode_wilayah_state: null,
      kode_wilayah_feedback: '',
      password: '',
      password_state: null,
      password_feedback: '',
      isBusy: true,
      fields: [
        {
          key: 'name',
          label: 'Nama Lengkap',
          sortable: true
        },
        {
          key: 'email',
          label: 'Email',
          sortable: false,
          class: 'text-center',
          thClass: 'text-center',
        },
        {
          key: 'kabupaten',
          label: 'Kabupaten/Kota',
          sortable: false
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
      sortBy: 'name', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: false, //ASCEDING
      data_wilayah: [],
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    addUser(){
      //
    },
    resetModal() {
      this.name = ''
      this.name_state = null
      this.name_feedback = ''
      this.email = ''
      this.email_state = null
      this.email_feedback = ''
      this.kode_wilayah = []
      this.kode_wilayah_state = null
      this.kode_wilayah_feedback = ''
      this.password = ''
      this.password_state = null
      this.password_feedback = ''
    },
    handleOk(bvModalEvent) {
      // Prevent modal from closing
      bvModalEvent.preventDefault()
      // Trigger submit handler
      this.handleSubmit()
    },
    handleSubmit() {
      this.$http.post('/referensi/users/add', {
        name: this.name,
        email: this.email,
        wilayah_kcd: this.kode_wilayah,
        password: this.password,
      }).then(response => {
        let data = response.data
        if(data.errors){
          this.name_state = (data.errors.name) ? false : null
          this.name_feedback = (data.errors.name) ? data.errors.name[0] : ''
          this.email_state = (data.errors.email) ? false : null
          this.email_feedback = (data.errors.email) ? data.errors.email[0] : ''
          this.kode_wilayah_state = (data.errors.kode_wilayah) ? false : null
          this.kode_wilayah_feedback = (data.errors.kode_wilayah) ? data.errors.kode_wilayah[0] : ''
          this.password_state = (data.errors.password) ? false : null
          this.password_feedback = (data.errors.password) ? data.errors.password[0] : ''
        } else {
          this.$swal({
            icon: data.icon,
            title: data.title,
            text: data.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.$bvModal.hide('add-user')
            this.loadPostsData();
          })
        }
      })
    },
    loadPostsData() {
      //let current_page = this.search == '' ? this.current_page : this.current_page != 1 ? 1 : this.current_page
      let current_page = this.current_page//this.search == '' ? this.current_page : 1
      //LAKUKAN REQUEST KE API UNTUK MENGAMBIL DATA POSTINGAN
      this.$http.get('/referensi/users', {
        params: {
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
        this.data_wilayah = response.data.data_wilayah
        console.log(this.data_wilayah);
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