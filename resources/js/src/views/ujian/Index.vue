<template>
  <div>
    <b-card class="text-center" v-if="isBusy">
      <div class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <div><strong>Loading...</strong></div>
      </div>
    </b-card>
    <b-card v-else>
      <b-card-text class="text-center">
        <Countdown :end="yourEndDate" :deadline="yourEndDate"></Countdown>
      </b-card-text>
    </b-card>
    <b-row>
      <b-col cols="8" v-if="loading">
        <b-card>
          <b-card-text class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <div><strong>Loading...</strong></div>
          </b-card-text>
        </b-card>
      </b-col>
      <b-col cols="8" v-else>
        <b-card>
          <b-card-text>
            <h2 class="mb-2">Nomor Soal: {{nomor}}</h2>
            <h4 v-html="data.deskripsi"></h4>
            <b-form-group v-slot="{ ariaDescribedby }" class="mt-2">
              <template v-for="(jawaban, index) in data.jawaban">
                <b-form-radio v-model="selected" :aria-describedby="ariaDescribedby" name="some-radios" :value="jawaban.opsi" class="mb-1"><h4 v-html="jawaban.deskripsi"></h4></b-form-radio>
              </template>
            </b-form-group>
            <b-row>
              <b-col lg="4">
                <b-button v-if="nomor <= 1">&laquo; Sebelumnya</b-button>
                <b-button v-else @click="lanjut(data.soal_id, nomor, 'prev')" variant="success">&laquo; Sebelumnya</b-button>
              </b-col>
              <b-col lg="4" class="text-center">
                <b-form-checkbox v-model="ragu" name="check-button" button :button-variant="button_ragu" @change="getRagu">
                  Ragu
                </b-form-checkbox>
              </b-col>
              <b-col lg="4" class="text-right">
                <b-button v-if="nomor == jumlah_soal" variant="danger" @click="selesai">Selesai</b-button>
                <b-button v-else @click="lanjut(data.soal_id, nomor, 'next')" variant="success">Selanjutnya &raquo;</b-button>
              </b-col>
            </b-row>
          </b-card-text>
        </b-card>
      </b-col>
      <b-col cols="4" v-if="loading">
        <b-card>
          <b-card-text class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <div><strong>Loading...</strong></div>
          </b-card-text>
        </b-card>
      </b-col>
      <b-col cols="4" v-else>
        <b-card>
          <b-card-text>
            <h2 class="mb-2">Navigasi Soal</h2>
            <template v-for="chunk in chunked()">
              <b-row class="mb-1">
                <template v-for="article in chunk">
                  <b-col>
                    <b-button block :variant="nav_variant[article.nomor]" @click="lanjutNav(article.nomor)">{{article.nomor}}</b-button>
                  </b-col>
                </template>
              </b-row>
            </template>
            <h2 class="mt-3">Keterangan:</h2>
            <b-row class="mb-1">
              <b-col>
                <p><b-button variant="success">x</b-button> Soal Aktif</p>
                <p><b-button variant="secondary">x</b-button> Belum Terjawab</p>
                <p><b-button variant="primary">x</b-button> Terjawab</p>
                <p><b-button variant="warning">x</b-button> Terjawab Ragu</p>
              </b-col>
            </b-row>
          </b-card-text>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import _ from 'lodash'
