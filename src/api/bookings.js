// Bookings API - Now uses backend with localStorage fallback
import { reservationsAPI } from '@/services/api'

const STORAGE_KEY = 'suvaya_bookings'

function loadLocal() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    return raw ? JSON.parse(raw) : { dates: [], deposits: [] }
  } catch (e) {
    return { dates: [], deposits: [] }
  }
}

function saveLocal(state) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(state))
}

export function getBookings() {
  return loadLocal().dates
}

export async function isDateBooked(date) {
  try {
    // Try backend first
    const response = await reservationsAPI.checkDate(date)
    return response.data.is_booked
  } catch (error) {
    // Fallback to localStorage
    console.warn('Using localStorage fallback for bookings')
    const s = loadLocal()
    return s.dates.includes(date)
  }
}

export async function addBooking(date, meta = {}) {
  try {
    // If this is a full reservation, send to backend
    if (meta.type === 'reservation' && meta.name && meta.email) {
      const response = await reservationsAPI.create({
        name: meta.name,
        email: meta.email,
        phone: meta.phone,
        date: date,
        time: meta.time,
        guests: meta.guests,
        notes: meta.notes
      })
      return response
    } else {
      // Otherwise save locally (for calendar quick bookings)
      const s = loadLocal()
      if (!s.dates.includes(date)) s.dates.push(date)
      s.deposits.push({ date, ...meta })
      saveLocal(s)
    }
  } catch (error) {
    console.error('Failed to add booking:', error)
    // Fallback to localStorage
    const s = loadLocal()
    if (!s.dates.includes(date)) s.dates.push(date)
    s.deposits.push({ date, ...meta })
    saveLocal(s)
  }
}

export function clearBookings() {
  saveLocal({ dates: [], deposits: [] })
}
