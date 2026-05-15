import React, { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import toast from 'react-hot-toast'
import { api } from '../../services/api'
import './Login.css'

const ForgotPassword = () => {
  const [email, setEmail] = useState('')
  const [errorMessage, setErrorMessage] = useState('')
  const [loading, setLoading] = useState(false)
  const navigate = useNavigate()

  const handleSubmit = async (event) => {
    event.preventDefault()
    setErrorMessage('')
    setLoading(true)

    try {
      const response = await api.post('/auth/forgot-password', {
        email: email.trim().toLowerCase(),
      })

      sessionStorage.setItem('reset_email', email.trim().toLowerCase())
      sessionStorage.removeItem('reset_code')
      toast.success(response.data?.message || 'Si cet email existe, un code de reinitialisation a ete envoye.')
      navigate('/verify-reset-code')
    } catch (error) {
      const validationErrors = error.response?.data?.errors
      const firstValidationError = validationErrors ? Object.values(validationErrors).flat()[0] : null
      const message = firstValidationError || error.response?.data?.message || 'Impossible d envoyer le code.'
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
          <span className="chip">Reinitialisation</span>
          <h1>Recuperez votre acces.</h1>
          <p>Entrez votre adresse e-mail pour recevoir un code de verification valable 10 minutes.</p>
        </div>

        <div className="auth-card">
          <div className="auth-header">
            <h2>Mot de passe oublie</h2>
            <p>Nous vous enverrons un code de reinitialisation par e-mail.</p>
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
              />
            </div>

            {errorMessage ? <p className="auth-error">{errorMessage}</p> : null}

            <button type="submit" disabled={loading} className="auth-btn">
              {loading ? 'Envoi en cours...' : 'Envoyer le code'}
            </button>
          </form>

          <p className="auth-footer">
            Vous vous souvenez de votre mot de passe ? <Link to="/login">Retour a la connexion</Link>
          </p>
        </div>
      </div>
    </div>
  )
}

export default ForgotPassword
