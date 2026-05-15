import React, { createContext, useState, useContext, useEffect } from 'react'
import { api } from '../../services/api'
import {
  clearStoredToken,
  getStoredToken,
  storeToken
} from '../../services/authStorage'

const AuthContext = createContext()

export const useAuth = () => useContext(AuthContext)

// Keeps the authenticated user available across the app.
export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    // Skips the profile request when there is no active session marker.
    if (!getStoredToken()) {
      setLoading(false)
      return
    }

    fetchUser()
  }, [])

  // Restores the connected user from the API when a session exists.
  const fetchUser = async () => {
    setLoading(true)

    try {
      const response = await api.get('/me', { skipAuthRedirect: true })
      setUser(response.data.data)
      storeToken('authenticated')
    } catch (error) {
      clearStoredToken()
      setUser(null)
    } finally {
      setLoading(false)
    }
  }

  // Signs in the user and stores the local session marker.
  const login = async (email, password) => {
    const response = await api.post('/login', { email, password })
    const { client } = response.data.data
    storeToken('authenticated')
    setUser(client)
    return response.data
  }

  // Creates an account and stores the local session marker.
  const register = async (userData) => {
    const response = await api.post('/register', userData)
    const { client } = response.data.data
    storeToken('authenticated')
    setUser(client)
    return response.data
  }

  // Clears both the API session and the local session marker.
  const logout = async () => {
    try {
      await api.post('/logout')
    } catch {
      // Local session cleanup still happens if the API logout request has already expired.
    } finally {
      clearStoredToken()
      setUser(null)
    }
  }

  return (
    <AuthContext.Provider value={{ user, login, register, logout, loading }}>
      {children}
    </AuthContext.Provider>
  )
}

export default AuthProvider
