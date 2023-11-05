<template>
  <b-table striped hover :items="items" :fields="fields" :busy="isBusy" show-empty v-model="visibleRows">
    <template #table-busy>
      <div class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
    </template>
    <template v-slot:cell(nama)="row">
      <router-link :to="'/kabupaten/'+row.item.kode_wilayah.trim()" :id="'tooltip-target-'+row.item.kode_wilayah.trim()" v-if="hasRole('administrator')">{{row.item.nama}}</router-link>
      <b-tooltip :target="'tooltip-target-'+row.item.kode_wilayah.trim()" triggers="hover">
        Lihat SMK di Wilayah <b>{{row.item.nama}}</b>
      </b-tooltip>
      <router-link :to="'/kab-kota/'+row.item.kode_wilayah.trim()" :id="'tooltip-target-'+row.item.kode_wilayah.trim()" v-if="hasRole('disprov')">{{row.item.nama}}</router-link>
      <b-tooltip :target="'tooltip-target-'+row.item.kode_wilayah.trim()" triggers="hover">
        Lihat SMK di Wilayah <b>{{row.item.nama}}</b>
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
      <template v-slot:cell(negeri_penerima)="row">
        {{ commify(row.item.negeri_pengguna)}}
      </template>
      <template v-slot:cell(swasta_penerima)="row">
        {{ commify(row.item.swasta_pengguna)}}
      </template>
      <template v-slot:cell(pd)="row">
        {{ commify(row.item.total_pd)}}
      </template>
      <template v-slot:cell(persentase)="row">
        {{ persentase(row.item.nasional, row.item.pengguna) }}%
      </template>
      <template slot="bottom-row">
        <td>TOTAL</td>
        <td class="text-center">{{ commify(totalNasional) }}</td>
        <td class="text-center">{{ commify(totalNegeri) }}</td>
        <td class="text-center">{{ commify(totalSwasta) }}</td>
        <td class="text-center">{{ commify(totalPenggunaNegeri) }}</td>
        <td class="text-center">{{ commify(totalPenggunaSwasta) }}</td>
        <td class="text-center">{{ commify(totalPD) }}</td>
        <td class="text-center">{{ persentase(totalNasional, totalPengguna) }}%</td>
      </template>
    <template v-slot:cell(persentase)="row">
      {{persentase(row.item.nasional, row.item.pengguna)}}%
    </template>
  </b-table>
</template>

<script>
import { BTable, BTooltip, BSpinner } from 'bootstrap-vue'
export default {
  components: {
    BTable,
    BTooltip,
    BSpinner
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
      visibleRows: [],
      data: {},
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
    persentase(total, jml){
      let num = (total) ? jml / total * 100 : 0;
      return num.toFixed(2)
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
