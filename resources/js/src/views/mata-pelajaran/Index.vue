<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Data Mata Pelajaran</h2>
      </b-card-title>
      <b-card-text class="mb-0">
        <feather-icon icon="PlusCircleIcon" size="21" class="text-success cursor-pointer" @click="openModal('add')"/>
      </b-card-text>
    </b-card-header>
    <b-card-body>
      <datatable :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" @reload="handleReload" @edit="handleEdit" />
    </b-card-body>
    <add-modal @reload="handleReload"></add-modal>
    <edit-modal @reload="handleReload"></edit-modal>
    <detil-modal></detil-modal>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardText, BCardBody } from 'bootstrap-vue'
import AddModal from './AddModal.vue'
import EditModal from './EditModal.vue'
import DetilModal from './DetilModal.vue'
import Datatable from './Datatable.vue' //IMPORT COMPONENT DATATABLENYA
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardText,
    BCardBody,
    Datatable,
    AddModal,
    EditModal,
    DetilModal,
  },
  data() {
    return {
      isBusy: true,
      fields: [
        {
          key: 'mata_pelajaran_id',
          label: 'ID',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'nama',
          label: 'Nama Mata Pelajaran',
          sortable: true,
          thClass: 'text-center',
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
      per_page: 25, //DEFAULT LOAD PERPAGE ADALAH 10
      search: '',
      sortBy: 'mata_pelajaran_id', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: false, //ASCEDING
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      //let current_page = this.search == '' ? this.current_page : this.current_page != 1 ? 1 : this.current_page
      let current_page = this.current_page//this.search == '' ? this.current_page : 1
      //LAKUKAN REQUEST KE API UNTUK MENGAMBIL DATA POSTINGAN
      this.$http.get('/mata-pelajaran', {
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
    openModal(modalId){
      eventBus.$emit('open-modal', {
        id: modalId,
      });
    },
    handleEdit(item){
      eventBus.$emit('open-modal', {
        id: 'edit',
        item: item,
      });
    },
  },
}
</script>