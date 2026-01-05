import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import './style.css'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.use(vuetify)
app.mount('#app')
