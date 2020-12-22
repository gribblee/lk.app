import Vue from 'vue'

import { mapGetters } from 'vuex'

const TinkoffMixin = {
    install(Vue, options) {
        Vue.mixin({
            computed: {
                ...mapGetters({
                    $tinkoffInstallmentVisible: 'tinkoff/InstallmentVisible'
                }),
            },
        })
    },
}

Vue.use(TinkoffMixin)
