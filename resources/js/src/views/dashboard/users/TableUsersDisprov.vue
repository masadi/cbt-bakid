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
            <template v-slot:cell(kabupaten)="row">
                {{ row.item.kcd.map(entry => entry.wilayah.nama).join(', ') }}
            </template>
            <template v-slot:cell(actions)="row">
                <!--b-button variant="danger" size="sm" @click="resetPassword(row.item)">Reset Password</b-button-->
                <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="success" size="sm">
                    <b-dropdown-item href="javascript:" @click="resetPassword(row.item)"><i class="fas fa-check"></i> Reset Password</b-dropdown-item>
                    <b-dropdown-item href="javascript:" @click="hapusData(row.item)"><i class="fas fa-times"></i> Hapus</b-dropdown-item>
                </b-dropdown>
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
        <b-modal ref="modal-ijazah" title="Input Nomor Seri Ijazah" @show="resetModal" @hidden="resetModal" @ok="handleOk">
            <form ref="form" @submit.stop.prevent="handleSubmit">
                <b-form-group label="Nomor Seri Ijazah" label-for="nomor_seri" :invalid-feedback="feedback" :state="nomor_seriState">
                    <b-input-group>
                        <b-input-group-prepend is-text><b>{{kode_ijazah}}</b></b-input-group-prepend>
                        <b-form-input id="nomor_seri" v-model="nomor_seri" :state="nomor_seriState" required></b-form-input>
                    </b-input-group>
                </b-form-group>
                <b-form-group label="Tanggal Terbit Ijazah" label-for="tanggal" :invalid-feedback="feedback_tanggal" :state="tanggalState">
                    <!--b-form-input id="tanggal" v-model="tanggal" :state="tanggalState" required></b-form-input-->
                    <b-form-datepicker id="tanggal" v-model="tanggal" :state="tanggalState" locale="id-ID"></b-form-datepicker>
                </b-form-group>
            </form>
        </b-modal>
    </div>
</template>

<script>
import _ from 'lodash' //IMPORT LODASH, DIMANA AKAN DIGUNAKAN UNTUK MEMBUAT DELAY KETIKA KOLOM PENCARIAN DIISI
import { BRow, BCol, BTable, BSpinner, BPagination, BDropdown, BDropdownItem, BFormGroup, BFormInput, BInputGroup, BInputGroupPrepend, BFormDatepicker } from 'bootstrap-vue'
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
        BFormDatepicker
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
        resetPassword(item){
            this.$swal({
                title: 'Apakah Anda yakin?',
                text: 'Aksi ini tidak dapat dikembalikan!',
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
                        user_id: item.user_id,
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
        hapusData(item){
            this.$swal({
                title: 'Apakah Anda yakin?',
                text: 'Aksi ini tidak dapat dikembalikan!',
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
                    this.$http.post('/referensi/users/hapus', {
                        user_id: item.user_id,
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
        ijazah(item){
            this.$refs['modal-ijazah'].show()
            this.peserta_didik_id = item.peserta_didik_id
            let kode = (item.anggota_rombel.rombongan_belajar.tingkat == 12) ? 3 : 4
            this.kode_ijazah = 'M-SMK/K13-'+kode+'/23/'
            this.tahun = kode
            if(item.blangko){
                this.nomor_seri = item.blangko.urut
                this.tanggal = item.blangko.tanggal
            }
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
            if (!this.checkFormValidity()) {
                return
            }
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