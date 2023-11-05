import Vue from 'vue'
import VueRouter from 'vue-router'

// Routes
import { canNavigate } from '@/libs/acl/routeProtection'
import { isUserLoggedIn, getUserData, getHomeRouteForLoggedInUser } from '@/auth/utils'
/*import apps from './routes/apps'
import dashboard from './routes/dashboard'
import uiElements from './routes/ui-elements/index'
import pages from './routes/pages'
import chartsMaps from './routes/charts-maps'
import formsTable from './routes/forms-tables'
import others from './routes/others'*/

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes: [
    /*{ 
      path: '/', 
      redirect: { name: 'dashboard' },
    },*/
    {
      path: '/',
      name: 'beranda',
      component: () => import('@/views/beranda/Index.vue'),
      meta: {
        layout: 'horizontal',
        resource: 'Akses',
        action: 'read',
      },
    },
    {
      path: '/login',
      name: 'auth-login',
      component: () => import('@/views/pages/Login.vue'),
      meta: {
        layout: 'full',
        resource: 'Auth',
        redirectIfLoggedIn: true,
      },
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/views/dashboard/Index.vue'),
      meta: {
        resource: 'Web',
        action: 'read',
      }
    },
    {
      path: '/rombongan-belajar',
      name: 'rombongan-belajar',
      component: () => import('@/views/rombongan-belajar/Index.vue'),
      meta: {
        resource: 'Web',
        action: 'read',
      }
    },
    {
      path: '/guru',
      name: 'guru',
      component: () => import('@/views/guru/Index.vue'),
      meta: {
        resource: 'Web',
        action: 'read',
      }
    },
    {
      path: '/peserta-didik',
      name: 'peserta-didik',
      component: () => import('@/views/peserta-didik/Index.vue'),
      meta: {
        resource: 'Web',
        action: 'read',
      }
    },
    {
      path: '/mata-pelajaran',
      name: 'mata-pelajaran',
      component: () => import('@/views/mata-pelajaran/Index.vue'),
      meta: {
        resource: 'Web',
        action: 'read',
      }
    },
    {
      path: '/bank-soal',
      name: 'bank-soal',
      component: () => import('@/views/bank-soal/Index.vue'),
      meta: {
        resource: 'Web',
        action: 'read',
      }
    },
    {
      path: '/edit-soal/:ujian_id',
      name: 'edit-soal',
      component: () => import('@/views/bank-soal/Soal.vue'),
      meta: {
        resource: 'Web',
        action: 'read',
        navActiveLink: 'bank-soal',
      }
    },
    {
      path: '/ujian/:ujian_id',
      name: 'ujian',
      component: () => import('@/views/Ujian/Index.vue'),
      meta: {
        layout: 'horizontal',
        resource: 'Web',
        action: 'read',
      }
    },
    {
      path: '/hasil-ujian/:ujian_id',
      name: 'hasil-ujian',
      component: () => import('@/views/Ujian/Selesai.vue'),
      meta: {
        layout: 'horizontal',
        resource: 'Web',
        action: 'read',
      }
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('@/views/pages/Profile.vue'),
      meta: {
        resource: 'Web',
        action: 'read',
      }
    },
    {
      path: '/error-404',
      name: 'error-404',
      component: () => import('@/views/error/Error404.vue'),
      meta: {
        layout: 'full',
        resource: 'Auth',
        action: 'read',
      },
    },
    //...dashboard,
    //...pages,
    {
      path: '*',
      redirect: 'error-404',
    },
  ],
})

router.beforeEach((to, _, next) => {
  const isLoggedIn = isUserLoggedIn()
  console.log(to.meta);
  //if (to.meta && to.meta.resource && !canNavigate(to)) {
  if (!canNavigate(to)) {
    // Redirect to login if not logged in
    if (!isLoggedIn) return next({ name: 'auth-login' })
    // If logged in => not authorized
    return next({ name: 'misc-not-authorized' })
  }

  // Redirect if logged in
  if (to.meta.redirectIfLoggedIn && isLoggedIn) {
    const userData = getUserData()
    next(getHomeRouteForLoggedInUser(userData ? userData.role : null))
  }

  return next()
})

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
  if (appLoading) {
    appLoading.style.display = 'none'
  }
})

export default router
