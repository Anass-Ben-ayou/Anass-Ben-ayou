import React, { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import toast from 'react-hot-toast'
import { api } from '../../services/api'
import './Login.css'

const VerifyResetCode = () => {
  const [email, setEmail] = useState('')
  const [code, setCode] = useState('')
  const [errorMessage, setErrorMessage] = useState('')
  const [loading, setLoading] = useState(false)
  const navigate = useNavigate()

  useEffect(() => {
    const storedEmail = sessionStorage.getItem('reset_email') || ''
    setEmail(storedEmail)
  }, [])

  const handleSubmit = async (event) => {
    event.preventDefault()
    setErrorMessage('')
    setLoading(true)

    try {
      await api.post('/auth/verify-reset-code', {
        email: email.trim().toLowerCase(),
        code: code.trim(),
      })

      sessionStorage.setItem('reset_email', email.trim().toLowerCase())
      sessionStorage.setItem('reset_code', code.trim())
      toast.success('Code verifie avec succes')
      navigate('/reset-password')
    } catch (error) {
      const validationErrors = error.response?.data?.errors
      const firstValidationError = validationErrors ? Object.values(validationErrors).flat()[0] : null
      const message = firstValidationError || error.response?.data?.message || 'Code invalide.'
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
          <span className="chip">Verification</span>
          <h1>Confirmez votre code.</h1>
          <p>Saisissez le code a 6 chiffres recu par e-mail pour continuer la reinitialisation.</p>
        </div>

        <div className="auth-card">
          <div className="auth-header">
            <h2>Verifier le code</h2>
            <p>Le code expire 10 minutes apres son envoi.</p>
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
              />
            </div>

            <div className="form-group">
              <label>Code de verification</label>
              <input
                type="text"
                value={code}
                onChange={(event) => {
                  setCode(event.target.value.replace(/\D+/g, '').slice(0, 6))
                  if (errorMessage) setErrorMessage('')
                }}
                required
                placeholder="123456"
                inputMode="numeric"
                maxLength={6}
              />
            </div>

            {errorMessage ? <p className="auth-error">{errorMessage}</p> : null}

            <button type="submit" disabled={loading} className="auth-btn">
              {loading ? 'Verification...' : 'Verifier le code'}
            </button>
          </form>

          <p className="auth-footer">
            Vous n avez pas recu le code ? <Link to="/forgot-password">Renvoyer un code</Link>
          </p>
        </div>
      </div>
    </div>
  )
}

export default VerifyResetCode
