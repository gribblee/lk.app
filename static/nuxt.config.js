// import axios from 'axios'

export default {
  // Disable server-side rendering: https://go.nuxtjs.dev/ssr-mode
  ssr: false,

  loading: "@/components/loading.vue",
  loadingIndicator: {
    name: "circle",
    color: "#3B8070",
    background: "white"
  },

  // generate: {
  //   routes() {
  //     return axios.get("https://lk.leadz.monster").then(res => {
  //       return res.data.map(user => {
  //         return "/users/" + user.id;
  //       });
  //     });
  //   }
  // },

  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    title: "Главная",
    htmlAttrs: {
      lang: "RU-ru"
    },
    meta: [
      { charset: "utf-8" },
      { name: "viewport", content: "width=device-width, initial-scale=1" },
      { hid: "description", name: "description", content: "" }
    ],
    link: [{ rel: "icon", type: "image/x-icon", href: "/favicon.ico" }]
  },

  router: {
    middleware: ["clearValidationErrors"]
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: ["ant-design-vue/dist/antd.css", "~/assets/sass/app.scss"],

  // Plugins to run before rendering page (https://go.nuxtjs.dev/config-plugins)
  plugins: [
    "@/plugins/antd-ui",
    "@/plugins/axios",
    "@/plugins/v-mask",
    "@/plugins/bitrix24",

    "@/plugins/mixins/validation",
    {
      src: "@/plugins/vue-moment",
      mode: "client"
    },
    {
      src: "@/plugins/vue-linkify",
      mode: "client"
    },
    {
      src: "./plugins/YandexMetrika",
      mode: "client"
    },

    // {
    //   src: '@/plugins/mojs',
    //   mode: 'client'
    // },
    {
      src: "./plugins/VueAudio",
      mode: "client"
    }
  ],
  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  modules: ["@nuxtjs/axios", "@nuxtjs/auth", "nuxt-vuex-localstorage"],

  auth: {
    strategies: {
      local: {
        token: {
          property: "token",
          required: true,
          type: "Bearer"
        },
        endpoints: {
          login: {
            url: "/authorize",
            method: "post",
            propertyName: "token"
          },
          user: {
            url: "/user/me",
            method: "get",
            propertyName: "data"
          },
          logout: {
            url: "/logout",
            method: "get"
          }
        }
      }
    },
    redirect: {
      login: "/sign",
      logout: "/sign",
      home: "/"
    },
    plugins: [
      "@/plugins/auth",
      "@/plugins/directory",
      "@/plugins/user",
      "@/plugins/vueClipboard",

      "@/plugins/mixins/directoryMixin",
      "@/plugins/mixins/userMixin",
      "@/plugins/mixins/tinkoffMixin",
      "@/plugins/mixins/paymentMixin",
      "@/plugins/module/sliderSetting"
    ]
  },

  /*
   ** Axios module configuration
   ** See https://axios.nuxtjs.org/options
   */
  axios: {
    baseURL: "http://dev.leadz.monster/api" //`${process.env.NODE_BASE_URL}`
    // baseURL: "https://lk.leadz.monster/api"
  },

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    // https://go.nuxtjs.dev/typescript
    "@nuxt/typescript-build"
  ],

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {
    transpile: ["@nuxtjs/auth"]
  }
};
