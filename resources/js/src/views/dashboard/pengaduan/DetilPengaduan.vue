<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        <h2>Detil Pengaduan</h2>
      </b-card-title>
    </b-card-header>
    <b-card-body class="text-center" v-if="isBusy">
      <b-spinner class="align-middle"></b-spinner>
      <strong>Loading...</strong>
    </b-card-body>
    <b-card-body v-else>
      <app-timeline>
        <app-timeline-item>
          <div class="d-flex flex-sm-row flex-column flex-wrap justify-content-between mb-1 mb-sm-0">
            <h6>{{data_pengaduan.judul}}</h6>
            <small class="text-muted">{{data_pengaduan.waktu}}</small>
          </div>
          <p>{{data_pengaduan.deskripsi}}</p>
          <div class="d-flex flex-sm-row flex-column justify-content-between align-items-start">
            <b-media>
              <template #aside>
                <b-avatar :src="'/img/avatars/no-photo.png'" />
              </template>
              <h6>{{data_pengaduan.user.name}}</h6>
              <span class="text-muted">{{ data_pengaduan.user.roles.map(u => u.display_name).join(', ') }}</span>
            </b-media>
            <div v-if="data_pengaduan.user.user_id !== user.user_id">
              <a href="javascript:" @click="jawab(data_pengaduan.id)" class="text-warning float-right" title="Tulis jawaban"><feather-icon icon="MessageSquareIcon" /></a>
            </div>
          </div>
          <b-form-group label-for="deskripsi" :invalid-feedback="DeskripsiFeedback" :state="DeskripsiState" v-show="comment == data_pengaduan.id">
            <b-form-textarea id="deskripsi" v-model="deskripsi" :state="DeskripsiState" placeholder="Tuliskan jawaban disini..." rows="3" max-rows="6"></b-form-textarea>
          </b-form-group>
          <b-button variant="primary" size="sm" @click="kirim(data_pengaduan.id)" v-show="comment == data_pengaduan.id">Kirim</b-button>
        </app-timeline-item>
        <app-timeline-item v-for="(pengaduan, index) in data_pengaduan.comments" :key="pengaduan.id">
          <div class="d-flex flex-sm-row flex-column flex-wrap justify-content-between mb-1 mb-sm-0">
            <h6>{{pengaduan.judul}}</h6>
            <small class="text-muted">{{pengaduan.waktu}}</small>
          </div>
          <p>{{pengaduan.deskripsi}}</p>
          <div class="d-flex flex-sm-row flex-column justify-content-between align-items-start">
            <b-media>
              <template #aside>
                <b-avatar :src="'/img/avatars/no-photo.png'" />
              </template>
              <h6>{{pengaduan.user.name}}</h6>
              <span class="text-muted">{{ pengaduan.user.roles.map(u => u.display_name).join(', ') }}</span>
            </b-media>
            <div v-if="pengaduan.user.user_id !== user.user_id">
              <a href="javascript:" @click="jawab(pengaduan.id)" class="text-warning float-right" title="Tulis jawaban"><feather-icon icon="MessageSquareIcon" /></a>
            </div>
          </div>
          <b-form-group label="Deskripsi" label-for="deskripsi" :invalid-feedback="DeskripsiFeedback" :state="DeskripsiState" v-show="comment == pengaduan.id">
            <b-form-textarea id="deskripsi" v-model="deskripsi" :state="DeskripsiState" placeholder="Tuliskan pengaduan disini..." rows="3" max-rows="6"></b-form-textarea>
          </b-form-group>
          <b-button variant="primary" size="sm" @click="kirim(data_pengaduan.id)" v-show="comment == pengaduan.id">Kirim</b-button>
        </app-timeline-item>
      </app-timeline>
    </b-card-body>
  </b-card>
</template>

<script>
import {
  BCard, BCardHeader, BCardTitle, BCardBody, BSpinner, BImg, BAvatar, BMedia, BButton, BCollapse, VBToggle, BListGroup, BListGroupItem, BAvatarGroup, BBadge, VBTooltip, BFormGroup, BFormTextarea
} from 'bootstrap-vue'
import AppTimeline from '@core/components/app-timeline/AppTimeline.vue'
import AppTimelineItem from '@core/components/app-timeline/AppTimelineItem.vue'

export default {
  components: {
    BCard, 
    BCardHeader, 
    BCardTitle, 
    BCardBody, 
    BSpinner,
    AppTimeline,
    AppTimelineItem,
    BImg,
    BMedia,
    BAvatar,
    BButton,
    BCollapse,
    BListGroup,
    BListGroupItem,
    BAvatarGroup,
    BBadge,
    BFormGroup, 
    BFormTextarea
  },
  data() {
    return {
      isBusy: true,
      data_pengaduan: '',
      comment: null,
      deskripsi: '',
      DeskripsiFeedback: '',
      DeskripsiState: null,
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    profilePhoto(user){
      console.log(user);
    },
    loadPostsData() {
      this.comment = null
      this.$http.get('/pengaduan/detil/'+this.$route.params.pengaduan_id).then(response => {
        //this.items = response.data.all_pd
        let getData = response.data
        this.data_pengaduan = getData
        this.isBusy = false
      })
    },
    jawab(id){
      console.log(id);
      this.comment = id
      //[id] = 'mana'
      console.log(this.comment);
    },
    kirim(id){
      this.$http.post('/pengaduan/jawab', {
        pengaduan_id: id,
        deskripsi: this.deskripsi
      }).then(response => {
        let getData = response.data
        if(getData.errors){
          this.DeskripsiState = (getData.errors.deskripsi) ? false : null
          this.DeskripsiFeedback = (getData.errors.deskripsi) ? getData.errors.deskripsi[0] : ''
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            html: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.loadPostsData()
          })
        }
      }).catch(error => {
        console.log(error);
      })
    }
  }
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>