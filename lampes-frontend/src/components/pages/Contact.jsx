import React, { useCallback, useEffect, useState } from 'react'
import { FaHeadset, FaLock, FaShieldAlt, FaTruck } from 'react-icons/fa'
import ContactForm from '../contact/ContactForm'
import Reviews from '../contact/Reviews'
import { useAuth } from '../contexts/AuthContext'
import { productService } from '../../services/productService'
import './Contact.css'

const fallbackReviews = [
  {
    id: 'fallback-1',
    rating: 5,
    title: 'Piquet solaire Iota 8W',
    comment: 'Tres bonne lumiere pour le jardin. Le produit tient bien la charge et donne un rendu propre le soir.',
    customer_name: 'Ben ayou',
    date: '10 mai 2026'
  },
  {
    id: 'fallback-2',
    rating: 5,
    title: 'Applique solaire murale',
    comment: 'Installation simple, finition elegante et detecteur reactif. Parfait pour l entree de la maison.',
    customer_name: 'Nadia B.',
    date: '23 avril 2026'
  },
  {
    id: 'fallback-3',
    rating: 4,
    title: 'Projecteur solaire exterieur',
    comment: 'Eclairage puissant et conforme aux photos. J aurais aime un cable un peu plus long, mais le produit est solide.',
    customer_name: 'Youssef A.',
    date: '10 avril 2026'
  },
  {
    id: 'fallback-4',
    rating: 5,
    title: 'Borne solaire de terrasse',
    comment: 'Belle ambiance autour de la terrasse. La lumiere est douce et le design reste discret en journee.',
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
  const { user, loading: loadingUser } = useAuth()
  const [reviews, setReviews] = useState([])
  const [loadingReviews, setLoadingReviews] = useState(true)
  const [reviewsError, setReviewsError] = useState('')
  const [purchasedProducts, setPurchasedProducts] = useState([])
  const [loadingPurchasedProducts, setLoadingPurchasedProducts] = useState(false)

  const loadReviews = useCallback(async () => {
    setLoadingReviews(true)

    try {
      const data = await productService.getSiteReviews()
      setReviews(Array.isArray(data) ? data : [])
      setReviewsError('')
    } catch (error) {
      setReviewsError('Impossible de charger les avis pour le moment.')
    } finally {
      setLoadingReviews(false)
    }
  }, [])

  const loadPurchasedProducts = useCallback(async () => {
    if (loadingUser || !user) {
      setPurchasedProducts([])
      setLoadingPurchasedProducts(false)
      return
    }

    setLoadingPurchasedProducts(true)

    try {
      const products = await productService.getPurchasedReviewProducts()
      setPurchasedProducts(Array.isArray(products) ? products : [])
    } catch (error) {
      setPurchasedProducts([])
    } finally {
      setLoadingPurchasedProducts(false)
    }
  }, [loadingUser, user])

  const refreshReviewArea = useCallback(async () => {
    await Promise.all([
      loadReviews(),
      loadPurchasedProducts()
    ])
  }, [loadPurchasedProducts, loadReviews])

  useEffect(() => {
    loadReviews()
  }, [loadReviews])

  useEffect(() => {
    loadPurchasedProducts()
  }, [loadPurchasedProducts])

  const visibleReviews = reviews.length > 0 ? reviews : fallbackReviews

  return (
    <main className="contact-page">
      <div className="container">
        <section className="contact-hero">
          <div>
            <span className="chip">Contact</span>
            <h1>Parlons de votre projet d <span>eclairage solaire</span></h1>
            <p>
              Contactez-nous pour une question ou consultez les avis produits partages par nos clients.
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
            purchasedProducts={purchasedProducts}
            loadingPurchasedProducts={loadingPurchasedProducts || loadingUser}
            user={user}
            onReviewCreated={refreshReviewArea}
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
