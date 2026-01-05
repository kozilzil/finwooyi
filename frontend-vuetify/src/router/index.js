import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import OfferingWrite from '../views/offering/OfferingWrite.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/offering/write',
    name: 'offering-write',
    component: OfferingWrite
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
