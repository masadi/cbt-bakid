<template>
  <b-modal v-model="addModalShow" title="Tambah Data Guru" @show="resetForm" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-button block variant="primary" href="/downloads/template-guru.xlsx" target="_blank">Download Template Excel</b-button>
          </b-col>
          <b-col cols="12" class="mt-2">
            <b-form-group :invalid-feedback="feedback_file" :state="state_file">
              <b-form-file v-model="file1" :state="state_file" placeholder="Choose a file or drop it here..." drop-placeholder="Drop file here..." @change="onFileChange"></b-form-file>
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
import { BOverlay, BForm, BFormGroup, BFormFile, BRow, BCol, BButton } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BOverlay,
    BForm,
    BFormGroup,
    BFormFile,
    BRow,
    BCol,
    BButton,
  },
  data() {
    return {
      addModalShow: false,
      loading_form: false,
      file1:  null,
      feedback_file: '',
      state_file: null,
      form: {},
      state: {},
    }
  },
  created() {
    //this.form.user_id = this.user.user_id
    //this.form.sekolah_id = this.user.sekolah_id
    //this.form.semester_id = this.user.semester.semester_id
    //this.form.periode_aktif = this.user.semester.nama
    eventBus.$on('open-modal', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      if(data.id == 'add'){
        this.addModalShow = true
      }
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
      data.append('file', (this.file1) ? this.file1 : '');
      data.append('data', 'guru');
      this.$http.post('/general/upload', data).then(response => {
        this.loading_form = false
        let getData = response.data
        console.log(getData);
        if(getData.errors){
          this.feedback_file = (getData.errors.file) ? getData.errors.file.join(', ') : ''
          this.state_file = (getData.errors.file) ? false : null
          //this.feedback.tahun_ajaran_id = (getData.errors.tahun_ajaran_id) ? getData.errors.tahun_ajaran_id.join(', ') : ''
          //this.state.tahun_ajaran_id = (getData.errors.tahun_ajaran_id) ? false : null
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