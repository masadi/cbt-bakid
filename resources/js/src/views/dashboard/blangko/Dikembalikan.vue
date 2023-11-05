<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Data Blangko Ijazah Dikembalikan</h2>
      </b-card-title>
      <!--div class="heading-elements">
        <b-button variant="danger" v-b-modal.modal-prevent-closing>Tambah Data</b-button>
      </div-->
    </b-card-header>
    <b-card-body>
      <table-ijazah :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" :title="'Hapus Sekolah'" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" />
    </b-card-body>
    <b-modal id="modal-prevent-closing" ref="modal" title="Tambah Data Pengembalian Blangko Ijazah" @show="resetModal" @hidden="resetModal" @ok="handleOk">
      <form ref="form" @submit.stop.prevent="handleSubmit">
        <b-form-group label="Nomor Ijazah" label-for="nomor_seri" :invalid-feedback="feedback" :state="nameState">
          <b-form-input id="nomor_seri" v-model="form.nomor_seri" placeholder="M-SMK/K13-x/23/xxxxxxx" :state="nameState" required></b-form-input>
        </b-form-group>
        <b-form-group label="Keterangan" label-for="keterangan" :invalid-feedback="feedback_keterangan" :state="keteranganState">
          <b-form-input id="keterangan" v-model="form.keterangan" :state="keteranganState" required></b-form-input>
        </b-form-group>
      </form>
    </b-modal>
  </b-card>
</template>

<script>
import _ from 'lodash'
import { BCard, BCardHeader, BCardTitle, BCardBody, BSpinner, BButton, BForm, BFormGroup, BFormInput } from 'bootstrap-vue'

import TableIjazah from './IjazahDikembalikan.vue'
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
    BFormInput
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
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'nomor_seri',
          label: 'Nomor Ijazah',
          sortable: true,
          thClass: 'text-center'
        },
        {
          key: 'keterangan',
          label: 'Keterangan',
          sortable: false,
          thClass: 'text-center'
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
      sortBy: 'nomor_seri', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: false, //ASCEDING
      form: {
        nomor_seri: '',
        keterangan: '',
      },
      feedback: 'Nomor Ijazah tidak boleh kosong!',
      feedback_keterangan: 'Keterangan tidak boleh kosong!',
      nameState: null,
      keteranganState: null,
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    checkFormValidity() {
      const valid = this.$refs.form.checkValidity()
      this.nameState = valid
      this.keteranganState = valid
      return valid
    },
    resetModal() {
      this.form.nomor_seri = ''
      this.form.keterangan = ''
      this.nameState = null
    },
    handleOk(bvModalEvent) {
      // Prevent modal from closing
      bvModalEvent.preventDefault()
      // Trigger submit handler
      this.handleSubmit()
    },
    handleSubmit() {
      // Exit when the form isn't valid
      if (!this.checkFormValidity()) {
        return
      }
      // Hide the modal manually
      this.$http.post('/blangko/simpan', {
        permintaan: 'kembali',
        nomor_seri: this.form.nomor_seri,
        sekolah_id: this.user.sekolah_id,
        keterangan: this.form.keterangan,
      }).then(response => {
        let data = response.data
        if(data.errors){
          this.nameState = false
          this.feedback = data.errors.nomor_seri[0]
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
          status: 2,
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
  },
}
/* eslint-disable global-require */
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>