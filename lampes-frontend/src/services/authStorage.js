const TOKEN_KEY = 'auth_session'
const EXPIRY_KEY = 'auth_session_expires_at'
const SESSION_DURATION_MS = 8 * 60 * 60 * 1000

const hasWindow = typeof window !== 'undefined'

const readStorage = (key) => {
  if (!hasWindow) return null
  return window.sessionStorage.getItem(key)
}

const writeStorage = (key, value) => {
  if (!hasWindow) return
  window.sessionStorage.setItem(key, value)
}

const removeStorage = (key) => {
  if (!hasWindow) return
  window.sessionStorage.removeItem(key)
}

export const getStoredToken = () => {
  const token = readStorage(TOKEN_KEY)
  const expiry = Number(readStorage(EXPIRY_KEY) || 0)

  if (!token || !expiry || Date.now() >= expiry) {
    clearStoredToken()
    return null
  }

  return token
}

export const storeToken = (token) => {
  writeStorage(TOKEN_KEY, token || 'authenticated')
  writeStorage(EXPIRY_KEY, String(Date.now() + SESSION_DURATION_MS))
}

export const clearStoredToken = () => {
  removeStorage(TOKEN_KEY)
  removeStorage(EXPIRY_KEY)
}

export const refreshStoredToken = () => {
  const token = readStorage(TOKEN_KEY)
  if (!token) return
  writeStorage(EXPIRY_KEY, String(Date.now() + SESSION_DURATION_MS))
}
