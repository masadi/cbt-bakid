<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Data Blangko Ijazah Cadangan</h2>
      </b-card-title>
      <div class="heading-elements">
        <b-button variant="success" @click="konfirmasi">Konfirmasi Lengkap</b-button>
      </div>
    </b-card-header>
    <b-card-body>
      <!--section v-if="hasRole('administrator')">
        <blanko-administrator />
      </section>
      <section v-if="hasRole('direktorat')">
        <blanko-direktorat />
      </section>
      <section v-if="hasRole('disprov')">
        <blanko-disprov />
      </section>
      <section v-if="hasRole('diskab')">
        <blanko-diskab />
      </section>
      <section v-if="hasRole('sekolah')">
        <blanko-sekolah />
      </section>
      <datatable :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" :title="'Hapus Sekolah'" :optionsKabupaten="optionsKabupaten" :optionSekolah="optionSekolah" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" @status="handleStatus" @program="handleProgram" @sekolah_id="handleSekolah" @kabupaten_id="handleKabupaten" /-->
      <section v-if="hasRole('sekolah')">
        <datatable :isBusy="isBusy" :items="items" :fields="fields_sekolah" :meta="meta" :title="'Hapus Sekolah'" :optionsKabupaten="optionsKabupaten" :optionSekolah="optionSekolah" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" @status="handleStatus" @program="handleProgram" @sekolah_id="handleSekolah" @kabupaten_id="handleKabupaten" :cadangan="false" />
      </section>
      <section v-else>
        <datatable :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" :title="'Hapus Sekolah'" :optionsKabupaten="optionsKabupaten" :optionSekolah="optionSekolah" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" @status="handleStatus" @program="handleProgram" @sekolah_id="handleSekolah" @kabupaten_id="handleKabupaten" :cadangan="true" />
      </section>
    </b-card-body>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody, BButton } from 'bootstrap-vue'
/*import BlankoAdministrator from './BlankoAdministrator.vue'
import BlankoDirektorat from './BlankoDirektorat.vue'
import BlankoDisprov from './BlankoDisprov.vue'
import BlankoDiskab from './BlankoDiskab.vue'
import BlankoSekolah from './BlankoSekolah.vue'*/
import Datatable from './TableBlangko.vue' //IMPORT COMPONENT DATATABLENYA

export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BButton,
    /*BlankoAdministrator,
    BlankoDirektorat,
    BlankoDisprov,
    BlankoDiskab,
    BlankoSekolah,*/
    Datatable,
  },
  data() {
    return {
      isBusy: true,
      fields_sekolah: [
        {
          key: 'tahun',
          label: 'Program',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'nomor_seri',
          label: 'Nomor Ijazah',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'confirm',
          label: 'Status Konfirmasi',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'status_confirm',
          label: 'Keterangan',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'actions',
          label: 'Konfirmasi',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
      ],
      fields: [{
        key: 'tahun',
        label: 'Program',
        sortable: true,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'nomor_seri',
        label: 'Nomor Ijazah',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'sekolah',
        label: 'Sekolah',
        sortable: false,
        thClass: 'text-center',
      },
      {
        key: 'confirm',
        label: 'Status Konfirmasi',
        sortable: true,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'status_confirm',
        label: 'Keterangan',
        sortable: true,
        thClass: 'text-center',
        tdClass: 'text-center'
      },
      {
        key: 'actions',
        label: 'Konfirmasi',
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
      status: 'all',
      program: '',
      sekolah_id: '',
      kabupaten_id: '',
      optionsKabupaten: [],
      optionSekolah: [],
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    konfirmasi(){
      this.$swal({
        title: 'Apakah Anda yakin?',
        text: "Aksi ini akan mengkonfirmasi semua Blangko Ijazah Diterima telah dinyatakan lengkap!",
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
          this.$http.post('/blangko/confirm', {
            status: 1,
          }).then(response => {
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
    },
    loadPostsData() {
      //let current_page = this.search == '' ? this.current_page : this.current_page != 1 ? 1 : this.current_page
      let current_page = this.current_page//this.search == '' ? this.current_page : 1
      //LAKUKAN REQUEST KE API UNTUK MENGAMBIL DATA POSTINGAN
      this.$http.get('/blangko/cadangan', {
        params: {
          tahun_ajaran_id: 2022,
          page: current_page,
          per_page: this.per_page,
          q: this.search,
          sortby: this.sortBy,
          sortbydesc: this.sortByDesc ? 'DESC' : 'ASC',
          status: this.status,
          program: this.program,
          sekolah_id: this.sekolah_id,
          kabupaten_id: this.kabupaten_id,
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
        this.optionsKabupaten = response.data.wilayah
        this.optionSekolah = response.data.sekolah
      })
    },
    //JIKA ADA EMIT TERKAIT LOAD PERPAGE, MAKA FUNGSI INI AKAN DIJALANKAN
    handlePerPage(val) {
      this.per_page = val //SET PER_PAGE DENGAN VALUE YANG DIKIRIM DARI EMIT
      this.loadPostsData() //DAN REQUEST DATA BARU KE SERVER
    },
    handleStatus(val){
      this.status = val //SET PER_PAGE DENGAN VALUE YANG DIKIRIM DARI EMIT
      this.loadPostsData() //DAN REQUEST DATA BARU KE SERVER
    },
    handleProgram(val){
      this.program = val //SET PER_PAGE DENGAN VALUE YANG DIKIRIM DARI EMIT
      this.loadPostsData() //DAN REQUEST DATA BARU KE SERVER
    },
    handleSekolah(val){
      this.sekolah_id = val //SET PER_PAGE DENGAN VALUE YANG DIKIRIM DARI EMIT
      this.loadPostsData() //DAN REQUEST DATA BARU KE SERVER
    },
    handleKabupaten(val){
      this.kabupaten_id = val //SET PER_PAGE DENGAN VALUE YANG DIKIRIM DARI EMIT
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
@import '~@resources/scss/vue/pages/dashboard-ecommerce.scss';
@import '~@resources/scss/vue/libs/chart-apex.scss';
</style>
