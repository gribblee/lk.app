import Vue from 'vue'

import { mapGetters } from 'vuex'

const Validation = {
    install(Vue, options) {
        Vue.mixin({
            computed: {
                ...mapGetters({
                    user: 'user',
                    $userUpdated: "userData/updated",
                    authenticated: 'authenticated',
                }),
            },
        })
    },
}

Vue.use(Validation)
