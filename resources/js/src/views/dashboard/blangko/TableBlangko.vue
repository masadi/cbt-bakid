<template>
  <div>
    <b-row>
      <b-col md="4" class="mb-2" v-if="hasRole('disprov')">
        <b-form-select v-model="kabupaten_id" :options="optionsKabupaten" @change="loadKabupaten"></b-form-select>
        <!--select class="form-control" v-model="kabupaten_id" @change="loadKabupaten" :options="optionsKabupaten">
          <option value="">== Filter Kabupaten/Kota ==</option>
        </select-->
      </b-col>
      <b-col md="4" class="mb-2" v-if="hasRole('diskab')">
        <b-form-select v-model="sekolah_id" :options="optionSekolah" @change="loadSekolah"></b-form-select>
        <!--select class="form-control" v-model="sekolah_id" :options="optionSekolah" @change="loadSekolah">
          <option value="">== Filter Sekolah ==</option>
        </select-->
      </b-col>
      <b-col md="4" class="mb-2">
        <select class="form-control" v-model="program" @change="loadProgram">
            <option value="">== Filter Program ==</option>
            <option value="3">SMK 3 Tahun</option>
            <option value="4">SMK 4 Tahun</option>
          </select>
      </b-col>
      <b-col md="4" class="mb-2" offset-md="4" v-if="hasRole('sekolah')">
        <select class="form-control" v-model="status" @change="loadStatus">
            <option value="all">== Filter Status Konfirmasi ==</option>
            <option value="belum">Belum</option>
            <option value="ada">Ada</option>
            <option value="tidak-ada">Tidak Ada</option>
            <option value="rusak">Rusak</option>
          </select>
      </b-col>
      <b-col md="4" class="mb-2" v-else>
        <select class="form-control" v-model="status" @change="loadStatus">
            <option value="all">== Filter Status Konfirmasi ==</option>
            <option value="belum">Belum</option>
            <option value="ada">Ada</option>
            <option value="tidak-ada">Tidak Ada</option>
            <option value="rusak">Rusak</option>
          </select>
      </b-col>
    </b-row>
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
      <template v-slot:cell(tahun)="row">
        SMK {{row.item.tahun}} Tahun
      </template>
      <template v-slot:cell(sekolah)="row">
        {{(row.item.sekolah) ? row.item.sekolah.nama : '-'}}
      </template>
      <template v-slot:cell(confirm)="row">
        <div v-if="hasRole('disprov')">
          <template v-if="row.item.cadangan">
            <b-badge variant="success" v-if="row.item.confirm_provinsi">Sudah</b-badge>
            <b-badge variant="danger" v-else>Belum</b-badge>
          </template>
          <template v-else>
            <b-badge variant="success" v-if="row.item.confirm_sekolah">Sudah</b-badge>
            <b-badge variant="danger" v-else>Belum</b-badge>
          </template>
        </div>
        <div v-if="hasRole('diskab')">
          <template v-if="row.item.cadangan">
            <b-badge variant="success" v-if="row.item.confirm_kabupaten">Sudah</b-badge>
            <b-badge variant="danger" v-else>Belum</b-badge>
          </template>
          <template v-else>
            <b-badge variant="success" v-if="row.item.confirm_sekolah">Sudah</b-badge>
            <b-badge variant="danger" v-else>Belum</b-badge>
          </template>
          <!--b-badge variant="success" v-if="row.item.confirm_kabupaten">Sudah</b-badge>
          <b-badge variant="danger" v-else>Belum</b-badge-->
        </div>
        <div v-if="hasRole('sekolah')">
          <b-badge variant="success" v-if="row.item.confirm_sekolah">Sudah</b-badge>
          <b-badge variant="danger" v-else>Belum</b-badge>
        </div>
      </template>
      <template v-slot:cell(status_confirm)="row">
        <div v-if="hasRole('disprov')">
          <template v-if="row.item.cadangan">
            <b-badge :variant="confirmVariant(row.item.confirm_provinsi)">{{statusConfirm(row.item.confirm_provinsi)}}</b-badge>
          </template>
          <template v-else>
            <b-badge :variant="confirmVariant(row.item.confirm_sekolah)">{{statusConfirm(row.item.confirm_sekolah)}}</b-badge>
          </template>
        </div>
        <div v-if="hasRole('diskab')">
          <template v-if="row.item.cadangan">
            <b-badge :variant="confirmVariant(row.item.confirm_kabupaten)">{{statusConfirm(row.item.confirm_kabupaten)}}</b-badge>
          </template>
          <template v-else>
            <b-badge :variant="confirmVariant(row.item.confirm_sekolah)">{{statusConfirm(row.item.confirm_sekolah)}}</b-badge>
          </template>
        </div>
        <div v-if="hasRole('sekolah')">
          <b-badge :variant="confirmVariant(row.item.confirm_sekolah)">{{statusConfirm(row.item.confirm_sekolah)}}</b-badge>
        </div>
      </template>
      <template v-slot:cell(blanko)="row">
        <b-badge variant="success" v-if="row.item.blangko_3">Sudah</b-badge>
        <b-badge variant="danger" v-else>Belum</b-badge>
      </template>
      <template v-slot:cell(khusus)="row">
        <b-badge variant="success" v-if="row.item.user && row.item.user.fire == '1'">Ya</b-badge>
        <b-badge variant="danger" v-else>Tidak</b-badge>
      </template>
      <template v-slot:cell(actions)="row">
        <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="success" size="sm">
          <b-dropdown-item href="javascript:" @click="confirm(row.item, 1)"><font-awesome-icon icon="fa-solid fa-circle-check" /> Ada</b-dropdown-item>
          <b-dropdown-item href="javascript:" @click="confirm(row.item, 2)"><font-awesome-icon icon="fa-solid fa-circle-xmark" /> Tidak Ada</b-dropdown-item>
          <b-dropdown-item href="javascript:" @click="confirm(row.item, 3)"><font-awesome-icon icon="fa-solid fa-circle-exclamation" /> Rusak</b-dropdown-item>
          <b-dropdown-item href="javascript:" @click="sekolah(row.item)" v-if="cadangan && row.item.confirm_provinsi == 1 && !row.item.peserta_didik_id"><font-awesome-icon icon="fa-solid fa-building-columns" /> Sekolah</b-dropdown-item>
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
    <b-modal ref="modal-sekolah" title="Tentukan Sekolah" ok-title="Simpan" cancel-title="Batal" @ok="handleOk">
      <form ref="form" @submit.stop.prevent="handleSubmit">
        <b-form-group id="kode_wilayah" label="Kabupaten/Kota" label-for="kode_wilayah" :invalid-feedback="invalid_wilayah" :state="wilayahState">
          <b-form-select v-model="kode_wilayah" :options="data_wilayah" @change="getSekolah" text-field="nama" value-field="kode_wilayah" :state="wilayahState" :disabled="isDisabled"></b-form-select>
        </b-form-group>
        <b-form-group id="id_sekolah" label="Sekolah" label-for="id_sekolah" :invalid-feedback="invalid_sekolah" :state="sekolahState">
          <b-form-select v-model="id_sekolah" :options="data_sekolah" text-field="nama" value-field="sekolah_id" :state="sekolahState"></b-form-select>
        </b-form-group>
      </form>
    </b-modal>
  </div>
