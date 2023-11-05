<template>
  <b-modal v-model="editModalShow" title="Edit Data Guru" size="xl" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Nama Lengkap" label-for="nama" label-cols-md="3" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input id="nama" v-model="form.nama" :state="state.nama" placeholder="Nama Lengkap dengan gelar"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="NUPTK" label-for="nuptk" label-cols-md="3" :invalid-feedback="feedback.nuptk" :state="state.nuptk">
              <b-form-input id="nuptk" v-model="form.nuptk" placeholder="NUPTK"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="NIP" label-for="nip" label-cols-md="3" :invalid-feedback="feedback.nip" :state="state.nip">
              <b-form-input id="nip" v-model="form.nip" :state="state.nip" placeholder="NIP"></b-form-input>
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
            <b-form-group label="Email" label-for="email" label-cols-md="3" :invalid-feedback="feedback.email" :state="state.email">
              <b-form-input id="email" v-model="form.email" :state="state.email" placeholder="Email"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Foto Guru" label-for="image" label-cols-md="3" :invalid-feedback="feedback.image" :state="state.image">
              <b-form-file id="image" accept=".jpg, .png" v-model="form.image" :state="state.image" placeholder="Upload Foto Guru..." drop-placeholder="Drop file here..." @change="onFileChange"></b-form-file>
            </b-form-group>
          </b-col>
          <b-col cols="7" offset="3" v-if="preview_url">
            <b-img rounded v-bind="mainProps" :src="preview_url" alt="Foto Guru"></b-img>
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
        guru_id: '',
        nama: '',
        nuptk: '',
        jenis_kelamin: '',
        nip: '',
        email: '',
        image: null,
      },
      feedback: {
        nama: '',
        nuptk: '',
        jenis_kelamin: '',
        nip: '',
        email: '',
        image: '',
      },
      state: {
        nama: null,
        nuptk: null,
        jenis_kelamin: null,
        nip: null,
        email: null,
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
        this.form.guru_id = data.item.guru_id
        this.form.nama = data.item.nama
        this.form.email = data.item.email
        this.form.nuptk = data.item.nuptk
        this.form.jenis_kelamin = data.item.jenis_kelamin
        this.form.nip = data.item.nip
        //this.preview_url = data.item
      }
    },
    hideModal(){
      this.editModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.form.guru_id = ''
      this.form.nama = ''
      this.form.email = ''
      this.form.nuptk = ''
      this.form.jenis_kelamin = ''
      this.form.nip = ''
      this.form.image = null
      this.feedback.nama = ''
      this.feedback.email = ''
      this.feedback.nuptk = ''
      this.feedback.jenis_kelamin = ''
      this.feedback.nip = ''
      this.feedback.image = ''
      this.state.nama = null
      this.state.email = null
      this.state.nuptk = null
      this.state.jenis_kelamin = null
      this.state.nip = null
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
      data.append('guru_id', this.form.guru_id)
      data.append('nama', this.form.nama??'');
      data.append('email', this.form.email??'');
      data.append('nuptk', this.form.nuptk??'');
      data.append('jenis_kelamin', this.form.jenis_kelamin??'');
      data.append('nip', this.form.nip??'');
      data.append('photo', this.form.image??'');
      this.$http.post('/guru/update', data).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.nama = (getData.errors.nama) ? false : null
          this.state.email = (getData.errors.email) ? false : null
          this.state.nuptk = (getData.errors.nuptk) ? false : null
          this.state.nip = (getData.errors.nip) ? false : null
          this.state.jenis_kelamin = (getData.errors.jenis_kelamin) ? false : null
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
          this.feedback.email = (getData.errors.email) ? getData.errors.email.join(', ') : ''
          this.feedback.nuptk = (getData.errors.nuptk) ? getData.errors.nuptk.join(', ') : ''
          this.feedback.nip = (getData.errors.nip) ? getData.errors.nip.join(', ') : ''
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