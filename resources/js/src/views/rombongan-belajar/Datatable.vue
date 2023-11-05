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
      <template v-slot:cell(walas)="row">
        {{ (row.item.walas) ? row.item.walas.nama : '-' }}
      </template>
      <template v-slot:cell(actions)="row">
        <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="success" size="sm">
          <b-dropdown-item href="javascript:" @click="detil(row.item)"><font-awesome-icon icon="fa-solid fa-eye" /> Pembelajaran</b-dropdown-item>
          <b-dropdown-item href="javascript:" @click="walas(row.item)"><font-awesome-icon icon="fa-solid fa-user" /> Wali Kelas</b-dropdown-item>
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
    <b-modal ref="my-modal" size="xl" :title="judul" ok-title="Simpan" cancel-title="Tutup" @ok="handleOk">
      <form ref="form" @submit.stop.prevent="handleSubmit">
        <table class="table table-bordered">
          <thead>
            <th class="text-center">NO</th>
            <th class="text-center">Mata Pelajaran</th>
            <th class="text-center">Guru</th>
            <th class="text-center">Hapus</th>
          </thead>
          <tbody>
            <template v-if="data_mapel.length">
              <tr v-for="(mapel, index) in data_mapel" :key="mapel.mata_pelajaran_id">
                <td class="text-center">{{index + 1}}</td>
                <td>{{mapel.nama}}</td>
                <td>
                  <input type="hidden" v-model="form.mata_pelajaran_id[mapel.mata_pelajaran_id]" />
                  <b-form-select v-model="form.guru_id[mapel.mata_pelajaran_id]" :options="data_guru" value-field="guru_id" text-field="nama"></b-form-select>
                </td>
                <td class="text-center">
                  <b-button variant="danger" size="sm" @click="hapus(mapel.pembelajaran.pembelajaran_id, mapel.pembelajaran.rombongan_belajar_id)" v-if="mapel.pembelajaran">Hapus</b-button>
                </td>
              </tr>
            </template>
            <template v-else>
              <tr>
                <td class="text-center" colspan="4">Tidak ada data untuk ditampilkan</td>
              </tr>
            </template>
          </tbody>
        </table>
      </form>
    </b-modal>
    <b-modal ref="ganti-walas" size="xl" :title="judul" ok-title="Simpan" cancel-title="Tutup" @ok="handleOkWalas">
      <form ref="form" @submit.stop.prevent="handleSubmit">
        <b-col cols="12">
          <b-form-group label="Wali Kelas" label-for="guru_id" label-cols-md="3" :invalid-feedback="feedback.guru_id" :state="state.guru_id">
            <v-select id="guru_id" v-model="form_walas.guru_id" :reduce="nama => nama.guru_id" label="nama" :options="data_guru" placeholder="== Pilih Wali Kelas ==" :state="state.guru_id">
              <template #no-options="{ search, searching, loading }">
                Tidak ada data untuk ditampilkan
              </template>
            </v-select>
          </b-form-group>
        </b-col>
      </form>
    </b-modal>
  </div>
</template>

