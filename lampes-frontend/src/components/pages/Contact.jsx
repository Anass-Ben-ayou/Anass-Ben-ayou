import React, { useEffect, useState } from 'react'
import { FaHeadset, FaLock, FaShieldAlt, FaTruck } from 'react-icons/fa'
import ContactForm from '../contact/ContactForm'
import Reviews from '../contact/Reviews'
import { productService } from '../../services/productService'
import './Contact.css'

const fallbackReviews = [
  {
    id: 'fallback-1',
    rating: 5,
    title: 'Excellent service !',
    comment: 'Produits de qualite et service client au top. Je recommande vivement.',
    customer_name: 'Ben ayou',
    date: '10 mai 2026'
  },
  {
    id: 'fallback-2',
    rating: 5,
    title: 'Tres belle experience',
    comment: 'Les lampes sont elegantes, faciles a installer et rendent super bien sur la terrasse.',
    customer_name: 'Nadia B.',
    date: '23 avril 2026'
  },
  {
    id: 'fallback-3',
    rating: 4,
    title: 'Livraison rapide et conforme',
    comment: 'Livraison rapide et produits conformes aux photos. J aime beaucoup la finition et l ambiance.',
    customer_name: 'Youssef A.',
    date: '10 avril 2026'
  },
  {
    id: 'fallback-4',
    rating: 5,
    title: 'Service professionnel',
    comment: 'Une boutique propre, moderne et rassurante. Le service client a ete reactif du debut a la fin.',
    customer_name: 'Salma E.',
    date: '28 mars 2026'
  }
]

const serviceCards = [
  [<FaLock />, 'Paiement securise', 'Commande protegee pour chaque achat'],
  [<FaTruck />, 'Livraison express', 'Preparation rapide partout au Maroc'],
  [<FaShieldAlt />, 'Garantie 2 ans', 'Concu pour un usage quotidien interieur et exterieur'],
  [<FaHeadset />, 'Service client', 'Accompagnement avant et apres votre commande']
]

const Contact = () => {
  const [reviews, setReviews] = useState([])
  const [loadingReviews, setLoadingReviews] = useState(true)
  const [reviewsError, setReviewsError] = useState('')

  useEffect(() => {
    const loadReviews = async () => {
      try {
        const data = await productService.getSiteReviews()
        setReviews(Array.isArray(data) ? data : [])
      } catch (error) {
        setReviewsError('Impossible de charger les avis pour le moment.')
      } finally {
        setLoadingReviews(false)
      }
    }

    loadReviews()
  }, [])

  const visibleReviews = reviews.length > 0 ? reviews : fallbackReviews

  return (
    <main className="contact-page">
      <div className="container">
        <section className="contact-hero">
          <div>
            <span className="chip">Contact</span>
            <h1>Parlons de votre projet d <span>eclairage solaire</span></h1>
            <p>
              Cette page reste volontairement simple : un formulaire de contact et les avis pour inspirer confiance
              sans surcharger l experience.
            </p>
          </div>
        </section>

        <section className="contact-main">
          <div className="contact-form-area">
            <ContactForm />
          </div>

          <Reviews
            reviews={visibleReviews}
            loading={loadingReviews}
            error={reviews.length > 0 ? reviewsError : ''}
          />
        </section>

        <section className="contact-service-strip">
          {serviceCards.map(([icon, title, text]) => (
            <article key={title}>
              <span>{icon}</span>
              <div>
                <strong>{title}</strong>
                <p>{text}</p>
              </div>
            </article>
          ))}
        </section>
      </div>
    </main>
  )
}

export default Contact
