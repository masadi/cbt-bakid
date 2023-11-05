<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Layanan Pengaduan</h2>
      </b-card-title>
      <div class="heading-elements">
        <b-button variant="success" v-b-modal.modal-pengaduan>Kirim Pengaduan</b-button>
      </div>
    </b-card-header>
    <b-card-body>
      <datatable :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" />
    </b-card-body>
    <b-modal id="modal-pengaduan" size="lg" title="Kirim Pengaduan" @show="showModal" @hidden="resetModal" @ok="handleOk" ok-title="Kirim" cancel-title="Batal">
      <form ref="form" @submit.stop.prevent="handleSubmit">
        <b-form-group label="Kategori" label-for="kategori_id" :invalid-feedback="KategoriFeedback" :state="KategoriState">
          <b-form-select id="kategori_id" v-model="kategori_id" :options="data_kategori" value-field="id" text-field="title" :state="KategoriState"></b-form-select>
        </b-form-group>
        <b-form-group label="Judul" label-for="judul" :invalid-feedback="JudulFeedback" :state="JudulState">
          <b-form-input id="judul" v-model="judul" :state="JudulState"></b-form-input>
        </b-form-group>
        <b-form-group label="Deskripsi" label-for="deskripsi" :invalid-feedback="DeskripsiFeedback" :state="DeskripsiState">
          <b-form-textarea id="deskripsi" v-model="deskripsi" :state="DeskripsiState" placeholder="Tuliskan pengaduan disini..." rows="3" max-rows="6"></b-form-textarea>
        </b-form-group>
      </form>
    </b-modal>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody, BButton, BModal, BFormGroup, BFormSelect, BFormInput, BFormTextarea } from 'bootstrap-vue'
import Datatable from './TablePengaduan.vue' //IMPORT COMPONENT DATATABLENYA
export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    Datatable,
    BButton,
    BModal,
    BFormGroup,
    BFormSelect,
    BFormInput,
    BFormTextarea
  },
  data() {
    return {
      //form start
      data_kategori: [],
      kategori_id: '',
      judul: '',
      deskripsi: '',
      JudulFeedback: '',
      JudulState: null,
      KategoriFeedback: '',
      KategoriState: null,
      DeskripsiFeedback: '',
      DeskripsiState: null,
      //form end
      isBusy: true,
      fields: [{
        key: 'judul',
        label: 'Judul Pengaduan',
        sortable: true,
        thClass: 'text-center',
      },
      {
        key: 'user',
        label: 'Nama Pengguna',
        sortable: false,
        thClass: 'text-center',
      },
      {
        key: 'kategori',
        label: 'Kategori',
        sortable: true,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'status',
        label: 'Status',
        sortable: true,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'actions',
        label: 'Detil',
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
    showModal(){
      this.getKategori()
      this.resetModal()
    },
    getKategori(){
      this.$http.get('/pengaduan/kategori').then(response => {
        this.data_kategori = response.data
      }).catch(error => {
        console.log(error);
      })
    },
    resetModal() {
      this.kategori_id = ''
      this.judul = ''
      this.deskripsi = '',
      this.KategoriState = null
      this.KategoriFeedback = ''
      this.JudulState = null
      this.JudulFeedback = ''
      this.deskripsi = '',
      this.DeskripsiState = null,
      this.DeskripsiFeedback = ''
    },
    handleOk(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit() {
      this.$http.post('/pengaduan/tambah', {
        kategori_id: this.kategori_id,
        judul: this.judul,
        deskripsi: this.deskripsi
      }).then(response => {
        let getData = response.data
        if(getData.errors){
          this.KategoriState = (getData.errors.kategori_id) ? false : null
          this.KategoriFeedback = (getData.errors.kategori_id) ? getData.errors.kategori_id[0] : ''
          this.JudulState = (getData.errors.judul) ? false : null
          this.JudulFeedback = (getData.errors.judul) ? getData.errors.judul[0] : ''
          this.DeskripsiState = (getData.errors.deskripsi) ? false : null
          this.DeskripsiFeedback = (getData.errors.deskripsi) ? getData.errors.deskripsi[0] : ''
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            html: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.$nextTick(() => {
              this.$bvModal.hide('modal-pengaduan')
              this.loadPostsData()
            })
          })
        }
      }).catch(error => {
        console.log(error);
      })
    },
    loadPostsData() {
      //let current_page = this.search == '' ? this.current_page : this.current_page != 1 ? 1 : this.current_page
      let current_page = this.current_page//this.search == '' ? this.current_page : 1
      //LAKUKAN REQUEST KE API UNTUK MENGAMBIL DATA POSTINGAN
      this.$http.get('/pengaduan', {
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
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>