<script>
import _ from 'lodash' //IMPORT LODASH, DIMANA AKAN DIGUNAKAN UNTUK MEMBUAT DELAY KETIKA KOLOM PENCARIAN DIISI
import { BRow, BCol, BTable, BSpinner, BPagination, BDropdown, BDropdownItem, BAlert, BButton, BFormSelect, BFormGroup } from 'bootstrap-vue'
import vSelect from 'vue-select'
export default {
  components: {
    BRow,
    BCol,
    BTable,
    BSpinner,
    BPagination,
    BDropdown, 
    BDropdownItem,
    BAlert,
    BButton,
    BFormSelect,
    BFormGroup,
    vSelect,
  },
  props: {
    items: {
      type: Array,
      required: true
    },
    fields: {
      type: Array,
      required: true
    },
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
      sortBy: null,
      sortDesc: false,
      judul: null,
      data_mapel: [],
      form: {
        rombongan_belajar_id: '',
        guru_id: {},
        mata_pelajaran_id: {},
      },
      form_walas: {
        guru_id: '',
        rombongan_belajar_id: '',
      },
      feedback: {
        guru_id: '',
      },
      state: {
        guru_id: null,
      },
      data_guru: [],
    }
  },
  watch: {
    sortBy(val) {
      this.$emit('sort', {
        sortBy: this.sortBy,
        sortDesc: this.sortDesc
      })
    },
    sortDesc(val) {
      this.$emit('sort', {
        sortBy: this.sortBy,
        sortDesc: this.sortDesc
      })
    }
  },
  methods: {
    detil(item){
      this.$http.post('/rombongan-belajar/pembelajaran', {
        rombongan_belajar_id: item.rombongan_belajar_id
      }).then(response => {
        let getData = response.data
        this.data_mapel = getData.data
        this.data_mapel.forEach((value, index) => {
            this.form.mata_pelajaran_id[value.mata_pelajaran_id] = value.mata_pelajaran_id
            this.form.guru_id[value.mata_pelajaran_id] = (value.pembelajaran) ? value.pembelajaran.guru_id : ''
        });
        this.form.rombongan_belajar_id = item.rombongan_belajar_id
        this.judul = getData.judul
        this.data_guru = getData.guru
        this.$refs['my-modal'].show()
      });
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.$http.post('/rombongan-belajar/simpan-pembelajaran', this.form).then(response => {
        let data = response.data
        this.$swal({
          icon: data.icon,
          title: data.title,
          text: data.text,
          customClass: {
            confirmButton: 'btn btn-success',
          },
        }).then(result => {
          this.$refs['my-modal'].hide()
        })
      })
    },
    handleOkWalas(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmitWalas()
    },
    handleSubmitWalas(){
      this.$http.post('/rombongan-belajar/simpan-wali-kelas', this.form_walas).then(response => {
        let getData = response.data
        if(getData.errors){
          this.state.guru_id = (getData.errors.guru_id) ? false : null
          this.feedback.guru_id = (getData.errors.guru_id) ? getData.errors.guru_id.join(', ') : ''
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            text: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.$refs['ganti-walas'].hide()
            this.loadPerPage(this.meta.per_page)
          })
        }
      })
    },
    hapus(pembelajaran_id, rombongan_belajar_id){
      this.$swal({
        title: 'Apakah Anda yakin?',
        text: "Aksi ini tidak dapat dikembalikan!",
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
          this.$http.post('/rombongan-belajar/hapus-pembelajaran', {
            pembelajaran_id: pembelajaran_id,
            rombongan_belajar_id: rombongan_belajar_id,
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
              this.data_mapel = data.data_mapel
              this.data_mapel.forEach((value, index) => {
                this.form.mata_pelajaran_id[value.mata_pelajaran_id] = value.mata_pelajaran_id
                this.form.guru_id[value.mata_pelajaran_id] = (value.pembelajaran) ? value.pembelajaran.guru_id : ''
              });
            })
          });
        }
      })
    },
    walas(item){
      this.form_walas.guru_id = item.guru_id
      this.form_walas.rombongan_belajar_id = item.rombongan_belajar_id
      this.$http.post('/rombongan-belajar/wali-kelas', {
        rombongan_belajar_id: item.rombongan_belajar_id
      }).then(response => {
        let getData = response.data
        this.judul = getData.judul
        this.form_walas.guru_id = getData.rombel.guru_id
        this.data_guru = getData.guru
        this.$refs['ganti-walas'].show()
      });
    },
    loadPerPage(val) {
      this.$emit('per_page', this.meta.per_page)
    },
    changePage(val) {
      this.$emit('pagination', val)
    },
    search: _.debounce(function (e) {
      this.$emit('search', e.target.value)
    }, 500),
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>