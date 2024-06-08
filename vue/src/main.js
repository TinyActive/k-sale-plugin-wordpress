import { createApp, provide } from 'vue'
import { numericOnly, currency } from './common/directives.js'
// Vuetify
import './style.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { aliases, mdi } from 'vuetify/iconsets/mdi'
import '@mdi/font/css/materialdesignicons.css'
import App from './App.vue'

const vuetify = createVuetify({
    components,
    directives,
    aliases,
    sets: {
        mdi,
    }
})
const app = createApp(App);

// use a plugin
app.use(vuetify)

// add directive
app.directive('numericOnly', numericOnly);
app.directive('currency', currency);

// mount
app.mount('#app');
