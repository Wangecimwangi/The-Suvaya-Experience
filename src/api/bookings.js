// Simple localStorage-backed bookings API
const STORAGE_KEY = 'suvaya_bookings'

function load() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    return raw ? JSON.parse(raw) : { dates: [], deposits: [] }
  } catch (e) {
    return { dates: [], deposits: [] }
  }
}

function save(state) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(state))
}

export function getBookings() {
  return load().dates
}

export function isDateBooked(date) {
  const s = load()
  return s.dates.includes(date)
}

export function addBooking(date, meta = {}) {
  const s = load()
  if (!s.dates.includes(date)) s.dates.push(date)
  s.deposits.push({ date, ...meta })
  save(s)
}

export function clearBookings() {
  save({ dates: [], deposits: [] })
}
