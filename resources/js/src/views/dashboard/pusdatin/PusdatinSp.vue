<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>Data Pusdatin</b-card-title>
    </b-card-header>
    <b-card-body>
      <b-row>
        <b-col md="4" class="mb-2">
          <div class="form-inline">
          <!-- KETIKA SELECT BOXNYA DIGANTI, MAKA AKAN MENJALANKAN FUNGSI loadPerPage -->
            <select class="form-control" v-model="meta.per_page" @change="loadPerPage">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <label class="ml-2">Entri</label>
          </div>
        </b-col>
        <b-col md="4" offset-md="4">
          <div class="form-inline float-right">
            <label class="mr-2">Cari</label>
            <!-- KETIKA ADA INPUTAN PADA KOLOM PENCARIAN, MAKA AKAN MENJALANKAN FUNGSI SEARCH -->
            <input type="text" class="form-control" @input="search">
          </div>
        </b-col>
      </b-row>
      <b-table responsive striped :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" :status.sync="status" show-empty :busy="isBusy">
        <template #table-busy>
          <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
        </template>
        <template v-slot:cell(total)="row">
          {{row.item.program_3_tahun + row.item.program_4_tahun}}
        </template>
      </b-table>
      <b-row class="mt-2">
        <b-col md="6">
          <p>Menampilkan {{ (meta.from) ? meta.from : 0 }} sampai {{ meta.to }} dari {{ meta.total }} entri</p>
        </b-col>
        <!-- BLOCK INI AKAN MENJADI PAGINATION DARI DATA YANG DITAMPILKAN -->
        <b-col md="6">
          <!-- DAN KETIKA TERJADI PERGANTIAN PAGE, MAKA AKAN MENJALANKAN FUNGSI changePage -->
          <b-pagination v-model="meta.current_page" :total-rows="meta.total" :per-page="meta.per_page" align="right" @change="changePage" aria-controls="dw-datatable"></b-pagination>
        </b-col>
      </b-row>
    </b-card-body>
  </b-card>
</template>

<script>
import _ from 'lodash'
import {
  BCard, BCardHeader, BCardTitle, BCardBody, BTable, BSpinner, BRow, BCol, BPagination
} from 'bootstrap-vue'

export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BTable,
    BSpinner,
    BRow,
    BCol,
    BPagination
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
    meta: {
      required: true
    },
    isBusy: {
      type: Boolean,
      default: () => true,
    }
  },
  data() {
    return {
      sortBy: null,
      sortDesc: false,
      status: null,
    }
  },
  watch: {
    sortBy(val) {
      this.$emit('sort', {
        sortBy: this.sortBy,
        sortDesc: this.sortDesc
      })
    },
    sortDesc(val) {
      this.$emit('sort', {
        sortBy: this.sortBy,
        sortDesc: this.sortDesc
      })
    },
    status(val){
      console.log(val);
      this.$emit('status', val)
    },
  },
  methods: {
    loadPerPage(val) {
      this.$emit('per_page', this.meta.per_page)
    },
    changePage(val) {
      this.$emit('pagination', val)
    },
    search: _.debounce(function (e) {
      this.$emit('search', e.target.value)
    }, 500),
  },
}
</script>