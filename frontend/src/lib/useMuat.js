import { ref } from 'vue'
import { bacaGalat } from './api'

/**
 * Pembungkus pemanggilan API dengan state memuat/galat.
 * const { data, memuat, galat, jalankan } = useMuat(() => api.get('/profil'))
 */
export function useMuat(pemanggil) {
  const data = ref(null)
  const memuat = ref(false)
  const galat = ref(null)

  async function jalankan(...args) {
    memuat.value = true
    galat.value = null
    try {
      const res = await pemanggil(...args)
      data.value = res.data
      return res.data
    } catch (e) {
      galat.value = bacaGalat(e)
      return null
    } finally {
      memuat.value = false
    }
  }

  return { data, memuat, galat, jalankan }
}
