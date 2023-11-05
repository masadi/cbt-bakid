<template>
  <b-modal v-model="editModalShow" title="Edit Data Peserta Didik" size="xl" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Nama Lengkap" label-for="nama" label-cols-md="3" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input id="nama" v-model="form.nama" :state="state.nama" placeholder="Nama Lengkap dengan gelar"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="NISN" label-for="nisn" label-cols-md="3" :invalid-feedback="feedback.nisn" :state="state.nisn">
              <b-form-input id="nisn" v-model="form.nisn" placeholder="NISN"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Jenis Kelamin" label-for="jenis_kelamin" label-cols-md="3" :invalid-feedback="feedback.jenis_kelamin" :state="state.jenis_kelamin">
              <v-select id="jenis_kelamin" v-model="form.jenis_kelamin" :reduce="name => name.code" label="name" :options="[{name: 'Laki-laki', code: 'L'}, {name: 'Perempuan', code: 'P'}]" placeholder="== Pilih Jenis Kelamin ==" :state="state.jenis_kelamin">
                <template #no-options="{ search, searching, loading }">
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Tempat Lahir" label-for="tempat_lahir" label-cols-md="3" :invalid-feedback="feedback.tempat_lahir" :state="state.tempat_lahir">
              <b-form-input id="nama" v-model="form.tempat_lahir" :state="state.tempat_lahir" placeholder="Tempat Lahir"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Tanggal Lahir*" label-for="tanggal_lahir" label-cols-md="3" :invalid-feedback="feedback.tanggal_lahir" :state="state.tanggal_lahir">
              <!--b-form-datepicker v-model="form.tanggal_lahir" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="tanggal_lahir" @context="onContext" placeholder="== Pilih Tanggal Lahir ==" :state="state.tanggal_lahir" /-->
              <b-input-group>
                <b-form-input id="tanggal_lahir" v-model="form.tanggal_lahir" type="text" placeholder="YYYY-MM-DD" autocomplete="off" show-decade-nav></b-form-input>
                <b-input-group-append>
                  <b-form-datepicker v-model="form.tanggal_lahir" show-decade-nav button-only right locale="id" aria-controls="tanggal_lahir" @context="onContext"></b-form-datepicker>
                </b-input-group-append>
              </b-input-group>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Foto Siswa" label-for="image" label-cols-md="3" :invalid-feedback="feedback.image" :state="state.image">
              <b-form-file id="image" accept=".jpg, .png" v-model="form.image" :state="state.image" placeholder="Upload Foto Siswa..." drop-placeholder="Drop file here..." @change="onFileChange"></b-form-file>
            </b-form-group>
          </b-col>
          <b-col cols="7" offset="3" v-if="preview_url">
            <b-img rounded v-bind="mainProps" :src="preview_url" alt="Foto Siswa"></b-img>
          </b-col>
        </b-row>
      </b-form>
    </b-overlay>
    <template #modal-footer="{ ok, cancel }">
        <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="secondary" class="d-inline-block">
          <b-button @click="cancel()">Tutup</b-button>
        </b-overlay>
        <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="success" class="d-inline-block">
          <b-button variant="success" @click="ok()">Perbaharui</b-button>
        </b-overlay>
      </template>
  </b-modal>
</template>

