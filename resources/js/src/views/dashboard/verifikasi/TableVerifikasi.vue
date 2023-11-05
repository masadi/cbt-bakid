<template>
  <div>
    <b-row>
      <b-col md="4" class="mb-2">
        <div class="form-inline">
        <!-- KETIKA SELECT BOXNYA DIGANTI, MAKA AKAN MENJALANKAN FUNGSI loadPerPage -->
          <select class="form-control" v-model="meta.per_page" @change="loadPerPage">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <label class="ml-2">Entri</label>
        </div>
      </b-col>
      <b-col md="4" offset-md="4">
        <div class="form-inline float-right">
          <label class="mr-2">Cari</label>
          <!-- KETIKA ADA INPUTAN PADA KOLOM PENCARIAN, MAKA AKAN MENJALANKAN FUNGSI SEARCH -->
          <input type="text" class="form-control" @input="search">
        </div>
      </b-col>
    </b-row>
    {{meta.aksi}}
    <b-table responsive striped :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" show-empty :busy="isBusy">
      <template #table-busy>
        <div class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
      </template>
      <template v-slot:cell(sekolah)="row">
        {{row.item.sekolah.nama}}
      </template>
      <template v-slot:cell(tahun)="row">
        {{row.item.tahun}} Tahun
      </template>
      <template v-slot:cell(actions)="row">
        <b-button variant="primary" size="sm" @click="preview(row.item)" v-if="meta.aksi == 'verifikasi-blangko'"><font-awesome-icon icon="fa-solid fa-magnifying-glass" /> Lihat Berkas</b-button>
        <b-button variant="danger" size="sm" @click="batalkan(row.item)" v-else><font-awesome-icon icon="fa-solid fa-circle-xmark" /> Batalkan</b-button>
        <!--b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="success" size="sm">
          <b-dropdown-item href="javascript:" @click="verifikasi(row.item, 1)"><font-awesome-icon icon="fa-solid fa-file-circle-check" /> Valid</b-dropdown-item>
          <b-dropdown-item href="javascript:" @click="verifikasi(row.item, 0)"><font-awesome-icon icon="fa-solid fa-file-circle-xmark" /> Invalid</b-dropdown-item>
        </b-dropdown-->
      </template>
    </b-table>
    <b-row class="mt-2">
      <b-col md="6">
        <p>Menampilkan {{ (meta.from) ? meta.from : 0 }} sampai {{ meta.to }} dari {{ meta.total }} entri</p>
      </b-col>
      <!-- BLOCK INI AKAN MENJADI PAGINATION DARI DATA YANG DITAMPILKAN -->
      <b-col md="6">
        <!-- DAN KETIKA TERJADI PERGANTIAN PAGE, MAKA AKAN MENJALANKAN FUNGSI changePage -->
        <b-pagination v-model="meta.current_page" :total-rows="meta.total" :per-page="meta.per_page" align="right" @change="changePage" aria-controls="dw-datatable"></b-pagination>
      </b-col>
    </b-row>
    <b-modal ref="modal-preview" size="xl" title="Pratinjau" ok-title="Valid" ok-variant="success" cancel-title="Invalid" cancel-variant="danger" @ok="handleOk" @cancel="handleCancel">
      <iframe :src="previewUrl" width="100%" height="600"></iframe>
    </b-modal>
  </div>
</template>

