const rupiah = new Intl.NumberFormat('id-ID', {
  style: 'currency',
  currency: 'IDR',
  maximumFractionDigits: 0,
})

export function formatRupiah(nilai) {
  return rupiah.format(Number(nilai) || 0)
}

/** "S, M, L, XL" -> ['S', 'M', 'L', 'XL'] (untuk chip ukuran). */
export function pecahUkuran(teks) {
  if (!teks) return []
  return String(teks)
    .split(',')
    .map((u) => u.trim())
    .filter(Boolean)
}
