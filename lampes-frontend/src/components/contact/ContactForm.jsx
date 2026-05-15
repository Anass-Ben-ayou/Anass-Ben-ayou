import React, { useEffect, useState } from 'react'
import { FaArrowRight, FaEdit } from 'react-icons/fa'
import toast from 'react-hot-toast'
import { useAuth } from '../contexts/AuthContext'
import { productService } from '../../services/productService'
import './ContactForm.css'

// Handles the public contact form submission.
const ContactForm = () => {
  const { user } = useAuth()
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    subject: '',
    message: ''
  })
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    if (!user) {
      return
    }

    setFormData((current) => ({
      ...current,
      name: current.name || `${user.prenom || ''} ${user.nom || ''}`.trim(),
      email: current.email || user.email || ''
    }))
  }, [user])

  const handleChange = (event) => {
    setFormData((current) => ({
      ...current,
      [event.target.name]: event.target.value
    }))
  }

  const handleSubmit = async (event) => {
    event.preventDefault()
    setSubmitting(true)

    try {
      const response = await productService.sendContactMessage(formData)
      toast.success(response.message || 'Message envoye')
      setFormData({
        name: '',
        email: '',
        subject: '',
        message: ''
      })
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible d envoyer le message')
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <form className="contact-form glass-card" onSubmit={handleSubmit}>
      <h2><FaEdit /> Contactez-nous</h2>
      <div className="contact-form-grid">
        <label>
          <span>Nom complet</span>
          <input name="name" value={formData.name} onChange={handleChange} placeholder="Votre nom" required />
        </label>
        <label>
          <span>Email</span>
          <input name="email" type="email" value={formData.email} onChange={handleChange} placeholder="Votre email" required />
        </label>
        <label>
          <span>Sujet</span>
          <input name="subject" value={formData.subject} onChange={handleChange} placeholder="Sujet" required />
        </label>
        <label>
          <span>Message</span>
          <textarea name="message" value={formData.message} onChange={handleChange} placeholder="Votre message..." rows="6" required />
        </label>
      </div>
      <button type="submit" className="btn-primary" disabled={submitting}>
        <FaArrowRight />
        {submitting ? 'Envoi en cours...' : 'Envoyer le message'}
      </button>
    </form>
  )
}

export default ContactForm
