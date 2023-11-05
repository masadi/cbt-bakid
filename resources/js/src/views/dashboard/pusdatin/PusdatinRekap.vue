<template>
  <b-row class="match-height">
    <b-col xl="4" md="6" sm="12">
      <b-card class="text-center">
        <div v-if="isBusy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <div v-else>
          <b-card-text class="font-small-3">
            <div class="avatar bg-light-info p-50 mb-1" id="tooltip-target-1" @click="sp(1)">
              <div class="avatar-content">
                <b-tooltip target="tooltip-target-1" triggers="hover">
                  Tampilkan Semua Satuan Pendidikan
                </b-tooltip>
                <font-awesome-icon icon="fa-solid fa-house" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.sp}}</h2>
            <p class="card-text">Satuan Pendidikan</p>
          </b-card-text>
        </div>
      </b-card>
    </b-col>
    <b-col xl="4" md="6" sm="12">
      <b-card class="text-center">
        <div v-if="isBusy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <div v-else>
          <b-card-text class="font-small-3">
            <div class="avatar bg-light-primary p-50 mb-1" id="tooltip-target-2" @click="sp(3)">
              <div class="avatar-content">
                <b-tooltip target="tooltip-target-2" triggers="hover">
                  Tampilkan Semua Satuan Pendidikan Program 3 Tahun
                </b-tooltip>
                <font-awesome-icon icon="fa-solid fa-house" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.sp_3_tahun}}</h2>
            <p class="card-text">SP Program 3 Tahun</p>
          </b-card-text>
        </div>
      </b-card>
    </b-col>
    <b-col xl="4" md="6" sm="12">
      <b-card class="text-center">
        <div v-if="isBusy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <div v-else>
          <b-card-text class="font-small-3">
            <div class="avatar bg-light-danger p-50 mb-1" id="tooltip-target-3" @click="sp(4)">
              <div class="avatar-content">
                <b-tooltip target="tooltip-target-3" triggers="hover">
                  Tampilkan Semua Satuan Pendidikan Program 4 Tahun
                </b-tooltip>
                <font-awesome-icon icon="fa-solid fa-house" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.sp_4_tahun}}</h2>
            <p class="card-text">SP Program 4 Tahun</p>
          </b-card-text>
        </div>
      </b-card>
    </b-col>
    <b-col xl="4" md="6" sm="12">
      <b-card class="text-center">
        <div v-if="isBusy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <div v-else>
          <b-card-text class="font-small-3">
            <div class="avatar bg-light-warning p-50 mb-1" id="tooltip-target-4" @click="sp(2)">
              <div class="avatar-content">
                <b-tooltip target="tooltip-target-4" triggers="hover">
                  Tampilkan Semua Satuan Pendidikan Terhapus di Dapodik
                </b-tooltip>
                <font-awesome-icon icon="fa-solid fa-house" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.soft_delete}}</h2>
            <p class="card-text">SP Soft Delete</p>
          </b-card-text>
        </div>
      </b-card>
    </b-col>
    <b-col xl="4" md="6" sm="12">
      <b-card class="text-center">
        <div v-if="isBusy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <div v-else>
          <b-card-text class="font-small-3">
            <div class="avatar bg-light-warning p-50 mb-1" id="tooltip-target-5" @click="sp(3)">
              <div class="avatar-content">
                <b-tooltip target="tooltip-target-5" triggers="hover">
                  Tampilkan Semua Satuan Pendidikan Program 3 Tahun
                </b-tooltip>
                <font-awesome-icon icon="fa-solid fa-users" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.pd_3_tahun}}</h2>
            <p class="card-text">PD Program 3 Tahun</p>
          </b-card-text>
        </div>
      </b-card>
    </b-col>
    <b-col xl="4" md="6" sm="12">
      <b-card class="text-center">
        <div v-if="isBusy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <div v-else>
          <b-card-text class="font-small-3">
            <div class="avatar bg-light-warning p-50 mb-1" id="tooltip-target-6" @click="sp(4)">
              <div class="avatar-content">
                <b-tooltip target="tooltip-target-6" triggers="hover">
                  Tampilkan Semua Satuan Pendidikan Program 4 Tahun
                </b-tooltip>
                <font-awesome-icon icon="fa-solid fa-users" size="2xl" />
              </div>
            </div>
            <h2 class="fw-bolder">{{data.pd_4_tahun}}</h2>
            <p class="card-text">PD Program 4 Tahun</p>
          </b-card-text>
        </div>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import {
  BRow, BCol, BCard, BCardText, BLink, BButton, BImg, BSpinner, BTooltip 
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
    BSpinner,
    BTooltip 
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
    sp(status){
      console.log('rekap:'+status);
      this.$emit('status', status)
    }
  },
  data() {
    return {
      isBusy: true,
      data: {},
    }
  },
  created() {
    this.$http.get('/pusdatin/index').then(response => {
      this.isBusy = false
      this.data = response.data
    })
  }
}
</script>
