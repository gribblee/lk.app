import Vue from 'vue'

import { mapGetters } from 'vuex'

const DirectoryData = {
  install(Vue, options) {
    Vue.mixin({
      computed: {
        ...mapGetters({
          $directory: 'directory/directory',
        }),
      },
    })
  },
}

Vue.use(DirectoryData)
