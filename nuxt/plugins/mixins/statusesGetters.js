import Vue from "vue";

import { mapGetters } from "vuex";

const Statuses = {
  install(Vue, options) {
    Vue.mixin({
      computed: {
        ...mapGetters({
          $statuses: "statuses/statuses",
          $settingForm: "statuses/settingForm",
          $helpTooltip: 'statuses/helpTooltip'
        }),
      },
    });
  },
};

Vue.use(Statuses);
