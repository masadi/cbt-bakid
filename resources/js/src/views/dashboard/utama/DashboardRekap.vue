<template>
  <b-row class="match-height">
    <b-col xl="3" md="6" sm="12">
      <b-card class="text-center">
        <div v-if="isBusy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <div v-else>
          <b-card-text class="font-small-3" v-if="hasRole('sekolah')">
            <div class="avatar bg-light-info p-50 mb-1">
              <div class="avatar-content">
                <font-awesome-icon icon="fa-solid fa-users" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.pd_3_tahun}}</h2>
            <p class="card-text">PD 3 Tahun</p>
          </b-card-text>
          <b-card-text class="font-small-3" v-else>
            <div class="avatar bg-light-info p-50 mb-1">
              <div class="avatar-content">
                <font-awesome-icon icon="fa-solid fa-house" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.sp}}</h2>
            <p class="card-text">Satuan Pendidikan</p>
          </b-card-text>
        </div>
      </b-card>
    </b-col>
    <b-col xl="3" md="6" sm="12">
      <b-card class="text-center">
        <div v-if="isBusy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <div v-else>
          <b-card-text class="font-small-3" v-if="hasRole('sekolah')">
            <div class="avatar bg-light-primary p-50 mb-1">
              <div class="avatar-content">
                <font-awesome-icon icon="fa-solid fa-users" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.pd_4_tahun}}</h2>
            <p class="card-text">PD 4 Tahun</p>
          </b-card-text>
          <b-card-text class="font-small-3" v-else>
            <div class="avatar bg-light-primary p-50 mb-1">
              <div class="avatar-content">
                <font-awesome-icon icon="fa-solid fa-users" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.pd}}</h2>
            <p class="card-text">Peserta Didik</p>
          </b-card-text>
        </div>
      </b-card>
    </b-col>
    <b-col xl="3" md="6" sm="12">
      <b-card class="text-center">
        <div v-if="isBusy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <div v-else>
          <b-card-text class="font-small-3">
            <div class="avatar bg-light-danger p-50 mb-1">
              <div class="avatar-content">
                <font-awesome-icon icon="fa-solid fa-file-circle-check" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.blangko_3}}</h2>
            <p class="card-text">Blangko 3 Tahun</p>
          </b-card-text>
        </div>
      </b-card>
    </b-col>
    <b-col xl="3" md="6" sm="12">
      <b-card class="text-center">
        <div v-if="isBusy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <div v-else>
          <b-card-text class="font-small-3">
            <div class="avatar bg-light-warning p-50 mb-1">
              <div class="avatar-content">
                <font-awesome-icon icon="fa-solid fa-file-circle-check" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.blangko_4}}</h2>
            <p class="card-text">Blangko 4 Tahun</p>
          </b-card-text>
        </div>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import {
  BRow, BCol, BCard, BCardText, BLink, BButton, BImg, BSpinner
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { kFormatter } from '@core/utils/filter'

export default {
  components: {
    BRow,
    BCol,
    BCard,
    BCardText,
    BLink,
    BImg,
    BButton,
    BSpinner
  },
  directives: {
    Ripple,
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
  methods: {
    kFormatter,
  },
  data() {
    return {
      isBusy: true,
      data: {},
    }
  },
  created() {
    this.$http.get('/dashboard/index').then(response => {
      this.isBusy = false
      this.data = response.data
    })
  }
}
</script>
