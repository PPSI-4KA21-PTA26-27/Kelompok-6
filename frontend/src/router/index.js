import { createRouter, createWebHistory } from 'vue-router'
import { tokenStore, setUnauthorizedHandler } from '../lib/api'

// Halaman yang belum dikerjakan memakai PlaceholderView sampai tahapnya tiba.
const Placeholder = () => import('../views/PlaceholderView.vue')

const routes = [
  { path: '/', name: 'beranda', component: () => import('../views/HomeView.vue'), meta: { judul: 'Beranda' } },
  { path: '/katalog', name: 'katalog', component: () => import('../views/KatalogView.vue'), meta: { judul: 'Katalog' } },
  { path: '/produk/:id(\\d+)', name: 'produk', component: Placeholder, meta: { judul: 'Detail Produk' } },
  { path: '/kontak', name: 'kontak', component: Placeholder, meta: { judul: 'Kontak dan Lokasi' } },

  { path: '/admin/login', name: 'admin-login', component: Placeholder, meta: { judul: 'Masuk Admin', admin: true, polos: true } },
  { path: '/admin', name: 'admin-ringkasan', component: Placeholder, meta: { judul: 'Ringkasan', admin: true, perluLogin: true } },
  { path: '/admin/produk', name: 'admin-produk', component: Placeholder, meta: { judul: 'Kelola Produk', admin: true, perluLogin: true } },
  { path: '/admin/produk/tambah', name: 'admin-produk-tambah', component: Placeholder, meta: { judul: 'Tambah Produk', admin: true, perluLogin: true } },
  { path: '/admin/produk/:id(\\d+)/ubah', name: 'admin-produk-ubah', component: Placeholder, meta: { judul: 'Ubah Produk', admin: true, perluLogin: true } },
  { path: '/admin/profil', name: 'admin-profil', component: Placeholder, meta: { judul: 'Ubah Profil', admin: true, perluLogin: true } },

  { path: '/:pathMatch(.*)*', name: 'tidak-ditemukan', component: () => import('../views/NotFoundView.vue'), meta: { judul: 'Halaman tidak ditemukan' } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: (to, from, saved) => saved || { top: 0 },
})

router.beforeEach((to) => {
  if (to.meta.perluLogin && !tokenStore.get()) {
    return { name: 'admin-login', query: { lanjut: to.fullPath } }
  }
  // Sudah masuk, tidak perlu melihat halaman login lagi.
  if (to.name === 'admin-login' && tokenStore.get()) {
    return { name: 'admin-ringkasan' }
  }
})

router.afterEach((to) => {
  document.title = to.meta.judul ? `${to.meta.judul} | Tani Casual` : 'Tani Casual'
})

// Token ditolak server (401): kembali ke halaman login.
setUnauthorizedHandler(() => {
  if (router.currentRoute.value.meta.admin && router.currentRoute.value.name !== 'admin-login') {
    router.push({ name: 'admin-login', query: { lanjut: router.currentRoute.value.fullPath, sesi: 'berakhir' } })
  }
})

export default router
