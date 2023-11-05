<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Berita Acara Pendistribusian Blangko Ijazah</h2>
      </b-card-title>
      <div class="heading-elements" v-show="jumlah_3" v-if="!hasRole('diskab')">
        <b-button variant="danger" size="sm" v-b-modal.berita-acara>Edit Data</b-button>
        <b-button variant="success" :href="link_cetak" target="_blank" v-show="data">Cetak</b-button>
        <b-button variant="warning" size="sm" v-show="data" v-b-modal.upload-berita-acara>Unggah Berita Acara</b-button>
      </div>
    </b-card-header>
    <b-card-body v-if="!hasRole('diskab')">
      <p>Pada hari {{hari}} tanggal {{tanggal}} bulan {{bulan}} tahun {{tahun}}, telah dilaksanakan kegiatan Pendistribusian Blangko Ijazah oleh:</p>
      <table>
        <tr>
          <td>Nama</td>
          <td>: {{(data) ? data.nama : '-'}}</td>
        </tr>
        <tr>
          <td>Jabatan</td>
          <td>: {{(data) ? data.jabatan : '-'}}</td>
        </tr>
        <tr>
          <td>Instansi</td>
          <td>: {{(data) ? data.instansi : '-'}}</td>
        </tr>
        <tr>
          <td>Kab/Kota</td>
          <td>: {{(data) ? data.kabupaten : '-'}}</td>
        </tr>
        <tr>
          <td>Provinsi</td>
          <td>: {{(data) ? data.provinsi : '-'}}</td>
        </tr>
        <tr v-show="data && data.negara">
          <td>Negara</td>
          <td>: {{(data) ? data.negara : '-'}}</td>
        </tr>
        <tr>
          <td>Penerima Blangko Ijazah</td>
          <td>: <i>TERLAMPIR</i></td>
        </tr>
      </table>
      <table class="mt-1" width="50%">
        <tr>
          <td class="text-center">
            {{jabatan}},
            <br>
            <br>
            <br>
            <br>
            {{nama}}
          </td>
        </tr>
      </table>
    </b-card-body>
    <b-card-body v-else>
      <datatable :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" />
    </b-card-body>
    <b-modal id="berita-acara" ref="modal" title="Edit Data Berita Acara Pendistribusian Blangko Ijazah" @show="resetModal" @hidden="resetModal" @ok="handleOk" ok-title="Simpan" cancel-title="Batal">
      <form ref="form" @submit.stop.prevent="handleSubmit">
        <b-form-group label="Tanggal" label-for="tanggal" :invalid-feedback="feedback_tanggal" :state="tanggalState">
          <b-form-datepicker id="tanggal" v-model="form_tanggal" :state="tanggalState" locale="id-ID"></b-form-datepicker>
        </b-form-group>
        <b-form-group label="Nama" label-for="nama" :invalid-feedback="feedback_nama" :state="namaState">
          <b-form-input id="nama" v-model="nama" :state="namaState"></b-form-input>
        </b-form-group>
        <b-form-group label="Jabatan" label-for="jabatan" :invalid-feedback="feedback_jabatan" :state="jabatanState">
          <b-form-input id="jabatan" v-model="jabatan" :state="jabatanState"></b-form-input>
        </b-form-group>
        <b-form-group label="Instansi" label-for="instansi" :invalid-feedback="feedback_instansi" :state="instansiState">
          <b-form-input id="instansi" v-model="instansi" :state="instansiState"></b-form-input>
        </b-form-group>
        <b-form-group label="Kabupaten/Kota" label-for="kabupaten" :invalid-feedback="feedback_kabupaten" :state="kabupatenState">
          <b-form-input id="kabupaten" v-model="kabupaten" :state="kabupatenState"></b-form-input>
        </b-form-group>
        <b-form-group label="Provinsi" label-for="provinsi" :invalid-feedback="feedback_provinsi" :state="provinsiState">
          <b-form-input id="provinsi" v-model="provinsi" :state="provinsiState"></b-form-input>
        </b-form-group>
        <b-form-group label="Negara" label-for="negara" :invalid-feedback="feedback_negara" :state="negaraState" v-show="fire">
          <b-form-input id="negara" v-model="negara" :state="negaraState"></b-form-input>
        </b-form-group>
      </form>
    </b-modal>
    <b-modal id="upload-berita-acara" title="Unggah Berita Acara Pendistribusian Blangko Ijazah">
      <template #modal-footer="{ ok, cancel }">
          <b-button variant="danger" @click="cancel()" v-if="!loading">
            Tutup
          </b-button>
          <b-button variant="success" @click="handleOkUpload" v-if="!loading">
            Unggah
          </b-button>
          <b-button variant="success" disabled v-if="loading">
            <b-spinner small></b-spinner>
            <span class="sr-only">Loading...</span>
          </b-button>
        </template>
      <form ref="form" @submit.stop.prevent="handleSubmitUpload">
        <b-form-file v-model="file1" :state="fileState" placeholder="Unggah berkas PDF disini..." drop-placeholder="Unggah berkas PDF disini..." @change="onFileChange"></b-form-file>
        <p v-show="feedback_file" class="text-danger">{{feedback_file}}</p>
      </form>
    </b-modal>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody, BButton, BFormGroup, BFormInput, BFormSelect, BFormDatepicker, BFormFile, BSpinner } from 'bootstrap-vue'