import Countdown from './Countdown.vue'
import { BCard, BCardText, BRow, BCol, BSpinner, BTable, BBadge, BButton, BFormGroup, BFormRadio, BFormCheckbox } from 'bootstrap-vue'
export default {
  components: {
    BCard, BCardText, BRow, BCol, BSpinner, BTable, BBadge, BButton, BFormGroup, BFormRadio, BFormCheckbox, Countdown
  },
  data() {
    return {
      loading: true,
      isBusy: true,
      data: {},
      selected: '',
      ragu: false,
      button_ragu: 'secondary',
      nomor: 0,
      ujian_id: '',
      soal_id: null,
      jumlah_soal: 0,
      yourEndDate: 0,
      itemsPerRow: 4,
      all_soal: [],
      nav_variant: {},
    }
  },
  created() {
    this.getwaktu(this.$route.params.ujian_id)
    this.getSoal(this.$route.params.ujian_id, this.$route.query.nomor, false)
  },
  methods: {
    selesai(){
      this.$swal({
        title: 'Apakah Anda yakin?',
        text: 'Aksi ini akan mengakhiri sesi ujian',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yakin!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ml-1',
        },
        buttonsStyling: false,
        allowOutsideClick: () => !this.$swal.isLoading(),
      }).then(result => {
        if (result.value) {
          this.$http.post('/ujian/selesai', {
            ujian_id: this.$route.params.ujian_id,
            soal_id: this.soal_id,
            jawaban: this.selected,
            ragu: this.ragu,
            waktu: this.yourEndDate,
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
              //this.$router.replace('/')
              this.$router.replace({ name: "hasil-ujian", params: {ujian_id:this.$route.params.ujian_id} })
            })
          });
        }
      })
    },
    getwaktu(ujian_id){
      this.$http.post('/ujian/waktu', {
        ujian_id: ujian_id,
      }).then(response => {
        let getData = response.data
        if(getData){
          var today = new Date();
          var d = this.addMinutes(today, getData.waktu)
          this.yourEndDate = this.setDate(d)
        }
      })
    },
    chunked () {
      return _.chunk(this.all_soal, this.itemsPerRow)
    },
    setDate(d){
      const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
      let month = months[d.getMonth()];
      let day = d.getDate();
      let year = d.getFullYear();
      let h = d.getHours();
      let i = d.getMinutes();
      let s = d.getSeconds();
      return month+' '+day+', '+ year+' '+h+':'+i+':'+s
    },
    getSoal(ujian_id, nomor, replace){
      this.selected = ''
      this.ragu = false
      this.button_ragu = 'secondary'
      this.$http.post('/ujian/index', {
        ujian_id: ujian_id,
        nomor: nomor,
      }).then(response => {
        let getData = response.data
        this.isBusy = false
        this.loading = false
        this.data = getData.soal
        this.nomor = nomor
        this.ujian_id = ujian_id
        this.soal_id = getData.soal.soal_id
        this.jumlah_soal = getData.ujian.soal_count
        if(getData.soal.jawaban_siswa){
          console.log(getData.soal.jawaban_siswa.is_ragu);
          this.selected = getData.soal.jawaban_siswa.opsi
          this.ragu = (getData.soal.jawaban_siswa.is_ragu) ? true : false
          this.button_ragu = (getData.soal.jawaban_siswa.is_ragu) ? 'warning' : 'secondary'
        }
        this.all_soal = getData.all_soal
        this.all_soal.forEach((value, index) => {
          if(value.jawaban_siswa){
            if(parseInt(nomor) === parseInt(value.nomor)){
                this.nav_variant[value.nomor] = 'success'
            } else {
              if(value.jawaban_siswa.is_ragu){
                this.nav_variant[value.nomor] = 'warning'
              } else {
                this.nav_variant[value.nomor] = 'primary'
              }
            }
          } else {
            if(parseInt(nomor) === parseInt(value.nomor)){
              this.nav_variant[value.nomor] = 'success'
            } else {
              this.nav_variant[value.nomor] = 'secondary'
            }
          }
          console.log(parseInt(nomor));
          console.log(parseInt(value.nomor));
        })
        if(replace){
          this.$router.replace({ name: "ujian", params: {ujian_id:this.$route.params.ujian_id}, query: {nomor: nomor} })
        }
      })
    },
    lanjutNav(nomor_ujian){
      this.loading = true
      this.$http.post('/ujian/simpan', {
        ujian_id: this.ujian_id,
        soal_id: this.soal_id,
        jawaban: this.selected,
        ragu: this.ragu,
        waktu: this.yourEndDate,
      }).then(response => {
        this.getSoal(this.$route.params.ujian_id, nomor_ujian, true)
      })
    },
    lanjut(soal_id, nomor, nav){
      this.loading = true
      var nomor_ujian;
      if(nav == 'prev'){
        nomor_ujian = parseInt(nomor) - 1;
      } else {
        nomor_ujian = parseInt(nomor) + 1;
      }
      this.$http.post('/ujian/simpan', {
        ujian_id: this.ujian_id,
        soal_id: soal_id,
        jawaban: this.selected,
        ragu: this.ragu,
        waktu: this.yourEndDate,
      }).then(response => {
        this.getSoal(this.$route.params.ujian_id, nomor_ujian, true)
      })
    },
    getRagu(val){
      if(val){
        this.button_ragu = 'warning'
      } else {
        this.button_ragu = 'secondary'
      }
    },
    addMinutes(date, minutes) {
      date.setMinutes(date.getMinutes() + minutes);
      return date;
    },
  }
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>