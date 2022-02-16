export default {
  // Disable server-side rendering: https://go.nuxtjs.dev/ssr-mode
  // ssr: false,

  env: {
    baseUrlApi: process.env.BASE_URL_API || 'https://junews.mathieuranc.fr',
  },

  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    title: 'Backoffice Junews',
    htmlAttrs: {
      lang: 'en',
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' },
    ],
    link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }],
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: {
    dirs: [
      '~/components',
      {
        path: '~/components/Card/',
        prefix: 'Card',
      },
      {
        path: '~/components/Create/',
        prefix: 'Create',
      },
      {
        path: '~/components/Modify/',
        prefix: 'Modify',
      },
    ],
  },

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    // https://go.nuxtjs.dev/axios
    '@nuxtjs/axios',
    '@nuxtjs/auth-next',
  ],

  // Axios module configuration: https://go.nuxtjs.dev/config-axios
  axios: {
    baseURL: process.env.BASE_URL_API,
  },
  auth: {
    localStorage: false,
    cookie: {
      prefix: 'auth.',
      options: {
        path: '/',
        maxAge: 10800,
      },
    },
    strategies: {
      local: {
        endpoints: {
          login: { url: 'login', method: 'post', propertyName: 'access_token' },
          user: { url: 'me', method: 'get', propertyName: 'content' },
          logout: false,
        },
      },
    },
  },

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {},
}
