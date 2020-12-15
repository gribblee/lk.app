export default {
  /*
  ** Nuxt target
  ** See https://nuxtjs.org/api/configuration-target
  */
  target: 'server',
  // Global page headers (https://go.nuxtjs.dev/config-head)
  head: {
    title: 'lk.leadz.monster',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  router: {
    middleware: [
      'clearValidationErrors',
    ],
  },

  // Global CSS (https://go.nuxtjs.dev/config-css)
  css: [
    'ant-design-vue/dist/antd.css',
    '~/assets/sass/app.scss'
  ],

  // Plugins to run before rendering page (https://go.nuxtjs.dev/config-plugins)
  plugins: [
    '@/plugins/antd-ui',
    '@/plugins/axios',
    '@/plugins/v-mask',
    '@/plugins/bitrix24',

    '@/plugins/mixins/validation',
    '@/plugins/mixins/demoMixin',

    {
      src: '@/plugins/mojs',
      mode: 'client'
    },
    {
      src: './plugins/VueAudio',
      mode: 'client',
    },
  ],

  // Auto import components (https://go.nuxtjs.dev/config-components)
  components: true,

  // Modules for dev and build (recommended) (https://go.nuxtjs.dev/config-modules)
  buildModules: [
    // https://go.nuxtjs.dev/typescript
    '@nuxt/typescript-build',
  ],

  // Modules (https://go.nuxtjs.dev/config-modules)
  modules: [
    '@nuxtjs/axios',
    '@nuxtjs/auth',
  ],

  /**
   * Auth module configuration
   * See https://auth.nuxtjs.org/guide/setup.html
   */

  auth: {
    localStorage: true,
    strategies: {
      local: {
        endpoints: {
          login: {
            url: '/authorize',
            method: 'post',
            propertyName: 'token',
          },
          user: {
            url: '/me',
            method: 'get',
            propertyName: 'data'
          },
          logout: {
            url: '/logout',
            method: 'get',
          }
        }
      },
    },
    redirect: {
      login: '/sign',
      logout: '/sign',
      home: '/',
    },
    plugins: [
      '@/plugins/auth',
      '@/plugins/directory',
      '@/plugins/user',

      '@/plugins/mixins/directoryMixin',
      '@/plugins/mixins/userMixin',
      '@/plugins/mixins/tinkoffMixin',
      '@/plugins/mixins/paymentMixin',
    ]
  },

  /*
   ** Axios module configuration
   ** See https://axios.nuxtjs.org/options
   */
  axios: {
    baseURL: 'http://lk.leadz.monster/api',
  },

  // Build Configuration (https://go.nuxtjs.dev/config-build)
  build: {
    transpile: ['@nuxtjs/auth']
  }
}
