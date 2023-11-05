<template>
  <div>
    <b-card no-body>
      <b-card-header>
        <b-card-title>
          <h2>Edit Soal Mata Ujian {{ujian}}</h2>
        </b-card-title>
      </b-card-header>
      <b-card-body>
        <template v-if="isBusy">
          <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
        </template>
        <template v-else>
          <h2>Deskripsi Soal</h2>
          <editor
            v-model="content"
            api-key="w740v3v9x9obi3ezr352vukv5dmz6jl31pplcuujfvzbfp4s"
            :init="{
              height: 300,
              //menubar: false,
              plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
              ],
              toolbar:
                'undo redo | formatselect | bold italic backcolor | \
                alignleft aligncenter alignright alignjustify | \
                bullist numlist outdent indent | removeformat | help'
            }"
          />
        </template>
      </b-card-body>
    </b-card>
    <template v-if="isBusy">
      <div class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
    </template>
    <template v-for="(jawaban, index) in data_jawaban" v-else>
      <b-card no-body>
        <b-card-header>
          <b-card-title>
            <h2>Deskripsi Jawaban {{jawaban.opsi}}</h2>
          </b-card-title>
          <div class="heading-elements">
            <b-form-select v-model="benar[jawaban.jawaban_id]" :options="options"></b-form-select>
          </div>
        </b-card-header>
        <b-card-body>
          <editor
            v-model="content_jawaban[jawaban.jawaban_id]"
            api-key="w740v3v9x9obi3ezr352vukv5dmz6jl31pplcuujfvzbfp4s"
            :init="{
              height: 200,
              //menubar: false,
              plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
              ],
              toolbar:
                'undo redo | formatselect | bold italic backcolor | \
                alignleft aligncenter alignright alignjustify | \
                bullist numlist outdent indent | removeformat | help'
            }"
          />
        </b-card-body>
      </b-card>
    </template>
    <b-card no-body>
      <b-card-body>
        <template v-if="isBusy">
          <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
        </template>
        <template v-else>
          <b-row>
            <b-col>
              <template v-if="nomor <= 1">
              <b-button>&laquo; Sebelumnya</b-button>
              </template>
              <template v-else>
                <b-overlay :show="loading" rounded opacity="0.6" spinner-small spinner-variant="success" class="d-inline-block" @hidden="onHidden">
                  <b-button ref="button" @click="lanjut('prev')" variant="success" :disabled="isBusy">&laquo; Sebelumnya</b-button>
                </b-overlay>
              </template>
            </b-col>
            <b-col class="text-center">
              <b-overlay :show="loading" rounded opacity="0.6" spinner-small spinner-variant="success" class="d-inline-block" @hidden="onHidden">
                <b-button ref="button" variant="success" @click="simpan">Simpan</b-button>
              </b-overlay>
            </b-col>
            <b-col class="text-right">
              <template v-if="nomor == jumlah_soal">
                <b-button>Selanjutnya &raquo;</b-button>
              </template>
              <template v-else>
                <b-overlay :show="loading" rounded opacity="0.6" spinner-small spinner-variant="success" class="d-inline-block" @hidden="onHidden">
                  <b-button ref="button" @click="lanjut('next')" variant="success" :disabled="isBusy">Selanjutnya &raquo;</b-button>
                </b-overlay>
              </template>
            </b-col>
          </b-row>
        </template>
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody, BFormSelect, BButton, BRow, BCol, BSpinner, BOverlay } from 'bootstrap-vue'
import Editor from '@tinymce/tinymce-vue'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BFormSelect,
    BButton,
    BRow, 
    BCol,
    BSpinner,
    BOverlay,
    Editor,
  },
  data() {
    return {
      loading: false,
      isBusy: true,
      ujian: null,
      nomor: 0,
      jumlah_soal: 0,
      content: null,
      data_jawaban: [],
      content_jawaban: {},
      benar: {},
      options: [
        { text: 'Benar', value: 1},
        { text: 'Salah', value: 0},
      ]
    }
  },
  watch:{
    $route (to, from){
        console.log(to);
        console.log(from);
        this.loadPostsData()
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    loadPostsData(){
      this.isBusy = true
      this.$http.post('/bank-soal/soal', {
        ujian_id: this.$route.params.ujian_id,
        nomor: this.$route.query.nomor,
      }).then(response => {
        this.isBusy = false
        this.loading = false
        let getData = response.data
        this.content = getData.data.deskripsi
        this.nomor = getData.data.nomor
        this.ujian = getData.ujian.nama
        this.jumlah_soal = getData.ujian.jumlah_soal
        this.data_jawaban = getData.data.jawaban
        getData.data.jawaban.forEach((value, index) => {
          this.content_jawaban[value.jawaban_id] = value.deskripsi
          this.benar[value.jawaban_id] = value.benar
        });
      })
    },
    simpan() {
      this.loading = true
      this.$http.post('/bank-soal/simpan', {
        soal_id: this.soal_id,
        soal: this.content,
        jawaban: this.content_jawaban,
      }).then(response => {
        this.loading = false
        let getData = response.data
        this.$toast({
          component: ToastificationContent,
          position: 'bottom-center',
          props: {
            title: 'Berhasil',
            icon: 'CoffeeIcon',
            variant: 'success',
            text: 'Soal berhasil disimpan',
          },
        })
      })
    },
    onHidden() {
      this.$refs.button.focus()
    },
    lanjut(aksi){
      this.loading = true
      var nomor;
      if(aksi === 'next'){
        nomor = this.nomor + 1;
      } else {
        nomor = this.nomor - 1;
      }
      this.$router.push({ name: "edit-soal", params: {ujian_id:this.$route.params.ujian_id}, query: {nomor: nomor} })
    }
  },
}
</script>