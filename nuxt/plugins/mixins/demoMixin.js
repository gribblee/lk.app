import Vue from 'vue'

import { mapGetters } from 'vuex'

const DemoData = {
    install(Vue, options) {
        Vue.mixin({
            computed: {
                ...mapGetters({
                    $demoTitle: 'demoData/title',
                    $demoDescription: 'demoData/description',
                    $demoStateId: 'demoData/stateId',
                    $demoStep: 'demoData/step'
                }),
            },
        })
    },
}

Vue.use(DemoData)
