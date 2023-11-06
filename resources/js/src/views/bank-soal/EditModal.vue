<template>
  <b-modal v-model="editModalShow" title="Edit Ujian" size="xl" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Nama Mata Ujian" label-for="nama" label-cols-md="3" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input id="nama" v-model="form.nama" :state="state.nama" placeholder="Nama Mata Ujian"></b-form-input>
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
        ujian_id: '',
        nama: '',
        status: '',
      },
      feedback: {
        nama: '',
        status: '',
      },
      state: {
        nama: null,
        status: null,
      },
    }
  },
  created() {
    eventBus.$on('open-modal', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      console.log(data);
      this.editModalShow = true
      this.form.ujian_id = data.ujian_id
      this.form.nama = data.nama
      this.form.status = data.status
    },
    hideModal(){
      this.editModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.form.ujian_id = ''
      this.form.nama = ''
      this.form.status = ''
      this.feedback.nama = ''
      this.feedback.status = ''
      this.state.nama = null
      this.state.status = null
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      this.$http.post('/bank-soal/update', this.form).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.nama = (getData.errors.nama) ? false : null
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
          this.state.status = (getData.errors.status) ? false : null
          this.feedback.status = (getData.errors.status) ? getData.errors.status.join(', ') : ''
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