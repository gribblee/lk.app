import Vue from 'vue'

import { mapGetters } from 'vuex'

const PaymentMixin = {
    install(Vue, options) {
        Vue.mixin({
            computed: {
                ...mapGetters({
                    $visiblePayRequisite: 'payment/visiblePayRequisite',
                    $visiblePayCard: 'payment/visiblePayCard'
                }),
            },
        })
    },
}

Vue.use(PaymentMixin)
