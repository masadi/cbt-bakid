<template>
  <b-modal v-model="editModalShow" title="Edit Nama Mata Pelajaran" size="xl" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Nama Mata Pelajaran" label-for="nama" label-cols-md="3" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input id="nama" v-model="form.nama" :state="state.nama" placeholder="Nama Mata Pelajaran"></b-form-input>
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
          <b-button variant="success" @click="ok()">Perbaharui</b-button>
        </b-overlay>
      </template>
  </b-modal>
</template>

<script>
import { BOverlay, BForm, BFormInput, BInputGroup, BRow, BCol, BFormGroup, BFormFile, BButton, BFormDatepicker, BImg, BInputGroupAppend } from 'bootstrap-vue'
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
  },
  data() {
    return {
      editModalShow: false,
      loading_form: false,
      form: {
        mata_pelajaran_id: '',
        nama: '',
      },
      feedback: {
        nama: '',
      },
      state: {
        nama: null,
      },
    }
  },
  created() {
    eventBus.$on('open-modal', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      if(data.id == 'edit'){
        this.editModalShow = true
        this.form.mata_pelajaran_id = data.item.mata_pelajaran_id
        this.form.nama = data.item.nama
      }
    },
    hideModal(){
      this.editModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.form.mata_pelajaran_id = ''
      this.form.nama = ''
      this.feedback.nama = ''
      this.state.nama = null
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      this.$http.post('/mata-pelajaran/update', this.form).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.nama = (getData.errors.nama) ? false : null
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
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