<script>
import _ from 'lodash' //IMPORT LODASH, DIMANA AKAN DIGUNAKAN UNTUK MEMBUAT DELAY KETIKA KOLOM PENCARIAN DIISI
import { BRow, BCol, BTable, BSpinner, BPagination, BDropdown, BDropdownItem, BBadge, BAlert, BProgress, BButton } from 'bootstrap-vue'
export default {
  components: {
    BRow,
    BCol,
    BTable,
    BSpinner,
    BPagination,
    BDropdown, 
    BDropdownItem,
    BBadge,
    BAlert,
    BProgress, 
    BButton,
  },
  //PROPS INI ADALAH DATA YANG AKAN DIMINTA DARI PENGGUNA COMPONENT DATATABLE YANG KITA BUAT
  props: {
        //ITEMS STRUKTURNYA ADALAH ARRAY, KARENA BAGIAN INI BERISI DATA YANG AKAN DITAMPILKAN DAN SIFATNYA WAJIB DIKIRIMKAN KETIKA COMPONENT INI DIGUNAKAN
        items: {
            type: Array,
            required: true
        },
        //FIELDS JUGA SAMA DENGAN ITEMS
        fields: {
            type: Array,
            required: true
        },
        //ADAPUN META, TYPENYA ADALAH OBJECT YANG BERISI INFORMASI MENGENAL CURRENT PAGE, LOAD PERPAGE, TOTAL DATA, DAN LAIN SEBAGAINYA.
        meta: {
            required: true
        },
        isBusy: {
            type: Boolean,
            default: () => true,
        }
    },
    data() {
        return {
          previewUrl: '',
          blangko_id: '',
          sortBy: null, 
          sortDesc: false,
        }
    },
    watch: {
        //KETIKA VALUE DARI VARIABLE sortBy BERUBAH
        sortBy(val) {
            //MAKA KITA EMIT DENGAN NAMA SORT DAN DATANYA ADALAH OBJECT BERUPA VALUE DARI SORTBY DAN SORTDESC
            //EMIT BERARTI MENGIRIMKAN DATA KEPADA PARENT ATAU YANG MEMANGGIL COMPONENT INI
            //SEHINGGA DARI PARENT TERSEBUT, KITA BISA MENGGUNAKAN VALUE YANG DIKIRIMKAN
            this.$emit('sort', {
                sortBy: this.sortBy,
                sortDesc: this.sortDesc
            })
        },
        //KETIKA VALUE DARI SORTDESC BERUBAH
        sortDesc(val) {
            //MAKA CARA YANG SAMA AKAN DIKERJAKAN
            this.$emit('sort', {
                sortBy: this.sortBy,
                sortDesc: this.sortDesc
            })
        }
    },
    methods: {
      handleOk(bvModalEvent) {
        bvModalEvent.preventDefault()
        this.prosesVerifikasi(1)
      },
      handleCancel(bvModalEvent) {
        bvModalEvent.preventDefault()
        this.prosesVerifikasi(0)
      },
      prosesVerifikasi(status){
        console.log(this.blangko_id);
        this.$swal({
          title: 'Apakah Anda yakin?',
          text: (status) ? 'Blangko Ijazah ini akan dilaporkan Valid!' : 'Blangko Ijazah ini akan dilaporkan Invalid!',
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
            this.$http.post('/blangko/verifikasi', {
              blangko_id: this.blangko_id,
              status: status,
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
                this.$refs['modal-preview'].hide()
                this.loadPerPage(this.meta.per_page)
              })
            });
          }
        })
      },
      batalkan(item){
        this.blangko_id = item.id
        this.$swal({
          title: 'Apakah Anda yakin?',
          text: 'Aksi ini akan membatalkan proses verifikasi yang telah tersimpan!',
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
            this.$http.post('/blangko/batalkan-verifikasi', {
              blangko_id: this.blangko_id,
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
                this.loadPerPage(this.meta.per_page)
              })
            });
          }
        })
      },
      preview(item){
        this.blangko_id = item.id
        var _this = this
        this.$http.get('/blangko/preview', {
          params: {
            npsn: item.sekolah.npsn,
            filename: item.berkas,
          }
        }).then(response => {
          let getData = response.data.data
          if(getData){
            this.previewUrl = 'https://drive.google.com/file/d/'+getData.file_id+'/preview'
            this.$refs['modal-preview'].show()
          } else {
            _this.$swal({
              icon: 'error',
              title: 'Gagal',
              html: 'File pdf tidak ditemukan!',
              customClass: {
                confirmButton: 'btn btn-success',
              },
            })
          }
        })
      },
      loadPerPage(val) {
          //DAN KITA EMIT LAGI DENGAN NAMA per_page DAN VALUE SESUAI PER_PAGE YANG DIPILIH
          this.$emit('per_page', this.meta.per_page)
      },
      //KETIKA PAGINATION BERUBAH, MAKA FUNGSI INI AKAN DIJALANKAN
      changePage(val) {
          //KIRIM EMIT DENGAN NAMA PAGINATION DAN VALUENYA ADALAH HALAMAN YANG DIPILIH OLEH USER
          this.$emit('pagination', val)
      },
      //KETIKA KOTAK PENCARIAN DIISI, MAKA FUNGSI INI AKAN DIJALANKAN
      //KITA GUNAKAN DEBOUNCE UNTUK MEMBUAT DELAY, DIMANA FUNGSI INI AKAN DIJALANKAN
      //500 MIL SECOND SETELAH USER BERHENTI MENGETIK
      search: _.debounce(function (e) {
          //KIRIM EMIT DENGAN NAMA SEARCH DAN VALUE SESUAI YANG DIKETIKKAN OLEH USER
          this.$emit('search', e.target.value)
      }, 500),
    },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>