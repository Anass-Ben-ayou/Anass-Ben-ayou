import axios from 'axios'
import {
  clearStoredToken,
  refreshStoredToken
} from './authStorage'

const API_URL = (
  process.env.REACT_APP_API_URL ||
  'http://localhost:8000/api/v1'
).replace(/\/+$/, '')
const PUBLIC_API_URL = API_URL.replace(/\/v1$/, '')

export const api = axios.create({
  baseURL: API_URL,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

export const publicApi = axios.create({
  baseURL: PUBLIC_API_URL,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

const csrfClient = axios.create({
  baseURL: API_URL,
  withCredentials: true,
  headers: {
    'Accept': 'application/json'
  }
})

const SAFE_METHODS = ['get', 'head', 'options']
const CSRF_COOKIE_NAME = 'XSRF-TOKEN'

let csrfRequest = null

const readCookie = (name) => {
  if (typeof document === 'undefined') return null

  const escapedName = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
  const match = document.cookie.match(new RegExp(`(?:^|; )${escapedName}=([^;]*)`))

  return match ? decodeURIComponent(match[1]) : null
}

const ensureCsrfToken = async () => {
  const currentToken = readCookie(CSRF_COOKIE_NAME)
  if (currentToken) return currentToken

  if (!csrfRequest) {
    csrfRequest = csrfClient.get('/csrf-token')
      .finally(() => {
        csrfRequest = null
      })
  }

  await csrfRequest
  return readCookie(CSRF_COOKIE_NAME)
}

const attachCsrfToken = async (config) => {
  const method = (config.method || 'get').toLowerCase()
  const requestUrl = config.url || ''

  if (!SAFE_METHODS.includes(method) && !requestUrl.includes('/csrf-token')) {
    const csrfToken = await ensureCsrfToken()

    if (csrfToken) {
      config.headers['X-CSRF-TOKEN'] = csrfToken
    }
  }

  return config
}

// Adds CSRF to every mutating API request, including public forms.
api.interceptors.request.use(
  async (config) => {
    return attachCsrfToken(config)
  },
  (error) => {
    return Promise.reject(error)
  }
)

publicApi.interceptors.request.use(
  async (config) => {
    return attachCsrfToken(config)
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Intercepteur pour les erreurs
api.interceptors.response.use(
  (response) => {
    refreshStoredToken()
    return response
  },
  (error) => {
    const requestUrl = error.config?.url || ''
    const isAuthRequest = requestUrl.includes('/login') || requestUrl.includes('/register')
    const skipAuthRedirect = Boolean(error.config?.skipAuthRedirect)

    if (error.response?.status === 401 && !isAuthRequest && !skipAuthRedirect) {
      clearStoredToken()
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  }
)
