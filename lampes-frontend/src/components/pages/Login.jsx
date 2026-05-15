import React, { useState } from 'react'
import { Link, Navigate, useLocation, useNavigate } from 'react-router-dom'
import toast from 'react-hot-toast'
import { useAuth } from '../contexts/AuthContext'
import AuthServiceBar from '../auth/AuthServiceBar'
import './Login.css'

const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

const Login = () => {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [errorMessage, setErrorMessage] = useState('')
  const [loading, setLoading] = useState(false)
  const { user, loading: authLoading, login } = useAuth()
  const navigate = useNavigate()
  const location = useLocation()

  if (!authLoading && user) {
    return <Navigate to="/dashboard" replace />
  }

  const handleSubmit = async (event) => {
    event.preventDefault()
    const normalizedEmail = email.trim().toLowerCase()
    const normalizedPassword = password.trim()

    if (!EMAIL_PATTERN.test(normalizedEmail)) {
      setErrorMessage('Veuillez saisir une adresse e-mail valide.')
      return
    }

    if (normalizedPassword.length < 8) {
      setErrorMessage('Le mot de passe doit contenir au moins 8 caracteres.')
      return
    }

    setErrorMessage('')
    setLoading(true)

    try {
      await login(normalizedEmail, normalizedPassword)
      toast.success('Connexion reussie')
      navigate(location.state?.from?.pathname || '/dashboard', { replace: true })
    } catch (error) {
      const validationErrors = error.response?.data?.errors
      const firstValidationError = validationErrors
        ? Object.values(validationErrors).flat()[0]
        : null
      const message = firstValidationError || error.response?.data?.message || error.message || 'Erreur de connexion'
      setErrorMessage(message)
      toast.error(message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="auth-page">
      <div className="auth-shell glass-card">
        <div className="auth-copy">
          <span className="chip">Connexion</span>
          <h1>Heureux de vous revoir.</h1>
          <p>
            Connectez-vous pour retrouver votre panier, vos informations et vos
            selections de luminaires preferees.
          </p>
        </div>

        <div className="auth-card">
          <div className="auth-header">
            <h2>Se connecter</h2>
            <p>Accedez a votre espace client Solarlight.</p>
          </div>

          <form onSubmit={handleSubmit} className="auth-form">
            <div className="form-group">
              <label>Adresse e-mail</label>
              <input
                type="email"
                value={email}
                onChange={(event) => {
                  setEmail(event.target.value)
                  if (errorMessage) setErrorMessage('')
                }}
                required
                placeholder="exemple@solarlight.ma"
                autoComplete="email"
                inputMode="email"
                maxLength={255}
              />
            </div>

            <div className="form-group">
              <label>Mot de passe</label>
              <input
                type="password"
                value={password}
                onChange={(event) => {
                  setPassword(event.target.value)
                  if (errorMessage) setErrorMessage('')
                }}
                required
                placeholder="Votre mot de passe"
                autoComplete="current-password"
                minLength={8}
                maxLength={72}
              />
            </div>

            <div className="auth-inline-link">
              <Link to="/forgot-password">Mot de passe oublie ?</Link>
            </div>

            {errorMessage && <p className="auth-error">{errorMessage}</p>}

            <button type="submit" disabled={loading} className="auth-btn">
              {loading ? 'Connexion en cours...' : 'Entrer dans mon espace'}
            </button>
          </form>

          <p className="auth-footer">
            Vous n avez pas encore de compte ? <Link to="/register">Creer un compte</Link>
          </p>
        </div>
      </div>
      <AuthServiceBar />
    </div>
  )
}

export default Login
