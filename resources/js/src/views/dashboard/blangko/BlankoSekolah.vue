<template>
  <div>
    <div v-if="isBusy" class="text-center text-danger my-2">
      <b-spinner class="align-middle"></b-spinner>
      <strong>Loading...</strong>
    </div>
    <b-form @submit="onSubmit" @reset="onReset" v-if="!isBusy">
      <b-row class="my-1">
        <b-col sm="4">
          <label for="start_3">Nomor Urut Ijazah (SMK Program 3 Tahun)</label>
        </b-col>
        <b-col sm="8">
          <b-input-group>
            <b-form-input type="text" id="start_3" v-model="form.start_3" :state="stateStart_3" @keypress="NumbersOnly" @input="hitung_3" placeholder="Nomor Ijazah Awal"></b-form-input>
            <b-input-group-prepend is-text><b>s/d</b></b-input-group-prepend>
            <b-form-input type="text" id="end_3" v-model="form.end_3" :state="stateEnd_3" @keypress="NumbersOnly" @input="hitung_3" placeholder="Nomor Ijazah Akhir"></b-form-input>
          </b-input-group>
          <p v-show="feedback_start_3" class="text-danger">{{feedback_start_3}}</p>
          <p v-show="feedback_end_3" class="text-danger">{{feedback_end_3}}</p>
        </b-col>
      </b-row>
      <b-row class="my-1">
        <b-col sm="4">
          <label for="jumlah_3">Jumlah Blangko Ijazah Diterima (SMK Program 3 Tahun)</label>
        </b-col>
        <b-col sm="8">
          <!--b-form-input id="jumlah" type="number" v-model="form.jumlah"></b-form-input-->
          <b-input-group>
            <b-form-input type="text" id="jumlah_3" v-model="form.jumlah_3" :state="stateJumlah3" readonly></b-form-input>
            <b-input-group-append is-text><b>eksemplar</b></b-input-group-append>
          </b-input-group>
          <p v-show="feedback_jumlah_3" class="text-danger">{{feedback_jumlah_3}}</p>
        </b-col>
      </b-row>
      <b-row class="my-1">
        <b-col sm="4">
          <label for="start_4">Nomor Urut Ijazah (SMK Program 4 Tahun)</label>
        </b-col>
        <b-col sm="8">
          <b-input-group>
            <b-form-input type="text" id="start_4" v-model="form.start_4" :state="stateStart_4" @keypress="NumbersOnly" @input="hitung_4" :readonly="readonly" placeholder="Nomor Ijazah Awal"></b-form-input>
            <b-input-group-prepend is-text><b>s/d</b></b-input-group-prepend>
            <b-form-input type="text" id="end_4" v-model="form.end_4" :state="stateEnd_4" @keypress="NumbersOnly" @input="hitung_4" :readonly="readonly" placeholder="Nomor Ijazah Akhir"></b-form-input>
          </b-input-group>
          <p v-show="feedback_start_4" class="text-danger">{{feedback_start_4}}</p>
          <p v-show="feedback_end_4" class="text-danger">{{feedback_end_4}}</p>
        </b-col>
      </b-row>
      <b-row class="my-1">
        <b-col sm="4">
          <label for="jumlah_4">Jumlah Blangko Ijazah Diterima (SMK Program 4 Tahun)</label>
        </b-col>
        <b-col sm="8">
          <!--b-form-input id="jumlah" type="number" v-model="form.jumlah"></b-form-input-->
          <b-input-group>
            <b-form-input type="text" id="jumlah_4" v-model="form.jumlah_4" :state="stateJumlah4" readonly></b-form-input>
            <b-input-group-append is-text><b>eksemplar</b></b-input-group-append>
          </b-input-group>
          <p v-show="feedback_jumlah_4" class="text-danger">{{feedback_jumlah_4}}</p>
        </b-col>
      </b-row>
      <b-spinner variant="primary" label="Spinning" v-show="saving"></b-spinner>
      <b-button type="submit" variant="primary" v-show="!saving">Simpan</b-button>
      <b-button type="reset" variant="danger" v-show="!saving">Reset</b-button>
    </b-form>
  </div>
</template>

<script>
import _ from 'lodash' //IMPORT LODASH, DIMANA AKAN DIGUNAKAN UNTUK MEMBUAT DELAY KETIKA KOLOM PENCARIAN DIISI
import {
  BForm, BRow, BCol, BFormInput, BInputGroup, BInputGroupPrepend, BInputGroupAppend, BButton, BSpinner, BFormInvalidFeedback
} from 'bootstrap-vue'

