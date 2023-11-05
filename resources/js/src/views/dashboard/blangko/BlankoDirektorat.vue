<template>
  <div>
    Blangko-direktorat
  </div>
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
    //
  }
}
</script>
