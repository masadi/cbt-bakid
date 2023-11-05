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
        <!-- BLOCK INI AKAN MENGHASILKAN LIST DATA DALAM BENTUK TABLE MENGGUNAKAN COMPONENT TABLE DARI BOOTSTRAP VUE -->

        <!-- :ITEMS ADALAH DATA YANG AKAN DITAMPILKAN -->
        <!-- :FIELDS AKAN MENJADI HEADER DARI TABLE, MAKA BERISI FIELD YANG SALING BERKORELASI DENGAN ITEMS -->
        <!-- :sort-by.sync & :sort-desc.sync AKAN MENGHANDLE FITUR SORTING -->
        <b-table responsive striped :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" show-empty :busy="isBusy">
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>
            <template v-slot:cell(ttl)="row">
                {{row.item.tempat_lahir}}, {{row.item.tanggal_lahir_str}}
            </template>
            <template v-slot:cell(no_induk)="row">
                {{row.item.nisn}}
            </template>
            <template v-slot:cell(kurikulum)="row">
                {{(row.item.tahun == 3) ? 'K13-3' : 'K13-4'}}
            </template>
            <template v-slot:cell(kompetensi)="row">
                {{(row.item.anggota_rombel.rombongan_belajar.jurusan_sp) ? row.item.anggota_rombel.rombongan_belajar.jurusan_sp.nama_jurusan_sp : '-'}}
            </template>
            <template v-slot:cell(no)="row">
                {{row.index + meta.from}}
            </template>
            <template v-slot:cell(ijazah)="row">
                {{(row.item.blangko) ? row.item.blangko.nomor_seri : ''}}
            </template>
            <template v-slot:cell(tanggal)="row">
                {{(row.item.blangko) ? row.item.blangko.tanggal_str : ''}}
            </template>
            <template v-slot:cell(actions)="row">
                <template v-if="row.item.blangko && row.item.blangko.valid">
                    <b-button variant="primary" size="sm" @click="preview(row.item.blangko)"><font-awesome-icon icon="fa-solid fa-magnifying-glass" /> Lihat Berkas</b-button>
                </template>
                <template v-else>
                    <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="success" size="sm">
                        <b-dropdown-item href="javascript:" @click="batalkan(row.item)"><font-awesome-icon icon="fa-solid fa-xmark" /> Batalkan</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="ijazah(row.item)"><font-awesome-icon icon="fa-solid fa-file-circle-check" /> Nomor Ijazah</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="upload(row.item.blangko)" v-if="row.item.blangko"><font-awesome-icon icon="fa-solid fa-cloud-arrow-up" /> Unggah Scan PDF</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="preview(row.item.blangko)" v-if="row.item.blangko && row.item.blangko.berkas"><font-awesome-icon icon="fa-solid fa-magnifying-glass" /> Lihat Berkas</b-dropdown-item>
                    </b-dropdown>
                </template>
            </template>
        </b-table>

        <!-- BAGIAN INI AKAN MENAMPILKAN JUMLAH DATA YANG DI-LOAD -->
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
        <b-modal ref="modal-ijazah" title="Input Nomor Ijazah" @hidden="resetModal">
            <template #modal-footer="{ ok, cancel }">
                <b-button variant="danger" @click="cancel()" v-if="!loading">
                    Tutup
                </b-button>
                <b-button variant="success" @click="handleOk" v-if="!loading">
                    Simpan
                </b-button>
                <b-button variant="success" disabled v-if="loading">
                    <b-spinner small></b-spinner>
                    <span class="sr-only">Loading...</span>
                </b-button>
            </template>
            <form ref="form" @submit.stop.prevent="handleSubmit">
                <!--b-form-group label="Nomor Ijazah" label-for="nomor_seri" :invalid-feedback="feedback" :state="nomor_seriState">
                    <b-input-group>
                        <b-input-group-prepend is-text><b>{{kode_ijazah}}</b></b-input-group-prepend>
                        <b-form-input id="nomor_seri" v-model="nomor_seri" :state="nomor_seriState" required></b-form-input>
                    </b-input-group>
                </b-form-group-->
                <b-form-group label="Nomor Ijazah" label-for="nomor_seri" :invalid-feedback="feedback" :state="nomor_seriState">
                    <form-select-dropdown :tahun="tahun" :nomor_seri="nomor_seri" :peserta_didik_id="peserta_didik_id" @selected="handleSelected" :state="nomor_seriState" />
                </b-form-group>
                <b-form-group label="Tanggal Terbit Ijazah" label-for="tanggal" :invalid-feedback="feedback_tanggal" :state="tanggalState">
                    <b-form-datepicker id="tanggal" v-model="tanggal" :state="tanggalState" locale="id-ID"></b-form-datepicker>
                </b-form-group>
            </form>
        </b-modal>
        <b-modal ref="upload-berkas" title="Unggah Scan PDF Blangko Ijazah">
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
        <b-modal ref="modal-preview" size="xl" hide-footer title="Pratinjau">
            <iframe :src="previewUrl" width="100%" height="600"></iframe>
        </b-modal>
    </div>