export default {
  components: {
    BForm,
    BRow,
    BCol,
    BFormInput,
    BInputGroup,
    BInputGroupPrepend,
    BInputGroupAppend,
    BButton,
    BSpinner,
    BFormInvalidFeedback
  },
  /*props: {
    data: {
      type: Object,
      default: () => { },
    },
    isBusy: {
      type: Boolean,
      default: () => true,
    }
  },*/
  data() {
    return {
      form: {
        jumlah_3: 0,
        jumlah_4: 0,
        start_3: '',
        end_3: '',
        start_4: '',
        end_4: '',
      },
      stateJumlah3: null,
      stateStart_3: null,
      stateEnd_3: null,
      stateJumlah4: null,
      stateStart_4: null,
      stateEnd_4: null,
      feedback_jumlah_3: '',
      feedback_jumlah_4 : '',
      feedback_start_3 : '',
      feedback_end_3 : '',
      feedback_start_4 : '',
      feedback_end_4 : '',
      isBusy: true,
      data: {},
      saving: false,
      readonly: false,
      provinsi_id: null,
    }
  },
  created() {
    this.getBlangko()
  },
  methods: {
    NumbersOnly(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
        evt.preventDefault();;
      } else {
        return true;
      }
    },
    hitung_3() {
      this.form.jumlah_3 = (this.form.end_3) ? this.form.end_3 - this.form.start_3 + 1 : null
    },
    hitung_4(){
      this.form.jumlah_4 = (this.form.end_4) ? this.form.end_4 - this.form.start_4 + 1 : null
    },
    getBlangko(){
      this.$http.get('/blangko/diterima').then(response => {
        this.data = response.data
        if(this.data.blangko_3_tahun){
          this.form.jumlah_3 = (this.data.blangko_3_tahun.jumlah) ? this.data.blangko_3_tahun.end - this.data.blangko_3_tahun.start + 1 : 0
          this.form.start_3 = this.data.blangko_3_tahun.start
          this.form.end_3 = this.data.blangko_3_tahun.end
        }
        if(this.data.blangko_4_tahun){
          this.form.jumlah_4 = (this.data.blangko_4_tahun.jumlah) ? this.data.blangko_4_tahun.end - this.data.blangko_4_tahun.start + 1 : 0
          this.form.start_4 = this.data.blangko_4_tahun.start
          this.form.end_4 = this.data.blangko_4_tahun.end
        }
        if(this.hasRole('sekolah')){
          if(!this.data.rombel_4_tahun){
            this.readonly = true
          }
          this.provinsi_id = this.data.sekolah.provinsi_id
        }
        this.isBusy = false
      })
    },
    onSubmit(event) {
      this.saving = true
      event.preventDefault()
      //console.log(this.form);
      //return false
      this.$http.post('/blangko/simpan', {
        permintaan: 'simpan',
        jumlah_3: this.form.jumlah_3,
        jumlah_4: this.form.jumlah_4,
        start_3: this.form.start_3,
        end_3: this.form.end_3,
        start_4: this.form.start_4,
        end_4: this.form.end_4,
        sekolah_id: this.user.sekolah_id,
        kode_wilayah: this.provinsi_id,
      }).then(response => {
        this.saving = false
        let data = response.data
        this.stateJumlah3 = null
        this.stateStart_3 = null
        this.stateEnd_3 = null
        this.stateJumlah4 = null
        this.stateStart_4 = null
        this.stateEnd_4 = null
        this.feedback_jumlah_3 = ''
        this.feedback_jumlah_4 = ''
        this.feedback_start_3 = ''
        this.feedback_end_3 = ''
        this.feedback_start_4 = ''
        this.feedback_end_4 = ''
        if(data.errors){
          this.stateJumlah3 = (data.errors.jumlah_3) ? false : null
          this.stateStart_3 = (data.errors.start_3) ? false : null
          this.stateEnd_3 = (data.errors.end_3) ? false : null
          this.stateJumlah4 = (data.errors.jumlah_4) ? false : null
          this.stateStart_4 = (data.errors.start_4) ? false : null
          this.stateEnd_4 = (data.errors.end_4) ? false : null
          this.feedback_jumlah_3 = (data.errors.jumlah_3) ? data.errors.jumlah_3[0] : ''
          this.feedback_jumlah_4 = (data.errors.jumlah_4) ? data.errors.jumlah_4[0] : ''
          this.feedback_start_3 = (data.errors.start_3) ? data.errors.start_3[0] : ''
          this.feedback_end_3 = (data.errors.end_3) ? data.errors.end_3[0] : ''
          this.feedback_start_4 = (data.errors.start_4) ? data.errors.start_4[0] : ''
          this.feedback_end_4 = (data.errors.end_4) ? data.errors.end_4[0] : ''
        } else {
          this.$swal({
            icon: data.icon,
            title: data.title,
            text: data.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.getBlangko();
          })
        }
      })
      //alert(JSON.stringify(this.form))
    },
    onReset(event) {
      event.preventDefault()
      this.isBusy = true
      this.form.jumlah_3 = 0
      this.form.jumlah_4 = 0
      this.form.start_3 = ''
      this.form.end_3 = ''
      this.form.start_4 = ''
      this.form.end_4 = ''
      this.$nextTick(() => {
        this.isBusy = false
      })
    }
  }
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>