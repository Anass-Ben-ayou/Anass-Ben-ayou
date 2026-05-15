import React, { useState } from 'react'
import { Link, Navigate, useNavigate } from 'react-router-dom'
import toast from 'react-hot-toast'
import { useAuth } from '../contexts/AuthContext'
import AuthServiceBar from '../auth/AuthServiceBar'
import './Login.css'
import './Register.css'

const Register = () => {
  const [formData, setFormData] = useState({
    nom: '',
    prenom: '',
    email: '',
    password: '',
    password_confirmation: '',
    telephone: ''
  })
  const [errorMessage, setErrorMessage] = useState('')
  const [loading, setLoading] = useState(false)
  const { user, loading: authLoading, register } = useAuth()
  const navigate = useNavigate()

  if (!authLoading && user) {
    return <Navigate to="/dashboard" replace />
  }

  const handleChange = (event) => {
    if (errorMessage) setErrorMessage('')
    setFormData({ ...formData, [event.target.name]: event.target.value })
  }

  const handleSubmit = async (event) => {
    event.preventDefault()
    setLoading(true)

    try {
      await register(formData)
      toast.success('Inscription reussie')
      navigate('/dashboard')
    } catch (error) {
      const validationErrors = error.response?.data?.errors
      const firstValidationError = validationErrors
        ? Object.values(validationErrors).flat()[0]
        : null
      const message = firstValidationError || error.response?.data?.message || error.message || 'Erreur lors de l inscription'
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
          <span className="chip">Inscription</span>
          <h1>Creez votre univers lumineux.</h1>
          <p>
            Rejoignez Solarlight pour sauvegarder vos favoris, suivre vos achats
            et transformer votre interieur avec une boutique plus inspiree.
          </p>
        </div>

        <div className="auth-card">
          <div className="auth-header">
            <h2>Ouvrir un compte</h2>
            <p>Quelques informations suffisent pour commencer.</p>
          </div>

          <form onSubmit={handleSubmit} className="auth-form">
            <div className="form-row">
              <div className="form-group">
                <label>Nom</label>
                <input type="text" name="nom" value={formData.nom} onChange={handleChange} required />
              </div>
              <div className="form-group">
                <label>Prenom</label>
                <input type="text" name="prenom" value={formData.prenom} onChange={handleChange} required />
              </div>
            </div>

            <div className="form-group">
              <label>Adresse e-mail</label>
              <input type="email" name="email" value={formData.email} onChange={handleChange} required />
            </div>

            <div className="form-group">
              <label>Telephone</label>
              <input type="tel" name="telephone" value={formData.telephone} onChange={handleChange} required />
            </div>

            <div className="form-group">
              <label>Mot de passe</label>
              <input type="password" name="password" value={formData.password} onChange={handleChange} required />
            </div>

            <div className="form-group">
              <label>Confirmation du mot de passe</label>
              <input
                type="password"
                name="password_confirmation"
                value={formData.password_confirmation}
                onChange={handleChange}
                required
              />
            </div>

            {errorMessage && <p className="auth-error">{errorMessage}</p>}

            <button type="submit" disabled={loading} className="auth-btn">
              {loading ? 'Creation en cours...' : 'Creer mon compte'}
            </button>
          </form>

          <p className="auth-footer">
            Vous avez deja un compte ? <Link to="/login">Se connecter</Link>
          </p>
        </div>
      </div>
      <AuthServiceBar />
    </div>
  )
}

export default Register