</template>

<script>
import _ from 'lodash' //IMPORT LODASH, DIMANA AKAN DIGUNAKAN UNTUK MEMBUAT DELAY KETIKA KOLOM PENCARIAN DIISI
import FormSelectDropdown from '@/views/forms/form-element/form-select/FormSelectDropdown.vue'
import { BRow, BCol, BTable, BSpinner, BPagination, BDropdown, BDropdownItem, BFormGroup, BFormInput, BInputGroup, BInputGroupPrepend, BFormDatepicker, BFormFile, BButton } from 'bootstrap-vue'
export default {
    components: {
        BRow,
        BCol,
        BTable,
        BSpinner,
        BPagination,
        BDropdown, 
        BDropdownItem,
        BFormGroup,
        BFormInput,
        BInputGroup, 
        BInputGroupPrepend,
        BFormDatepicker,
        BFormFile,
        BButton,
        FormSelectDropdown
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
            previewUrl: '',
            loading: false,
            file1: null,
            fileState: null,
            feedback_file: '',
            blangko_id: '',
            nomor_seri: '',
            tanggal: '',
            tahun: '',
            peserta_didik_id: '',
            nomor_seriState: null,
            tanggalState: null,
            //isBusy: true,
            //VARIABLE INI AKAN MENGHADLE SORTING DATA
            sortBy: null, //FIELD YANG AKAN DISORT AKAN OTOMATIS DISIMPAN DISINI
            sortDesc: false, //SEDANGKAN JENISNYA ASCENDING ATAU DESC AKAN DISIMPAN DISINI
            //TAMBAHKAN DUA VARIABLE INI UNTUK MENGHANDLE MODAL DAN DATA YANG AKAN DIHAPUS
            kode_ijazah: '',
            feedback: 'Nomor Urut Ijazah tidak boleh kosong',
            feedback_tanggal: 'Tanggal Terbit Ijazah tidak boleh kosong',
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
        preview(item){
            var ini = this
            this.$http.get('/blangko/preview', {
                params: {
                    npsn: this.user.username,
                    filename: item.berkas,
                }
            }).then(response => {
                let getData = response.data.data
                if(getData){
                this.previewUrl = 'https://drive.google.com/file/d/'+getData.file_id+'/preview'
                this.$refs['modal-preview'].show()
                } else {
                ini.$swal({
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
        upload(item){
            this.blangko_id = item.id
            console.log(item);
            this.$refs['upload-berkas'].show()
        },
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
            data.append('scan_pdf', this.file1);
            data.append('blangko_id', this.blangko_id);
            data.append('user_id', this.user.user_id)
            var ini = this
            this.$http.post('/uploads/scan-pdf', data).then(function (response) {
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
                    ini.$refs['upload-berkas'].hide()
                    ini.loadPerPage(ini.meta.per_page)
                })
                }
            }).catch(function (error) {
                console.log(error);
                ini.fileState = false
                ini.feedback_file = 'Isian salah. Silahkan periksa kembali!!!'
            })
        },
        batalkan(item){
            this.$swal({
                title: 'Apakah Anda yakin?',
                text: 'Aksi ini akan membatalkan Peserta Didik Lulus!',
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
                    this.$http.post('/referensi/batalkan-pd', {
                        status: 'lulus',
                        peserta_didik_id: item.peserta_didik_id,
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
        ijazah(item){
            this.$refs['modal-ijazah'].show()
            this.peserta_didik_id = item.peserta_didik_id
            let kode = (item.anggota_rombel.rombongan_belajar.tingkat == 12) ? 3 : 4
            this.kode_ijazah = 'M-SMK/K13-'+kode+'/23/'
            this.tahun = kode
            if(item.blangko){
                this.nomor_seri = item.blangko.nomor_seri
                this.tanggal = item.blangko.tanggal
            } else {
                this.nomor_seri = ''
                this.tanggal = ''
            }
            this.nomor_seriState = null
            this.feedback = ''
            this.tanggalState = null
            this.feedback_tanggal = ''
        },
        checkFormValidity() {
            const valid = this.$refs.form.checkValidity()
            this.nomor_seriState = valid
            this.tanggalState = valid
            return valid
        },
        resetModal(){
            this.nomor_seri = ''
            this.tanggal = ''
            this.peserta_didik_id = ''
            this.nameState = null
        },
        handleOk(bvModalEvent){
            bvModalEvent.preventDefault()
            this.handleSubmit()
        },
        handleSubmit() {
            this.loading = true
            // Push the name to submitted names
            //this.submittedNames.push(this.name)
            // Hide the modal manually
            this.$http.post('/referensi/nomor-ijazah', {
                urut: this.nomor_seri,
                tanggal: this.tanggal,
                nomor_seri: this.kode_ijazah+this.nomor_seri,
                peserta_didik_id: this.peserta_didik_id,
                sekolah_id: this.user.sekolah_id,
                tahun: this.tahun,
            }).then(response => {
                this.loading = false
                let data = response.data
                if(data.errors){
                    if(data.errors.urut){
                        this.nomor_seriState = false
                        this.feedback = data.errors.urut[0]
                    }
                    if(data.errors.tanggal){
                        this.tanggalState = false
                        this.feedback_tanggal = data.errors.tanggal[0]
                    }
                } else {
                    this.nomor_seriState = null
                    this.feedback = ''
                    this.tanggalState = null
                    this.feedback_tanggal = ''
                    this.$swal({
                        icon: data.icon,
                        title: data.title,
                        text: data.text,
                        customClass: {
                            confirmButton: 'btn btn-success',
                        },
                    }).then(result => {
                        this.$refs['modal-ijazah'].hide ()
                        this.loadPerPage(this.meta.per_page);
                    })
                }
            });
            //this.$nextTick(() => {
                //this.$refs['modal-ijazah'].hide ()
            //})
        },
        //JIKA SELECT BOX DIGANTI, MAKA FUNGSI INI AKAN DIJALANKAN
        loadPerPage(val) {
            //DAN KITA EMIT LAGI DENGAN NAMA per_page DAN VALUE SESUAI PER_PAGE YANG DIPILIH
            this.$emit('per_page', this.meta.per_page)
        },
        //KETIKA PAGINATION BERUBAH, MAKA FUNGSI INI AKAN DIJALANKAN
        changePage(val) {
            //KIRIM EMIT DENGAN NAMA PAGINATION DAN VALUENYA ADALAH HALAMAN YANG DIPILIH OLEH USER
            this.$emit('pagination', val)
        },
        handleSelected(val){
            this.nomor_seri = val.nomor_seri
        },
        //KETIKA KOTAK PENCARIAN DIISI, MAKA FUNGSI INI AKAN DIJALANKAN
        //KITA GUNAKAN DEBOUNCE UNTUK MEMBUAT DELAY, DIMANA FUNGSI INI AKAN DIJALANKAN
        //500 MIL SECOND SETELAH USER BERHENTI MENGETIK
        search: _.debounce(function (e) {
            //KIRIM EMIT DENGAN NAMA SEARCH DAN VALUE SESUAI YANG DIKETIKKAN OLEH USER
            this.$emit('search', e.target.value)
        }, 500),
    }
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>