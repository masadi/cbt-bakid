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
    <b-table responsive striped :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" show-empty :busy="isBusy">
      <template #table-busy>
        <div class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
      </template>
      <template v-slot:cell(actions)="row">
        <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="success" size="sm">
          <b-dropdown-item href="javascript:" @click="detil(row.item)"><font-awesome-icon icon="fa-solid fa-eye" /> Detil</b-dropdown-item>
          <b-dropdown-item href="javascript:" @click="generateUser(row.item)" v-show="!row.item.user"><font-awesome-icon icon="fa-solid fa-user" /> Generate User</b-dropdown-item>
          <b-dropdown-item href="javascript:" @click="resetPassword(row.item)" v-show="row.item.user"><font-awesome-icon icon="fa-solid fa-user" /> Reset Password</b-dropdown-item>
          <!--b-dropdown-item href="javascript:" @click="makeFire(row.item)" v-show="row.item.user"><font-awesome-icon icon="fa-solid fa-fire" /> Sekolah Khusus</b-dropdown-item-->
        </b-dropdown>
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
    <b-modal ref="my-modal" size="lg" :title="judul" @show="resetDetil" ok-only :ok-title="ok_title" :ok-variant="ok_variant" @ok="tutupDetil">
      <table class="table table-bordered">
        <tr>
          <td>Nama</td>
          <td>{{(detail) ? detail.nama : '-'}}</td>
        </tr>
        <tr>
          <td>NPSN</td>
          <td>{{(detail) ? detail.npsn : '-'}}</td>
        </tr>
        <tr>
          <td>Alamat</td>
          <td>{{(detail) ? detail.alamat : '-'}}</td>
        </tr>
        <tr>
          <td>Kodepos</td>
          <td>{{(detail) ? detail.kode_pos : '-'}}</td>
        </tr>
        <tr>
          <td>Desa/Kelurahan</td>
          <td>{{(detail) ? detail.desa_kelurahan : '-'}}</td>
        </tr>
        <tr>
          <td>Kecamatan</td>
          <td>{{(detail) ? detail.kecamatan : '-'}}</td>
        </tr>
        <tr>
          <td>Kabupaten/Kota</td>
          <td>{{(detail) ? detail.kabupaten : '-'}}</td>
        </tr>
        <tr>
          <td>Provinsi</td>
          <td>{{(detail) ? detail.provinsi : '-'}}</td>
        </tr>
        <tr>
          <td>Email</td>
          <td>{{(detail) ? detail.email : '-'}}</td>
        </tr>
        <tr>
          <td>Website</td>
          <td>{{(detail) ? detail.website : '-'}}</td>
        </tr>
      </table>
      <h3>Jumlah Peserta Didik</h3>
      <table class="table table-bordered">
        <td class="text-center">Program</td>
          <td class="text-center">Jumlah PD</td>
          <td class="text-center">Jumlah Penerima</td>
          <td class="text-center">Telah Terima Ijazah</td>
        <tr>
          <td class="text-center">3 Tahun</td>
          <td class="text-center">{{pd_3}}</td>
          <td class="text-center">{{penerima_3}}</td>
          <td class="text-center">{{terima_3}}</td>
        </tr>
        <tr>
          <td class="text-center">4 Tahun</td>
          <td class="text-center">{{pd_4}}</td>
          <td class="text-center">{{penerima_4}}</td>
          <td class="text-center">{{terima_4}}</td>
        </tr>
      </table>
    </b-modal>
    <b-modal ref="ambilDapo" title="Ambil Data Peserta Didik" no-close-on-esc no-close-on-backdrop>
      <p v-show="show">Ambil Data Peserta Didik</p>
      <b-alert v-show="alert" show variant="danger">Pengambilan data Dapodik gagal. Silahkan coba beberapa saat lagi!</b-alert>
      <b-progress v-show="!show" height="2rem" :value="value" :max="max" show-progress animated></b-progress>
      <template #modal-footer="{ ok, cancel }">
        <b-button size="sm" variant="danger" v-show="show" @click="cancel()">Batal</b-button>
        <b-button size="sm" variant="success" v-show="show" @click="ambilDapo()">Ambil Data</b-button>
        <b-spinner label="Loading..." v-show="!show"></b-spinner>
      </template>
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
        title: {
            type: String,
            default: "Delete Modal"
        },
        editUrl: {
            type: String,
            default: null
        },
        isBusy: {
            type: Boolean,
            default: () => true,
        }
    },
    data() {
        return {
            //isBusy: true,
            //VARIABLE INI AKAN MENGHADLE SORTING DATA
            sortBy: null, //FIELD YANG AKAN DISORT AKAN OTOMATIS DISIMPAN DISINI
            sortDesc: false, //SEDANGKAN JENISNYA ASCENDING ATAU DESC AKAN DISIMPAN DISINI
            //TAMBAHKAN DUA VARIABLE INI UNTUK MENGHANDLE MODAL DAN DATA YANG AKAN DIHAPUS
            judul: null,
            detail: null,
            ok_title: 'Tutup',
            ok_variant: 'danger',
            alert: false,
            value: 0,
            max: 100,
            show: true,
            polling: null,
            pd_3: 0,
            pd_4: 0,
            penerima_3: 0,
            penerima_4: 0,
            terima_3: 0,
            terima_4: 0,
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
      tutupDetil(){
        if(!this.pd_3 && this.hasRole('administrator')){
          this.$refs['ambilDapo'].show()
        }
      },
        detil(item){
          console.log(item);
          this.judul = 'Data '+item.nama
          this.detail = {
            sekolah_id: item.sekolah_id,
            nama: item.nama,
            npsn: item.npsn,
            alamat: item.alamat,
            kode_pos: item.kode_pos,
            desa_kelurahan: item.desa_kelurahan,
            kecamatan: item.kecamatan,
            kabupaten: item.kabupaten,
            provinsi: item.provinsi,
            email: item.email,
            website: item.website,
            user: item.user,
          }
          this.pd_3 =  item.pd_3
          this.pd_4 =  item.pd_4
          this.penerima_3 =  item.penerima_3
          this.penerima_4 =  item.penerima_4
          this.terima_3 =  item.terima_3
          this.terima_4 =  item.terima_4
          if(!this.pd_3 && this.hasRole('administrator')){
            this.ok_title = 'Ambil PD'
            this.ok_variant = 'success'
          }
          this.$refs['my-modal'].show()
        },
        resetDetil(){
          //
        },
        ambilDapo(){
          this.show = false
          this.alert = false
          this.pollData()
          this.$http.post('/referensi/ambil-pd', {
            sekolah_id: this.detail.sekolah_id,
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
            sekolah_id: this.detail.sekolah_id,
            email: this.detail.user.email,
            npsn: this.detail.npsn,
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
        generateUser(item){
          this.$swal({
            title: 'Apakah Anda yakin?',
            text: "Aksi ini akan menggenerate pengguna Satuan Pendidikan!",
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
              this.$http.post('/referensi/generate-pengguna', {
                sekolah_id: item.sekolah_id
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
                  this.loadPerPage(10);
                })
              });
            }
          })
        },
        makeFire(item){
          var text;
          if(item.user.fire == '1'){
            text = 'Aksi ini akan mencabut akses Pemusnahan Blangko Ijazah ke Satuan Pendidikan!';
          } else {
            text = 'Aksi ini akan memberikan akses Pemusnahan Blangko Ijazah ke Satuan Pendidikan!';
          }
          this.$swal({
            title: 'Apakah Anda yakin?',
            text: text,
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
              this.$http.post('/referensi/make-fire', {
                fire: item.user.fire,
                user_id: item.user.user_id
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
                  this.loadPerPage(10);
                })
              });
            }
          })
        },
        resetPassword(item){
          this.$swal({
            title: 'Apakah Anda yakin?',
            text: "Tindakan ini tidak dapat dikembalikan",
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
              this.$http.post('/referensi/users/reset-password', {
                user_id: item.user.user_id
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
                  this.loadPerPage(10);
                })
              });
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