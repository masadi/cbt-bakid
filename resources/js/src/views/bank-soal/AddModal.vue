<template>
  <b-modal v-model="addModalShow" title="Tambah Mata Ujian" @show="resetForm" @hidden="hideModal" @ok="handleOk" size="lg">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-button block variant="primary" href="/downloads/template-soal.xlsx" target="_blank">Download Template Excel</b-button>
          </b-col>
          <b-col cols="12" class="mt-2">
            <b-form-group label="Mata Pelajaran" label-for="pembelajaran_id" label-cols-md="3" :invalid-feedback="feedback.pembelajaran_id" :state="state.pembelajaran_id">
              <v-select id="pembelajaran_id" v-model="form.pembelajaran_id" :reduce="text => text.value" label="text" :options="data_mapel" placeholder="== Pilih Mata Pelajaran ==" :state="state.status">
                <template #no-options="{ search, searching, loading }">
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Nama Mata Ujian" label-for="nama" label-cols-md="3" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input id="nama" v-model="form.nama" :state="state.nama" placeholder="Ketik nama mata ujian (UTS/UAS/DLL)"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Status Ujian" label-for="status" label-cols-md="3" :invalid-feedback="feedback.status" :state="state.status">
              <v-select id="status" v-model="form.status" :reduce="name => name.code" label="name" :options="[{name: 'Aktif', code: 1}, {name: 'Tidak Aktif', code: 0}]" placeholder="== Pilih Status Ujian ==" :state="state.status">
                <template #no-options="{ search, searching, loading }">
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="File ZIP" label-for="file_zip" label-cols-md="3" :invalid-feedback="feedback.file_zip" :state="state.file_zip">
              <b-form-file v-model="form.file_zip" :state="state.file_zip" placeholder="Choose a file or drop it here..." drop-placeholder="Drop file here..." @change="onFileChange"></b-form-file>
            </b-form-group>  
          </b-col>
        </b-row>
      </b-form>
    </b-overlay>
    <template #modal-footer="{ ok, cancel }">
        <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="secondary" class="d-inline-block">
          <b-button @click="cancel()">Tutup</b-button>
        </b-overlay>
        <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="success" class="d-inline-block">
          <b-button variant="success" @click="ok()">Proses</b-button>
        </b-overlay>
      </template>
  </b-modal>
</template>

<script>
import { BOverlay, BForm, BFormGroup, BFormFile, BRow, BCol, BButton, BFormInput, } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    BOverlay,
    BForm,
    BFormGroup,
    BFormFile,
    BRow,
    BCol,
    BButton,
    vSelect,
    BFormInput,
  },
  data() {
    return {
      addModalShow: false,
      loading_form: false,
      data_mapel: [],
      form: {
        pembelajaran_id: '',
        nama: '',
        status: '',
        file_zip: null,
      },
      feedback: {
        pembelajaran_id: '',
        nama: '',
        status: '',
        file_zip: '',
      },
      state: {
        pembelajaran_id: null,
        nama: null,
        status: null,
        file_zip: null,
      },
    }
  },
  created() {
    //this.form.user_id = this.user.user_id
    //this.form.sekolah_id = this.user.sekolah_id
    //this.form.semester_id = this.user.semester.semester_id
    //this.form.periode_aktif = this.user.semester.nama
    eventBus.$on('open-add-modal', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.data_mapel = data
      this.addModalShow = true
    },
    hideModal(){
      this.addModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.file1 = null
    },
    onFileChange(e) {
      this.file1 = e.target.files[0];
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      const data = new FormData();
      data.append('nama', this.form.nama??'');
      data.append('status', this.form.status??'');
      data.append('pembelajaran_id', this.form.pembelajaran_id??'');
      data.append('file_zip', this.form.file_zip??'');
      data.append('user_id', this.user.user_id)
      this.$http.post('/bank-soal/upload', data).then(response => {
        this.loading_form = false
        let getData = response.data
        console.log(getData);
        if(getData.errors){
          this.feedback.file_zip = (getData.errors.file_zip) ? getData.errors.file_zip.join(', ') : ''
          this.state.file_zip = (getData.errors.file_zip) ? false : null
          this.feedback.status = (getData.errors.status) ? getData.errors.status.join(', ') : ''
          this.state.status = (getData.errors.status) ? false : null
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
          this.state.nama = (getData.errors.nama) ? false : null
          this.feedback.pembelajaran_id = (getData.errors.pembelajaran_id) ? getData.errors.pembelajaran_id.join(', ') : ''
          this.state.pembelajaran_id = (getData.errors.pembelajaran_id) ? false : null
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            html: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.hideModal()
            this.$emit('reload')
          })
        }
      }).catch(error => {
        console.log(error);
      })
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>