<template>
  <div>
    <b-card class="text-center" v-if="isBusy">
      <div class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <div><strong>Loading...</strong></div>
      </div>
    </b-card>
    <b-card v-else>
      <b-card-text>
        <h1>Hasil Akhir Mata Ujian {{mata_ujian}}</h1>
        <h2>Jawaban Benar: {{benar}}</h2>
        <h2>Jawaban Salah: {{salah}}</h2>
        <b-button variant="primary" @click="selesai">Halaman Utama</b-button>
        <b-button variant="danger" @click="logout">Keluar Aplikasi</b-button>
      </b-card-text>
    </b-card>
  </div>
</template>

<script>
import { BCard, BCardText, BSpinner, BButton } from 'bootstrap-vue'
import { initialAbility } from '@/libs/acl/config'
import useJwt from '@/auth/jwt/useJwt'
export default {
  components: {
    BCard, BCardText, BSpinner, BButton
  },
  data() {
    return {
      isBusy: true,
      mata_ujian: '',
      benar: 0,
      salah: 0,
    }
  },
  created() {
    this.$http.post('/ujian/hasil', {
      ujian_id: this.$route.params.ujian_id,
    }).then(response => {
      this.isBusy = false
      let data = response.data
      this.mata_ujian = data.mata_ujian
      this.benar = data.benar
      this.salah = data.salah
    });
  },
  methods: {
    selesai(){
      this.$router.replace('/')
    },
    logout() {
      // Remove userData from localStorage
      // ? You just removed token from localStorage. If you like, you can also make API call to backend to blacklist used token
      localStorage.removeItem(useJwt.jwtConfig.storageTokenKeyName)
      localStorage.removeItem(useJwt.jwtConfig.storageRefreshTokenKeyName)

      // Remove userData from localStorage
      localStorage.removeItem('userData')

      // Reset ability
      this.$ability.update(initialAbility)

      // Redirect to login page
      this.$router.push({ name: 'auth-login' })
    },
  }
}
</script>