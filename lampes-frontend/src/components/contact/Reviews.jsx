import React, { useState } from 'react'
import { FaArrowRight, FaComments, FaStar } from 'react-icons/fa'
import toast from 'react-hot-toast'
import { productService } from '../../services/productService'
import './Reviews.css'

// Displays public testimonials and allows sending a new one.
const Reviews = ({ reviews, loading, error }) => {
  const [formData, setFormData] = useState({
    customer_name: '',
    email: '',
    comment: '',
    rating: 5
  })
  const [submitting, setSubmitting] = useState(false)

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
      const response = await productService.createSiteReview(formData)
      toast.success(response.message || 'Avis envoye')
      setFormData({
        customer_name: '',
        email: '',
        comment: '',
        rating: 5
      })
    } catch (submitError) {
      toast.error(submitError.response?.data?.message || 'Impossible d envoyer l avis')
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <section className="reviews-panel">
      <div className="glass-card reviews-list-panel">
        <div className="reviews-heading">
          <span className="chip"><FaStar /> Avis clients</span>
          <h2>Ce que nos clients disent</h2>
        </div>

        {loading ? (
          <div className="loading-spinner"></div>
        ) : error ? (
          <p>{error}</p>
        ) : reviews.length === 0 ? (
          <p className="reviews-empty">Aucun temoignage pour le moment. Le prochain message envoye apparaitra ici.</p>
        ) : (
          <div className="reviews-list">
            {reviews.map((review) => (
              <article key={review.id} className="review-card">
                <div className="review-stars">
                  {Array.from({ length: 5 }, (_, index) => (
                    <FaStar key={index} className={index < review.rating ? 'filled' : ''} />
                  ))}
                </div>
                {review.title ? <h4>{review.title}</h4> : null}
                <p>{review.comment}</p>
                <strong>{review.customer_name}</strong>
                <span>{review.date}</span>
              </article>
            ))}
          </div>
        )}
      </div>

      <form className="glass-card review-form" onSubmit={handleSubmit}>
        <h3><FaComments /> Laisser un temoignage</h3>
        <label>
          <span>Nom</span>
          <input name="customer_name" value={formData.customer_name} onChange={handleChange} placeholder="Votre nom" required />
        </label>
        <label>
          <span>Email</span>
          <input name="email" type="email" value={formData.email} onChange={handleChange} placeholder="Votre email" />
        </label>
        <label>
          <span>Note</span>
          <select name="rating" value={formData.rating} onChange={handleChange}>
            <option value="5">5 etoiles</option>
            <option value="4">4 etoiles</option>
            <option value="3">3 etoiles</option>
            <option value="2">2 etoiles</option>
            <option value="1">1 etoile</option>
          </select>
        </label>
        <label>
          <span>Votre commentaire</span>
          <textarea name="comment" rows="5" value={formData.comment} onChange={handleChange} placeholder="Partagez votre experience..." required />
        </label>
        <button type="submit" className="btn-primary" disabled={submitting}>
          {submitting ? 'Envoi en cours...' : 'Envoyer mon avis'}
          <FaArrowRight />
        </button>
      </form>
    </section>
  )
}

export default Reviews
