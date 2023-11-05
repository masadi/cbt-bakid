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
        <b-table striped :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" show-empty :busy="isBusy">
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>
            <template v-slot:cell(ttl)="row">
                {{row.item.tempat_lahir}}, {{row.item.tanggal_lahir_str}}
            </template>
            <template v-slot:cell(no)="row">
                {{row.index + meta.from}}
            </template>
            <template v-slot:cell(actions)="row">
                <!--b-button variant="danger" size="sm" @click="batalkan(row.item)">Batalkan</b-button-->
                <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="success" size="sm">
                    <b-dropdown-item href="javascript:" @click="confirm(row.item, 'batalkan')"><font-awesome-icon icon="fa-solid fa-circle-check" /> Batalkan</b-dropdown-item>
                    <b-dropdown-item href="javascript:" @click="confirm(row.item, 'kembalikan')"><font-awesome-icon icon="fa-solid fa-circle-xmark" /> Kembalikan</b-dropdown-item>
                    <b-dropdown-item href="javascript:" @click="confirm(row.item, 'pengganti')"><font-awesome-icon icon="fa-solid fa-circle-exclamation" /> Nomor Ijazah Pengganti</b-dropdown-item>
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
        <b-modal id="modal-prevent-closing" ref="modal" title="Tambah Data Penggantian Blangko Ijazah" @show="resetModal" @hidden="resetModal" @ok="handleOk">
            <form ref="form" @submit.stop.prevent="handleSubmit">
                <b-form-group label="Nomor Ijazah Lama" label-for="nomor_seri" :invalid-feedback="feedback_nomor_seri" :state="nomor_seriState">
                <b-form-input id="nomor_seri" placeholder="M-SMK/K13-x/23/xxxxxxx" v-model="form.nomor_seri" :state="nomor_seriState" required></b-form-input>
                </b-form-group>
                <b-form-group label="Nomor Ijazah Baru" label-for="nomor_seri_baru" :invalid-feedback="feedback_nomor_seri_baru" :state="nomor_seri_baruState">
                <b-form-input id="nomor_seri" placeholder="M-SMK/K13-x/23/xxxxxxx" v-model="form.nomor_seri_baru" :state="nomor_seri_baruState" required></b-form-input>
                </b-form-group>
                <b-form-group label="Jenis Pengembalian" label-for="jenis_pengembalian_id" :invalid-feedback="feedback_jenis_pengembalian_id" :state="jenis_pengembalian_id_baruState">
                <b-form-select id="jenis_pengembalian_id" v-model="form.jenis_pengembalian_id" :options="jenis_pengembalian"></b-form-select>
                </b-form-group>
                <b-form-group label="Keterangan" label-for="keterangan" :invalid-feedback="feedback_keterangan" :state="keteranganState">
                <b-form-input id="keterangan" v-model="form.keterangan" :state="keteranganState"></b-form-input>
                </b-form-group>
            </form>
        </b-modal>
    </div>
</template>