<script>
import { BOverlay, BForm, BFormInput, BInputGroup, BRow, BCol, BFormGroup, BFormFile, BButton, BFormDatepicker, BImg, BInputGroupAppend } from 'bootstrap-vue'
import vSelect from 'vue-select'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BOverlay,
    BForm,
    BFormInput,
    BInputGroup,
    BRow,
    BCol,
    BFormGroup,
    BFormFile,
    BButton,
    BFormDatepicker,
    BImg,
    BInputGroupAppend,
    vSelect,
  },
  data() {
    return {
      editModalShow: false,
      loading_form: false,
      form: {
        peserta_didik_id: '',
        nama: '',
        nisn: '',
        jenis_kelamin: '',
        tempat_lahir: '',
        tanggal_lahir: '',
        image: null,
      },
      feedback: {
        nama: '',
        nisn: '',
        jenis_kelamin: '',
        tempat_lahir: '',
        tanggal_lahir: '',
        image: '',
      },
      state: {
        nama: null,
        nisn: null,
        jenis_kelamin: null,
        tempat_lahir: null,
        tanggal_lahir: null,
        image: null,
      },
      mainProps: {width: 125, height: 125 },
      preview_url: null,
    }
  },
  created() {
    eventBus.$on('open-modal', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      if(data.id == 'edit'){
        this.editModalShow = true
        this.form.peserta_didik_id = data.item.peserta_didik_id
        this.form.nama = data.item.nama
        this.form.nisn = data.item.nisn
        this.form.jenis_kelamin = data.item.jenis_kelamin
        this.form.tempat_lahir = data.item.tempat_lahir
        this.form.tanggal_lahir = data.item.tanggal_lahir
        //this.preview_url = data.item
      }
      console.log(data);
      /*this.$http.post('/referensi/detil-data', {
        id: data.peserta_didik_id,
        data: 'pd',
        edit: 1,
      }).then(response => {
        var getData = response.data
        if(getData.errors){
          this.$swal({
            icon: 'error',
            title: 'Error',
            text: getData.errors,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          })
        } else {
          this.form = getData.pd
          this.form.data = 'pd'
          this.data_provinsi = getData.provinsi
          this.data_kabupaten = getData.kabupaten
          this.data_kecamatan = getData.kecamatan
          this.data_desa = getData.desa
          this.data_agama = getData.agama
          this.data_cita = getData.cita
          this.data_pekerjaan = getData.pekerjaan
          this.editModalShow = true
        }
      })*/
    },
    hideModal(){
      this.editModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.form.peserta_didik_id = ''
      this.form.nama = ''
      this.form.nisn = ''
      this.form.jenis_kelamin = ''
      this.form.tempat_lahir = ''
      this.form.tanggal_lahir = ''
      this.form.image = null
      this.feedback.nama = ''
      this.feedback.nisn = ''
      this.feedback.jenis_kelamin = ''
      this.feedback.tempat_lahir = ''
      this.feedback.tanggal_lahir = ''
      this.feedback.image = ''
      this.state.nama = null
      this.state.nisn = null
      this.state.jenis_kelamin = null
      this.state.tempat_lahir = null
      this.state.tanggal_lahir = null
      this.state.image = null
      this.preview_url = null
    },
    onFileChange(e) {
      this.form.image = e.target.files[0];
      this.preview_url = URL.createObjectURL(this.form.image)
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      const data = new FormData();
      data.append('data', this.form.data);
      data.append('peserta_didik_id', this.form.peserta_didik_id)
      data.append('nama', this.form.nama??'');
      data.append('nisn', this.form.nisn??'');
      data.append('jenis_kelamin', this.form.jenis_kelamin??'');
      data.append('tempat_lahir', this.form.tempat_lahir??'');
      data.append('tanggal_lahir', this.form.tanggal_lahir??'');
      data.append('photo', this.form.image??'');
      this.$http.post('/peserta-didik/update', data).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.nama = (getData.errors.nama) ? false : null
          this.state.nisn = (getData.errors.nisn) ? false : null
          this.state.tempat_lahir = (getData.errors.tempat_lahir) ? false : null
          this.state.tanggal_lahir = (getData.errors.tanggal_lahir) ? false : null
          this.state.jenis_kelamin = (getData.errors.jenis_kelamin) ? false : null
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
          this.feedback.nisn = (getData.errors.nisn) ? getData.errors.nisn.join(', ') : ''
          this.feedback.tempat_lahir = (getData.errors.tempat_lahir) ? getData.errors.tempat_lahir.join(', ') : ''
          this.feedback.tanggal_lahir = (getData.errors.tanggal_lahir) ? getData.errors.tanggal_lahir.join(', ') : ''
          this.feedback.jenis_kelamin = (getData.errors.jenis_kelamin) ? getData.errors.jenis_kelamin.join(', ') : ''
          this.state.image = (getData.errors.photo) ? false : null
          this.feedback.image = (getData.errors.photo) ? getData.errors.photo.join(', ') : ''
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            text: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(() => {
            this.hideModal()
            this.$emit('reload')
          })
        }
      }).catch(error => {
        console.log(error);
      })
    },
    onContext(ctx) {
      this.formatted = ctx.selectedFormatted
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>