import Datatable from './TableBeritaAcara.vue'

export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BButton,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BFormDatepicker,
    BFormFile,
    BSpinner,
    Datatable,
  },
  data() {
    return {
      loading: false,
      options: [
        { value: '', text: '== Pilih Instansi ==' },
        { value: 'Atase Pendidikan dan Kebudayaan', text: 'Atase Pendidikan dan Kebudayaan' },
        { value: 'Kantor Perwakilan RI di Luar Negeri', text: 'Kantor Perwakilan RI di Luar Negeri' },
        { value: 'Kepolisian Republik Indonesia', text: 'Kepolisian Republik Indonesia' },
      ],
      file1: null,
      id: '',
      fileState: null,
      feedback_file: '',
      fire: false,
      isBusy: true,
      data: {},
      pihak_2: {},
      hari: '-',
      tanggal: '-',
      bulan: '-',
      tahun: '-',
      form_tanggal: '',
      nama: '',
      instansi: '',
      jabatan: '',
      provinsi: '',
      kabupaten: '',
      negara: '',
      nama_2: '',
      instansi_2: '',
      jabatan_2: '',
      provinsi_2: '',
      kabupaten_2: '',
      feedback_tanggal: '',
      feedback_nama: '',
      feedback_jabatan: '',
      feedback_instansi: '',
      feedback_provinsi: '',
      feedback_kabupaten: '',
      feedback_nama_2: '',
      feedback_jabatan_2: '',
      feedback_instansi_2: '',
      feedback_provinsi_2: '',
      feedback_kabupaten_2: '',
      feedback_negara: '',
      tanggalState: null,
      namaState: null,
      jabatanState: null,
      instansiState: null,
      provinsiState: null,
      kabupatenState: null,
      nama_2State: null,
      jabatan_2State: null,
      instansi_2State: null,
      provinsi_2State: null,
      kabupaten_2State: null,
      negaraState: null,
      start_3: '',
      end_3: '',
      jumlah_3: '',
      start_4: '',
      end_4: '',
      jumlah_4: '',
      link_cetak: '#',
      baru: [],
      fields: [
        {
          key: 'nama',
          label: 'Nama',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'npsn',
          label: 'NPSN',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'kabupaten',
          label: 'Kabupaten/Kota',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'provinsi',
          label: 'Provinsi',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'actions',
          label: 'Aksi',
          sortable: true,
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
    if(this.hasRole('diskab')){
      this.loadPostsData()
    } else {
      this.getBeritaAcara()
    }
  },
  methods: {
    onFileChange(e) {
      this.file1 = e.target.files[0];
    },
    handleOkUpload(bvModalEvent) {
      // Prevent modal from closing
      bvModalEvent.preventDefault()
      // Trigger submit handler
      this.handleSubmitUpload()
    },
    handleSubmitUpload() {
      this.loading = true
      const data = new FormData();
      data.append('berita_acara', this.file1);
      data.append('id', this.id);
      data.append('user_id', this.user.user_id)
      var ini = this
      this.$http.post('/uploads/berita-acara', data).then(function (response) {
        ini.loading = false
        let data = response.data
        ini.fileState = null
        if(data.errors){
          ini.fileState = (data.errors.berita_acara) ? false : null
          ini.feedback_file = (data.errors.berita_acara) ? data.errors.berita_acara[0] : ''
        } else {
          ini.$swal({
            icon: data.icon,
            title: data.title,
            html: data.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            ini.$bvModal.hide('upload-berita-acara')
          })
        }
      }).catch(function (error) {
        console.log(error);
        ini.fileState = false
        ini.feedback_file = 'Isian salah. Silahkan periksa kembali!!!'
      })
    },
    getBeritaAcara(){
      this.$http.get('/referensi/berita-acara', {
        params: {
          jenis: 'distribusi'
        }
      }).then(response => {
        this.data = response.data.berita_acara
        if(this.data){
          this.id = this.data.id
          this.form_tanggal = this.data.tanggal
          this.nama = this.data.nama
          this.instansi = this.data.instansi
          this.jabatan = this.data.jabatan
          this.negara = this.data.negara
          this.provinsi = this.data.provinsi
          this.kabupaten = this.data.kabupaten
          this.link_cetak = app_url+'/cetak/berita-acara/pendistribusian/'+this.user.user_id
        } else {
          if(this.hasRole('sekolah')){
            this.jabatan = 'Kepala Sekolah'
            this.instansi = (this.user.sekolah) ? this.user.sekolah.nama : ''
            this.kabupaten = (this.user.sekolah) ? this.user.sekolah.kabupaten : ''
            this.provinsi = (this.user.sekolah) ? this.user.sekolah.provinsi : ''
          } else if(this.hasRole('disprov')){
            this.jabatan = 'Kepala Dinas Pendidikan Provinsi'
            this.instansi = 'Dinas Pendidikan Provinsi'
            this.provinsi = (this.user.wilayah) ? this.user.wilayah.nama : ''
          } else {
            this.jabatan = 'Kepala Cabang Dinas Pendidikan '+this.user.wilayah.parent.nama
            this.instansi = 'Cabang Dinas Pendidikan '+this.user.wilayah.parent.nama
            this.kabupaten = (this.user.wilayah) ? this.user.wilayah.nama : ''
            this.provinsi = (this.user.wilayah) ? this.user.wilayah.parent.nama : ''
          }
        }
        let blangko_3_tahun = response.data.blangko_3_tahun
        if(blangko_3_tahun){
          this.jumlah_3 = blangko_3_tahun.jumlah
          this.start_3 = blangko_3_tahun.start
          this.end_3 = blangko_3_tahun.end
        }
        let blangko_4_tahun = response.data.blangko_4_tahun
        if(blangko_4_tahun){
          this.jumlah_4 = blangko_4_tahun.jumlah
          this.start_4 = blangko_4_tahun.start
          this.end_4 = blangko_4_tahun.end
        }
        let waktu = response.data.waktu
        this.hari = waktu.hari
        this.tanggal = waktu.tanggal
        this.bulan = waktu.bulan
        this.tahun = waktu.tahun
        this.baru = response.data.baru
        this.fire = (this.user.fire !== '0') ? true : false
        this.isBusy = false
      })
    },
    resetModal() {
      this.form_tanggal = ''
      this.nama = ''
      this.instansi = ''
      this.jabatan = ''
      this.provinsi = ''
      this.kabupaten = ''
      this.nama_2 = ''
      this.instansi_2 = ''
      this.jabatan_2 = ''
      this.provinsi_2 = ''
      this.kabupaten_2 = ''
      this.negara = ''
      this.tanggalState = null
      this.namaState = null
      this.jabatanState = null
      this.instansiState = null
      this.provinsiState = null
      this.kabupatenState = null
      this.nama_2State = null
      this.jabatan_2State = null
      this.instansi_2State = null
      this.provinsi_2State = null
      this.kabupaten_2State = null
      this.negaraState = null
      this.getBeritaAcara()
    },
    handleOk(bvModalEvent) {
      // Prevent modal from closing
      bvModalEvent.preventDefault()
      // Trigger submit handler
      this.handleSubmit()
    },
    handleSubmit() {
      this.$http.post('/referensi/berita-acara', {
        user_id: this.user.user_id,
        jenis: 'distribusi',
        tanggal: this.form_tanggal,
        nama: this.nama,
        instansi: this.instansi,
        jabatan: this.jabatan,
        provinsi: this.provinsi,
        kabupaten: this.kabupaten,
        negara: this.negara,
      }).then(response => {
        let data = response.data
        if(data.errors){
          this.feedback_tanggal = (data.errors.tanggal) ? data.errors.tanggal[0] : ''
          this.feedback_nama = (data.errors.nama) ? data.errors.nama[0] : ''
          this.feedback_jabatan = (data.errors.jabatan) ? data.errors.jabatan[0] : ''
          this.feedback_instansi = (data.errors.instansi) ? data.errors.instansi[0] : ''
          this.feedback_provinsi = (data.errors.provinsi) ? data.errors.provinsi[0] : ''
          this.feedback_kabupaten = (data.errors.kabupaten) ? data.errors.kabupaten[0] : ''
          this.feedback_negara = (data.errors.negara) ? data.errors.negara[0] : ''
          this.tanggalState = (data.errors.tanggal) ? false : null
          this.namaState = (data.errors.nama) ? false : null
          this.jabatanState = (data.errors.jabatan) ? false : null
          this.instansiState = (data.errors.instansi) ? false : null
          this.pengawasState = (data.errors.pengawas) ? false : null
          this.negaraState = (data.errors.negara) ? false : null
          this.provinsiState = (data.errors.provinsi) ? false : null
          this.kabupatenState = (data.errors.kabupaten) ? false : null
        } else {
          this.$swal({
            icon: data.icon,
            title: data.title,
            text: data.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.$bvModal.hide('berita-acara')
            this.getBeritaAcara();
          })
        }
      })
    },
    loadPostsData() {
      //let current_page = this.search == '' ? this.current_page : this.current_page != 1 ? 1 : this.current_page
      let current_page = this.current_page//this.search == '' ? this.current_page : 1
      //LAKUKAN REQUEST KE API UNTUK MENGAMBIL DATA POSTINGAN
      this.$http.get('/berita-acara', {
        params: {
          jenis: 'ganti',
          tahun_ajaran_id: 2022,
          page: current_page,
          per_page: this.per_page,
          q: this.search,
          sortby: this.sortBy,
          sortbydesc: this.sortByDesc ? 'DESC' : 'ASC',
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
        //this.optionsKabupaten = response.data.wilayah
        this.optionSekolah = response.data.sekolah
      })
    },
    handlePerPage(val) {
      this.per_page = val //SET PER_PAGE DENGAN VALUE YANG DIKIRIM DARI EMIT
      this.loadPostsData() //DAN REQUEST DATA BARU KE SERVER
    },
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