<script>
import _ from 'lodash' //IMPORT LODASH, DIMANA AKAN DIGUNAKAN UNTUK MEMBUAT DELAY KETIKA KOLOM PENCARIAN DIISI
import { BRow, BCol, BTable, BSpinner, BPagination, BButton, BDropdown, BDropdownItem, BFormGroup, BFormInput, BFormSelect } from 'bootstrap-vue'
export default {
    components: {
        BRow,
        BCol,
        BTable,
        BSpinner,
        BPagination,
        BButton,
        BDropdown, 
        BDropdownItem,
        BFormGroup, 
        BFormInput, 
        BFormSelect,
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
            jenis_pengembalian: [
                { value: null, text: '== Pilih Jenis Pengembalian ==' },
                { value: 'rusak', text: 'Rusak' },
                { value: 'kesalahan-cetakan', text: 'Kesalahan Cetakan' },
                { value: 'kesalahan-penulisan', text: 'Kesalahan Penulisan' },
                { value: 'hilang', text: 'Hilang' },
                { value: 'lainnya', text: 'Lainnya' },
            ],
            form: {
                nomor_seri: '',
                nomor_seri_baru: '',
                jenis_pengembalian_id: null,
                keterangan: '',
            },
            feedback_jenis_pengembalian_id: '',
            feedback_nomor_seri: 'Nomor Ijazah Lama tidak boleh kosong!',
            feedback_nomor_seri_baru: 'Nomor Ijazah Baru tidak boleh kosong!',
            feedback_keterangan: 'Keterangan tidak boleh kosong!',
            nomor_seriState: null,
            nomor_seri_baruState: null,
            jenis_pengembalian_id_baruState: null,
            keteranganState: null,
            kode_wilayah: null,
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
        confirm(item, aksi){
            if(aksi == 'batalkan'){
                this.batalkan(item)
            }
            if(aksi == 'pengganti'){
                this.form.nomor_seri = item.nomor_seri
                this.$refs['modal'].show()
                this.form.nomor_seri = item.nomor_seri
            }
            if(aksi == 'kembalikan'){
                this.kembalikan(item)
            }
        },
        kembalikan(item){
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
                    this.$http.post('/referensi/blangko', {
                        permintaan: 'kembali',
                        nomor_seri: item.nomor_seri,
                        sekolah_id: this.user.sekolah_id,
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
                                this.loadPerPage(this.meta.per_page);
                            })
                    });
                }
            })
        },
        batalkan(item){
            this.$swal({
                title: 'Apakah Anda yakin?',
                text: 'Aksi ini akan membatalkan Penggantian Blangko Ijazah!',
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
                    this.$http.post('/referensi/batalkan-blangko', {
                        status: 1,
                        nomor_seri: item.nomor_seri,
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
                                this.loadPerPage(this.meta.per_page);
                            })
                    });
                }
            })
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
        //asf
        search: _.debounce(function (e) {
            //KIRIM EMIT DENGAN NAMA SEARCH DAN VALUE SESUAI YANG DIKETIKKAN OLEH USER
            this.$emit('search', e.target.value)
        }, 500),
        baseName(str){
            var base = new String(str).substring(str.lastIndexOf('/') + 1); 
                if(base.lastIndexOf(".") != -1)       
                    base = base.substring(0, base.lastIndexOf("."));
            return base;
        },
        resetModal() {
            this.form.nomor_seri = ''
            this.form.nomor_seri_baru = ''
            this.nomor_seriState = null
            this.nomor_seri_baruState = null
            this.form.keterangan = ''
            this.keteranganState = null
        },
        handleOk(bvModalEvent) {
            // Prevent modal from closing
            bvModalEvent.preventDefault()
            // Trigger submit handler
            this.handleSubmit()
        },
        handleSubmit() {
            this.$http.post('/blangko/simpan', {
                permintaan: 'ganti',
                nomor_seri: this.form.nomor_seri,
                nomor_seri_baru: this.form.nomor_seri_baru,
                start: parseInt(this.baseName(this.form.nomor_seri_baru)),
                jenis_pengembalian_id: this.form.jenis_pengembalian_id, 
                keterangan: this.form.keterangan,
                sekolah_id: this.user.sekolah_id,
                kode_wilayah: this.kode_wilayah,
            }).then(response => {
                let data = response.data
                if(data.errors){
                this.nomor_seriState = (data.errors.nomor_seri) ? false : true
                this.nomor_seri_baruState = (data.errors.nomor_seri_baru) ? false : true
                this.jenis_pengembalian_id_baruState = (data.errors.jenis_pengembalian_id) ? false : true
                this.feedback_nomor_seri = (data.errors.nomor_seri) ? data.errors.nomor_seri[0] : null
                this.feedback_nomor_seri_baru = (data.errors.nomor_seri_baru) ? data.errors.nomor_seri_baru[0] : null
                this.feedback_jenis_pengembalian_id = (data.errors.jenis_pengembalian_id) ? data.errors.jenis_pengembalian_id[0] : null
                } else {
                this.$swal({
                    icon: data.icon,
                    title: data.title,
                    text: data.text,
                    customClass: {
                    confirmButton: 'btn btn-success',
                    },
                }).then(result => {
                    this.$bvModal.hide('modal-prevent-closing')
                    this.loadPostsData()
                })
                }
            })
        },
    }
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>