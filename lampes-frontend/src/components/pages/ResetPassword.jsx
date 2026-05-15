import React, { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import toast from 'react-hot-toast'
import { api } from '../../services/api'
import './Login.css'

const ResetPassword = () => {
  const [email, setEmail] = useState('')
  const [code, setCode] = useState('')
  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
  const [errorMessage, setErrorMessage] = useState('')
  const [loading, setLoading] = useState(false)
  const navigate = useNavigate()

  useEffect(() => {
    setEmail(sessionStorage.getItem('reset_email') || '')
    setCode(sessionStorage.getItem('reset_code') || '')
  }, [])

  const handleSubmit = async (event) => {
    event.preventDefault()
    setErrorMessage('')
    setLoading(true)

    try {
      const response = await api.post('/auth/reset-password', {
        email: email.trim().toLowerCase(),
        code: code.trim(),
        password,
        password_confirmation: passwordConfirmation,
      })

      sessionStorage.removeItem('reset_email')
      sessionStorage.removeItem('reset_code')
      toast.success(response.data?.message || 'Mot de passe reinitialise avec succes.')
      navigate('/login')
    } catch (error) {
      const validationErrors = error.response?.data?.errors
      const firstValidationError = validationErrors ? Object.values(validationErrors).flat()[0] : null
      const message = firstValidationError || error.response?.data?.message || 'Impossible de reinitialiser le mot de passe.'
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
          <span className="chip">Nouveau mot de passe</span>
          <h1>Choisissez un nouveau mot de passe.</h1>
          <p>Créez un mot de passe securise d au moins 8 caracteres pour proteger votre compte SolarLight.</p>
        </div>

        <div className="auth-card">
          <div className="auth-header">
            <h2>Reinitialiser le mot de passe</h2>
            <p>Entrez le code recu puis confirmez votre nouveau mot de passe.</p>
          </div>

          <form onSubmit={handleSubmit} className="auth-form">
            <div className="form-group">
              <label>Adresse e-mail</label>
              <input
                type="email"
                value={email}
                onChange={(event) => setEmail(event.target.value)}
                required
                placeholder="exemple@solarlight.ma"
              />
            </div>

            <div className="form-group">
              <label>Code de verification</label>
              <input
                type="text"
                value={code}
                onChange={(event) => setCode(event.target.value.replace(/\D+/g, '').slice(0, 6))}
                required
                placeholder="123456"
                inputMode="numeric"
                maxLength={6}
              />
            </div>

            <div className="form-group">
              <label>Nouveau mot de passe</label>
              <input
                type="password"
                value={password}
                onChange={(event) => setPassword(event.target.value)}
                required
                minLength={8}
                placeholder="Nouveau mot de passe"
              />
            </div>

            <div className="form-group">
              <label>Confirmation du mot de passe</label>
              <input
                type="password"
                value={passwordConfirmation}
                onChange={(event) => setPasswordConfirmation(event.target.value)}
                required
                minLength={8}
                placeholder="Confirmez le mot de passe"
              />
            </div>

            {errorMessage ? <p className="auth-error">{errorMessage}</p> : null}

            <button type="submit" disabled={loading} className="auth-btn">
              {loading ? 'Reinitialisation...' : 'Mettre a jour le mot de passe'}
            </button>
          </form>

          <p className="auth-footer">
            Retour a la <Link to="/login">connexion</Link>
          </p>
        </div>
      </div>
    </div>
  )
}

export default ResetPassword
