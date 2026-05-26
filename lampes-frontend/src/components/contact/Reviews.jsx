import React, { useState } from 'react'
import { FaArrowRight, FaStar } from 'react-icons/fa'
import toast from 'react-hot-toast'
import { useNavigate } from 'react-router-dom'
import { productService } from '../../services/productService'
import './Reviews.css'

// Displays public product reviews and allows sending a new one.
const Reviews = ({ reviews, loading, error, purchasedProducts = [], loadingPurchasedProducts = false, user, onReviewCreated }) => {
  const navigate = useNavigate()
  const [formData, setFormData] = useState({
    id_produit: '',
    comment: '',
    rating: 5
  })
  const [submitting, setSubmitting] = useState(false)
  const hasReviewableProducts = purchasedProducts.some((product) => !product.has_review)

  const handleChange = (event) => {
    setFormData((current) => ({
      ...current,
      [event.target.name]: event.target.value
    }))
  }

  const handleSubmit = async (event) => {
    event.preventDefault()

    if (!user) {
      toast.error('Veuillez vous connecter pour laisser un avis produit')
      navigate('/login')
      return
    }

    if (!formData.id_produit) {
      toast.error('Choisissez un produit achete')
      return
    }

    setSubmitting(true)

    try {
      const response = await productService.createProductReview({
        id_produit: Number(formData.id_produit),
        note: Number(formData.rating),
        commentaire: formData.comment.trim()
      })
      toast.success(response.message || 'Avis ajoute avec succes')
      setFormData({
        id_produit: '',
        comment: '',
        rating: 5
      })
      await onReviewCreated?.()
    } catch (submitError) {
      const firstError = Object.values(submitError.response?.data?.errors || {})?.[0]?.[0]
      toast.error(firstError || submitError.response?.data?.message || 'Impossible d envoyer l avis')
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <section className="reviews-panel">
      <div className="glass-card reviews-list-panel">
        <div className="reviews-heading">
          <span className="chip"><FaStar /> Avis produits</span>
          <h2>Ce que nos clients pensent des produits</h2>
        </div>

        {loading ? (
          <div className="loading-spinner"></div>
        ) : error ? (
          <p>{error}</p>
        ) : reviews.length === 0 ? (
          <p className="reviews-empty">Aucun avis produit pour le moment. Le prochain avis envoye apparaitra ici.</p>
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
        <h3><FaStar /> Laisser un avis</h3>
        <label>
          <span>Produit achete</span>
          <select
            name="id_produit"
            value={formData.id_produit}
            onChange={handleChange}
            required
            disabled={!user || loadingPurchasedProducts || !hasReviewableProducts}
          >
            <option value="">
              {loadingPurchasedProducts
                ? 'Chargement de vos produits...'
                : user
                  ? 'Choisir un produit'
                  : 'Connectez-vous pour choisir un produit'}
            </option>
            {purchasedProducts.map((product) => (
              <option key={product.id} value={product.id} disabled={product.has_review}>
                {product.name}{product.has_review ? ' - avis deja envoye' : ''}
              </option>
            ))}
          </select>
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
          <span>Votre avis sur le produit</span>
          <textarea name="comment" rows="5" value={formData.comment} onChange={handleChange} placeholder="Partagez votre avis sur le produit..." required />
        </label>
        <button type="submit" className="btn-primary" disabled={submitting}>
          {submitting ? 'Envoi en cours...' : 'Publier mon avis'}
          <FaArrowRight />
        </button>
        {!user ? (
          <p className="review-form-note">Connectez-vous pour publier un avis sur un produit achete.</p>
        ) : !loadingPurchasedProducts && purchasedProducts.length === 0 ? (
          <p className="review-form-note">Aucun produit achete disponible pour laisser un avis.</p>
        ) : !loadingPurchasedProducts && !hasReviewableProducts ? (
          <p className="review-form-note">Vous avez deja laisse un avis pour tous vos produits achetes.</p>
        ) : null}
      </form>
    </section>
  )
}

export default Reviews