</template>

<script>
import _ from 'lodash' //IMPORT LODASH, DIMANA AKAN DIGUNAKAN UNTUK MEMBUAT DELAY KETIKA KOLOM PENCARIAN DIISI
import { BRow, BCol, BTable, BSpinner, BPagination, BDropdown, BDropdownItem, BBadge, BAlert, BProgress, BButtonGroup, BButton, BFormGroup, BFormSelect } from 'bootstrap-vue'
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
    BButtonGroup,
    BButton,
    BFormGroup,
    BFormSelect,
  },
  //PROPS INI ADALAH DATA YANG AKAN DIMINTA DARI PENGGUNA COMPONENT DATATABLE YANG KITA BUAT
  props: {
    //ITEMS STRUKTURNYA ADALAH ARRAY, KARENA BAGIAN INI BERISI DATA YANG AKAN DITAMPILKAN DAN SIFATNYA WAJIB DIKIRIMKAN KETIKA COMPONENT INI DIGUNAKAN
    items: {
      type: Array,
      required: true
    },
    optionsKabupaten: {
      type: Array,
      required: true
    },
    optionSekolah: {
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
    },
    cadangan: {
      type: Boolean,
      default: () => false,
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
      status: 'all',
      program: '',
      sekolah_id: '',
      kabupaten_id: '',
      kode_wilayah: null,
      data_wilayah: [],
      id_sekolah: null,
      data_sekolah: [],
      ijazah_id: null,
      wilayahState: null,
      sekolahState: null,
      invalid_wilayah: '',
      invalid_sekolah: '',
      isDisabled: false,
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
    confirmVariant(item){
      let data = {
        0: 'secondary',
        1: 'success',
        2: 'warning',
        3: 'danger',
      };
      return data[item]
    },
    statusConfirm(item){
      let data = {
        0: 'Belum',
        1: 'Ada',
        2: 'Tidak ada',
        3: 'Rusak',
      };
      return data[item]
    },
    confirm(item, status){
      var text;
      if(status == 1){
        text = 'ada!';
      }
      if(status == 2){
        text = 'tidak ada!';
      }
      if(status == 3){
        text = 'rusak!';
      }
      this.$swal({
        title: 'Apakah Anda yakin?',
        text: 'Aksi ini akan melaporkan Blangko Diterima '+text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yakin!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ml-1',
        },
        buttonsStyling: false,
        allowOutsideClick: () => !this.$swal.isLoading(),
      }).then(result => {
        if (result.value) {
          this.$http.post('/blangko/confirm', {
            ijazah_id: item.id,
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
              this.loadPerPage(10);
            })
          });
        }
      })
    },
    sekolah(item){
      this.kode_wilayah = item.kabupaten_id
      this.ijazah_id = item.id
      this.$http.post('/blangko/sekolah', {
        kode_wilayah: this.kode_wilayah,
      }).then(response => {
        if(this.kode_wilayah){
          this.isDisabled = true
        }
        this.data_wilayah = response.data.wilayah
        this.data_sekolah = response.data.sekolah
        this.$refs['modal-sekolah'].show()
      });
    },
    getSekolah(val){
      this.kode_wilayah = val
      this.$http.post('/blangko/sekolah', {
        kode_wilayah: this.kode_wilayah,
      }).then(response => {
        this.data_sekolah = response.data.sekolah
      });
    },
    handleSubmit(){
      this.$http.post('/blangko/sekolah', {
        aksi: 'aksi',
        ijazah_id: this.ijazah_id,
        id_sekolah: this.id_sekolah,
        kode_wilayah: this.kode_wilayah,
      }).then(response => {
        let data = response.data
        if(data.errors){
          this.wilayahState = (data.errors.kode_wilayah) ? false : true
          this.sekolahState = (data.errors.id_sekolah) ? false : true
          this.invalid_wilayah = (data.errors.kode_wilayah) ? data.errors.kode_wilayah[0] : null
          this.invalid_sekolah = (data.errors.id_sekolah) ? data.errors.id_sekolah[0] : null
        } else {
          this.$swal({
            icon: data.icon,
            title: data.title,
            text: data.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.$refs['modal-sekolah'].hide()
            this.loadPerPage(this.meta.per_page)
          })
        }
        //this.data_sekolah = response.data.sekolah
      });
    },
    handleOk(bvModalEvent) {
      // Prevent modal from closing
      bvModalEvent.preventDefault()
      // Trigger submit handler
      this.handleSubmit()
    },
    loadPerPage(val) {
      //DAN KITA EMIT LAGI DENGAN NAMA per_page DAN VALUE SESUAI PER_PAGE YANG DIPILIH
      this.$emit('per_page', this.meta.per_page)
    },
    loadStatus(val){
      this.$emit('status', this.status)
    },
    loadKabupaten(val){
      this.$emit('kabupaten_id', this.kabupaten_id)
    },
    loadSekolah(val){
      this.$emit('sekolah_id', this.sekolah_id)
    },
    loadProgram(val){
      this.$emit('program', this.program)
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