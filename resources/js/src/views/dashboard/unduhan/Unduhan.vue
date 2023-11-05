<template>
  <div>
    <unduhan-header :data="data.header" :isBusy="isBusy" />
    <unduhan-changelog :data="data.changelog" :isBusy="isBusy" />
  </div>
</template>

<script>
import { BRow, BCol } from 'bootstrap-vue'

import UnduhanHeader from './UnduhanHeader.vue'
import UnduhanChangelog from './UnduhanChangelog.vue'

/* eslint-disable global-require */
export default {
  components: {
    BRow,
    BCol,
    UnduhanHeader,
    UnduhanChangelog,
  },
  data() {
    return {
      isBusy: true,
      data: {},
    }
  },
  created() {
    
  },
}
/* eslint-disable global-require */
</script>