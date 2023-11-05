<template>
  <b-card class="card-transaction" no-body>
    <b-card-header>
      <b-card-title v-if="hasRole('administrator')">Kontak Person Dinas Pendidikan Provinsi</b-card-title>
      <b-card-title v-else>Kontak Person Dinas Pendidikan Provinsi &amp; Cabang Dinas Kabupaten/Kota</b-card-title>
    </b-card-header>
    <b-card-body>
      <div v-if="isBusy" class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
      <div v-else>
        <b-table responsive bordered hover :items="data" :fields="fields" show-empty>
          <template #empty="scope">
            <p class="text-center">Tidak ada data untuk ditampilkan</p>
          </template>
          <template v-slot:cell(no)="row">
            {{row.index + 1}}
          </template>
          <template v-slot:cell(roles)="row">
            {{ row.item.roles.map(entry => entry.description).join(', ') }}
          </template>
          <template v-slot:cell(email)="row">
            {{ row.item.email }}
            <a href="javascript:" @click="resetPassword(row.item)" class="text-warning float-right" title="Reset Password" v-if="hasRole('administrator')"><font-awesome-icon icon="fa-solid fa-user-shield" /></a>
          </template>
        </b-table>
      </div>
    </b-card-body>
  </b-card>
</template>

<script>
import {
  BCard, BCardHeader, BCardTitle, BCardBody, BTable, BSpinner, BButton
} from 'bootstrap-vue'

export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BTable,
    BSpinner,
    BButton
  },
  /*props: {
    data: {
      type: Array,
      default: () => [],
    },
    isBusy: {
      type: Boolean,
      default: () => true,
    }
  },*/
  data() {
    return {
      isBusy: true,
      data: [],
      fields: [
        {
          key: 'no',
          label: 'No',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'name',
          label: 'Nama',
          sortable: false,
          thClass: 'text-center',
        },
        {
          key: 'roles',
          label: 'Level',
          sortable: false,
          thClass: 'text-center',
        },
        {
          key: 'email',
          label: 'Email',
          sortable: false,
        },
        {
          key: 'no_hp',
          label: 'Nomor HP',
          sortable: false,
        },
      ],
    }
  },
  created() {
    this.$http.get('/dashboard/cp').then(response => {
      this.isBusy = false
      this.data = response.data
    })
  },
  methods: {
    resetPassword(item){
      this.$swal({
        title: 'Apakah Anda yakin?',
        text: "Tindakan ini tidak dapat dikembalikan",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yakin!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ml-1',
        },
        buttonsStyling: false,
      }).then(result => {
        if (result.value) {
          this.$http.post('/referensi/users/reset-password', {
            user_id: item.user_id
          }).then(response => {
            let data = response.data
            this.$swal({
              icon: data.icon,
              title: data.title,
              text: data.text,
              customClass: {
                confirmButton: 'btn btn-success',
              },
            })
          });
        }
      })
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>