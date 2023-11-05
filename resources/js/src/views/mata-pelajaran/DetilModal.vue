<template>
  <b-modal v-model="detilModalShow" title="Detil Peserta Didik" size="lg" ok-only ok-variant="secondary" ok-text="Tutup">
    <BTableSimple v-if="detil">
      <BTr>
        <BTd colspan="2">
          <b-avatar size="10rem" rounded :src="`/storage/images/${detil.photo}`" variant="light-primary"></b-avatar>
          <div class="mt-1"><b-badge variant="primary">Cita-cita: {{detil.cita}}</b-badge></div>
        </BTd>
      </BTr>
      <BTr>
        <BTd>Nama</BTd>
        <BTd>: {{detil.nama}}</BTd>
      </BTr>
      <BTr>
        <BTd>Nomor Induk</BTd>
        <BTd>: {{detil.no_induk}}</BTd>
      </BTr>
      <BTr>
        <BTd>NISN</BTd>
        <BTd>: {{detil.nisn}}</BTd>
      </BTr>
      <BTr>
        <BTd>NIK</BTd>
        <BTd>: {{detil.nik}}</BTd>
      </BTr>
      <BTr>
        <BTd>Jenis Kelamin</BTd>
        <BTd>: {{(detil.jenis_kelamin === 'L') ? 'Laki-laki' : 'Perempuan'}}</BTd>
      </BTr>
      <BTr>
        <BTd>Tempat, Tanggal Lahir</BTd>
        <BTd>: {{detil.tempat_lahir}}, {{detil.tanggal_lahir_indo }}</BTd>
      </BTr>
      <BTr>
        <BTd>Agama</BTd>
        <BTd>: {{(detil.agama_id) ? detil.agama.nama : null}}</BTd>
      </BTr>
      <BTr>
        <BTd>Alamat</BTd>
        <BTd>: {{detil.alamat_jalan}}, RT/RW: {{`${detil.rt}/${detil.rw}`}}, Kodepos {{detil.kode_pos}}</BTd>
      </BTr>
      <BTr>
        <BTd>Desa/Kelurahan</BTd>
        <BTd>: {{(detil.desa_id) ? detil.desa.name : null}}</BTd>
      </BTr>
      <BTr>
        <BTd>Kecamatan</BTd>
        <BTd>: {{(detil.kecamatan_id) ? detil.kecamatan.name : null}}</BTd>
      </BTr>
      <BTr>
        <BTd>Kabupaten/Kota</BTd>
        <BTd>: {{(detil.kabupaten_id) ? detil.kabupaten.name : null}}</BTd>
      </BTr>
      <BTr>
        <BTd>Provinsi</BTd>
        <BTd>: {{(detil.provinsi_id) ? detil.provinsi.name : null}}</BTd>
      </BTr>
      <BTr>
        <BTd>Nomor HP</BTd>
        <BTd>: {{detil.no_hp}}</BTd>
      </BTr>
      <BTr>
        <BTd>Sekolah Asal</BTd>
        <BTd>: {{detil.sekolah_asal}}</BTd>
      </BTr>
      <BTr>
        <BTd>Tanggal Masuk Sekolah</BTd>
        <BTd>: {{detil.diterima_indo}}</BTd>
      </BTr>
      <BTr>
        <BTd>Diterima Dikelas</BTd>
        <BTd>: {{detil.diterima_kelas}}</BTd>
      </BTr>
      <BTr>
        <BTd>Email</BTd>
        <BTd>: {{detil.email}}</BTd>
      </BTr>
      <BTr>
        <BTd>Nama Ayah</BTd>
        <BTd>: {{detil.nama_ayah}}</BTd>
      </BTr>
      <BTr>
        <BTd>Nama Ibu</BTd>
        <BTd>: {{detil.nama_ibu}}</BTd>
      </BTr>
      <BTr>
        <BTd>Pekerjaan Ayah</BTd>
        <BTd>: {{(detil.kerja_ayah) ? detil.pekerjaan_ayah.nama : null}}</BTd>
      </BTr>
      <BTr>
        <BTd>Pekerjaan Ibu</BTd>
        <BTd>: {{(detil.kerja_ibu) ? detil.pekerjaan_ibu.nama : null}}</BTd>
      </BTr>
    </BTableSimple>
  </b-modal>
</template>

<script>
import { BTableSimple, BTr, BTd, BAvatar, BBadge } from 'bootstrap-vue'
export default {
  components: {
    BTableSimple, BTr, BTd, BAvatar, BBadge
  },
  data() {
    return {
      detilModalShow: false,
      detil: null,
      mainProps: {width: 125, height: 125 },
    }
  },
  created() {
    
  },
  methods: {
    handleEvent(data){
      //this.detil = data
      //this.detilModalShow = true
      this.$http.post('/referensi/detil-data', {
        data: 'pd',
        wilayah: true,
        id: data.peserta_didik_id
      }).then(response => {
        let getData = response.data
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
          this.detil = getData
          this.detilModalShow = true
        }
      })
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>