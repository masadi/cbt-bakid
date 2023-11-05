<template>
  <div>
    <b-table striped hover :items="items" :fields="fields" :busy="isBusy" show-empty v-model="visibleRows">
      <template #table-busy>
        <div class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
      </template>
      <template v-slot:cell(nama)="row">
        <router-link :to="'/provinsi/' + row.item.kode_wilayah.trim()"
          :id="'tooltip-target-' + row.item.kode_wilayah.trim()">{{ row.item.nama }}</router-link>
        <b-tooltip :target="'tooltip-target-' + row.item.kode_wilayah.trim()" triggers="hover">
          Lihat Rekapitulasi Wilayah <b>{{ row.item.nama }}</b>
        </b-tooltip>
      </template>
      <template v-slot:cell(nasional)="row">
        {{ commify(row.item.nasional)}}
      </template>
      <template v-slot:cell(negeri_nasional)="row">
        {{ commify(row.item.negeri_nasional)}}
      </template>
      <template v-slot:cell(swasta_nasional)="row">
        {{ commify(row.item.swasta_nasional)}}
      </template>
      <template v-slot:cell(persentase)="row">
        {{ persentase(row.item.nasional, row.item.pengguna) }}%
      </template>
      <template v-slot:cell(negeri_penerima)="row">
        {{ commify(row.item.negeri_pengguna)}}
      </template>
      <template v-slot:cell(swasta_penerima)="row">
        {{ commify(row.item.swasta_pengguna)}}
      </template>
      <template v-slot:cell(pd)="row">
        {{ commify(row.item.total_pd)}}
      </template>
      <template slot="bottom-row">
        <td>TOTAL</td>
        <td class="text-center">{{ commify(totalNasional) }}</td>
        <td class="text-center">{{ commify(totalNegeri) }}</td>
        <td class="text-center">{{ commify(totalSwasta) }}</td>
        <td class="text-center">{{ commify(totalPenggunaNegeri) }}</td>
        <td class="text-center">{{ commify(totalPenggunaSwasta) }}</td>
        <td class="text-center">{{ persentase(totalNasional, totalPengguna) }}%</td>
        <td class="text-center">{{ commify(totalPD) }}</td>
        <td class="text-center"></td>
      </template>
      <template v-slot:cell(action)="row">
        <b-button @click="detil(row.item)" variant="success" size="sm"><font-awesome-icon icon="fa-solid fa-eye" /> Detil</b-button>
      </template>
    </b-table>
    <b-modal ref="my-modal" size="lg" :title="judul" ok-only ok-title="Tutup" ok-variant="danger" v-model="modalShow">
      <table class="table table-bordered">
        <tr>
          <td colspan="2" class="text-center">DITERIMA</td>
        </tr>
        <tr>
          <td width="30%">SMK 3 Tahun</td>
          <td width="70%">
            <span v-if="smk_3_tahun.diterima">
              {{smk_3_tahun.diterima}} lembar. ({{smk_3_tahun.start}} - {{smk_3_tahun.end}})
            </span>
            <span v-else>
              0
            </span>
          </td>
        </tr>
        <tr>
          <td>SMK 4 Tahun</td>
          <td>
            <span v-if="smk_4_tahun.diterima">
              {{smk_4_tahun.diterima}} lembar. ({{smk_4_tahun.start}} - {{smk_4_tahun.end}})
            </span>
            <span v-else>
              0
            </span>
          </td>
        </tr>
      </table>
    </b-modal>
  </div>
</template>

<script>
import { BTable, BTooltip, BSpinner, BButton, BModal } from 'bootstrap-vue'
export default {
  components: {
    BTable,
    BTooltip,
    BSpinner,
    BButton,
    BModal
  },
  props: {
    items: {
      type: Array,
      required: true
    },
    fields: {
      type: Array,
      required: true
    },
    isBusy: {
      type: Boolean,
      default: () => true,
    }
  },
  data() {
    return {
      data: {},
      visibleRows: [],
      judul: 'Detil Penatausahaan Ijazah',
      modalShow: false,
      smk_3_tahun: {
        diterima: 0,
        start: 0,
        end: 0,
      },
      smk_4_tahun: {
        diterima: 0,
        start: 0,
        end: 0,
      }
    }
  },
  computed: {
    totalNasional() {
      return this.visibleRows.reduce((accum, item) => {
        return accum + parseFloat(item.nasional)
      }, 0.00)
    },
    totalNegeri() {
      return this.visibleRows.reduce((accum, item) => {
        return accum + parseFloat(item.negeri_nasional)
      }, 0.00)
    },
    totalSwasta() {
      return this.visibleRows.reduce((accum, item) => {
        return accum + parseFloat(item.swasta_nasional)
      }, 0.00)
    },
    totalPengguna() {
      return this.visibleRows.reduce((accum, item) => {
        return accum + parseFloat(item.pengguna)
      }, 0.00)
    },
    totalPenggunaNegeri() {
      return this.visibleRows.reduce((accum, item) => {
        return accum + parseFloat(item.negeri_pengguna)
      }, 0.00)
    },
    totalPenggunaSwasta() {
      return this.visibleRows.reduce((accum, item) => {
        return accum + parseFloat(item.swasta_pengguna)
      }, 0.00)
    },
    totalPD() {
      return this.visibleRows.reduce((accum, item) => {
        return accum + parseFloat(item.total_pd)
      }, 0.00)
    },
  },
  methods: {
    persentase(total, jml) {
      let num = (total) ? jml / total * 100 : 0;
      return num.toFixed(2)
    },
    detil(item){
      this.$http.post('/blangko/wilayah', {
        kode_wilayah: item.kode_wilayah,
      }).then(response => {
        this.modalShow = true
        this.judul = 'Detil Penatausahaan Ijazah Wilayah '+response.data.wilayah.nama
        let getData = response.data
        if(getData.blangko_3_tahun){
          this.smk_3_tahun.diterima = getData.blangko_3_tahun.jumlah
          this.smk_3_tahun.start = getData.blangko_3_tahun.start
          this.smk_3_tahun.end = getData.blangko_3_tahun.end
        } else {
          this.smk_3_tahun.diterima = 0
          this.smk_3_tahun.start = 0
          this.smk_3_tahun.end = 0
        }
        if(getData.blangko_4_tahun){
          this.smk_4_tahun.diterima = getData.blangko_4_tahun.jumlah
          this.smk_4_tahun.start = getData.blangko_4_tahun.start
          this.smk_4_tahun.end = getData.blangko_4_tahun.end
        } else {
          this.smk_4_tahun.diterima = 0
          this.smk_4_tahun.start = 0
          this.smk_4_tahun.end = 0
        }
        console.log(this.smk_3_tahun);
      })
    },
    commify(n) {
      if(n){
        var parts = n.toString().split(".");
        const numberPart = parts[0];
        const decimalPart = parts[1];
        const thousands = /\B(?=(\d{3})+(?!\d))/g;
        return numberPart.replace(thousands, ".") + (decimalPart ? "." + decimalPart : "");
      } else {
        return 0
      }
    }
  }
}
</script>
