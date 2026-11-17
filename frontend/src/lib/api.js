import axios from 'axios'

const TOKEN_KEY = 'tc_token'

export const tokenStore = {
  get: () => localStorage.getItem(TOKEN_KEY),
  set: (token) => localStorage.setItem(TOKEN_KEY, token),
  clear: () => localStorage.removeItem(TOKEN_KEY),
}

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  timeout: 15000,
  headers: { Accept: 'application/json' },
})

// Dipanggil saat server menjawab 401 (token tidak valid/kedaluwarsa).
// Router mendaftarkan pengalihan ke halaman login lewat fungsi ini.
let onUnauthorized = () => {}
export function setUnauthorizedHandler(fn) {
  onUnauthorized = fn
}

api.interceptors.request.use((config) => {
  const token = tokenStore.get()
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

api.interceptors.response.use(
  (res) => res,
  (error) => {
    const status = error.response?.status
    const isLoginRequest = error.config?.url?.includes('/admin/login')
    // Login salah juga 401 di sebagian backend; jangan alihkan, biarkan form menampilkan pesannya.
    if (status === 401 && !isLoginRequest) {
      tokenStore.clear()
      onUnauthorized()
    }
    return Promise.reject(error)
  },
)

/**
 * Ubah galat axios menjadi bentuk yang seragam untuk tampilan:
 * { status, pesan, kolom } dengan kolom = { namaField: 'pesan pertama' }.
 */
export function bacaGalat(error) {
  const status = error.response?.status ?? 0
  const data = error.response?.data
  const kolom = {}

  if (status === 422 && data?.errors) {
    for (const [nama, pesan] of Object.entries(data.errors)) {
      kolom[nama] = Array.isArray(pesan) ? pesan[0] : String(pesan)
    }
  }

  let pesan
  if (!error.response) {
    pesan =
      error.code === 'ECONNABORTED'
        ? 'Permintaan terlalu lama. Coba lagi sebentar lagi.'
        : 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.'
  } else if (status === 401) {
    pesan = data?.message || 'Sesi Anda berakhir. Silakan masuk kembali.'
  } else if (status === 404) {
    pesan = data?.message || 'Data tidak ditemukan.'
  } else if (status === 422) {
    pesan = data?.message || 'Data yang dikirim belum valid.'
  } else if (status === 429) {
    pesan = 'Terlalu banyak permintaan. Tunggu sebentar lalu coba lagi.'
  } else if (status >= 500) {
    pesan = 'Terjadi gangguan pada server. Coba lagi nanti.'
  } else {
    pesan = data?.message || 'Terjadi kesalahan. Coba lagi.'
  }

  return { status, pesan, kolom }
}

export default api
