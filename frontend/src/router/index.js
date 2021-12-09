import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import store from '../store'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Home',
    meta: {
      navNoBG: true
    },
    component: Home
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('../views/About.vue')
  },
  {
    path: '/create-post',
    name: 'Editor',
    meta: {
      edit: false
    },
    component: () => import('../views/Editor.vue')
  },
  {
    path: '/following',
    name: 'Followed',
    component: () => import('../views/Following.vue')
  },
  {
    path: '/newest',
    name: 'Newest',
    meta: { navNoBG: true },
    component: () => import('../views/Newest.vue')
  },
  {
    path: '/trends',
    name: 'Trending',
    meta: { navNoBG: true },
    component: () => import('../views/Trending.vue')
  },
  {
    path: '/category/:name',
    name: 'Category',
    meta: {
      navNoBG: true
    },
    component: () => import('../views/Category.vue')
  },
  {
    path: '/:blog',
    name: 'Blog',
    component: () => import('../views/Blog.vue')
  },
  {
    path: '/:blog/@settings',
    name: 'Blog Settings',
    component: () => import('../views/BlogSettings.vue')
  },
  {
    path: '/:blog/:post',
    name: 'Post',
    component: () => import('../views/Post.vue')
  },
  {
    path: '/:blog/:post/edit',
    name: 'Editor',
    meta: {
      edit: true
    },
    component: () => import('../views/Editor.vue')
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

router.beforeEach((to, from, next) => {
  store.state.pageTitle = false
  next()
})

export default router
