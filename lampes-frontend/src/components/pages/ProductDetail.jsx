import React, { useCallback, useEffect, useMemo, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import {
  FaChevronLeft,
  FaChevronRight,
  FaCheck,
  FaHeadset,
  FaHeart,
  FaLock,
  FaSearchPlus,
  FaShieldAlt,
  FaShoppingCart,
  FaStar,
  FaSyncAlt,
  FaTruck
} from 'react-icons/fa'
import toast from 'react-hot-toast'
import { productService } from '../../services/productService'
import { useAuth } from '../contexts/AuthContext'
import { api } from '../../services/api'
import { notifyCartUpdated } from '../../services/cartEvents'
import { PRODUCT_IMAGE_FALLBACK, resolveProductImage } from '../../utils/productImages'
import './ProductDetail.css'

const normalizeDescriptionText = (value = '') => (
  value
    .replace(/\r\n/g, '\n')
    .replace(/\r/g, '\n')
    .trim()
)

const buildDescriptionBlocks = (rawDescription) => (
  normalizeDescriptionText(rawDescription)
    .split(/\n{2,}/)
    .map((block) => block.trim())
    .filter(Boolean)
)

const detailBenefits = [
  [<FaLock />, 'Paiement securise', 'Commande protegee pour chaque achat'],
  [<FaTruck />, 'Livraison express', 'Preparation rapide partout au Maroc'],
  [<FaShieldAlt />, 'Garantie 2 ans', 'Concu pour un usage quotidien exterieur'],
  [<FaHeadset />, 'Service client', 'Accompagnement avant et apres votre commande']
]

const fallbackSpecs = [
  ['Puissance', '24W'],
  ['Flux lumineux', '2400 lumens'],
  ['Batterie', 'Lithium 3.2V / 18Ah'],
  ['Temps de charge', '6-8 heures'],
  ['Autonomie', '10-12 heures'],
  ['Etancheite', 'IP65'],
  ['Materiau', 'Aluminium + ABS'],
  ['Hauteur recommandee', '4-6 metres']
]

const ProductDetail = () => {
  const { id } = useParams()
  const navigate = useNavigate()
  const { user } = useAuth()
  const [product, setProduct] = useState(null)
  const [quantity, setQuantity] = useState(1)
  const [loading, setLoading] = useState(true)
  const [addingToCart, setAddingToCart] = useState(false)
  const [activeTab, setActiveTab] = useState('description')
  const [activeImageIndex, setActiveImageIndex] = useState(0)
  const [reviews, setReviews] = useState([])
  const [loadingReviews, setLoadingReviews] = useState(false)
  const [submittingReview, setSubmittingReview] = useState(false)
  const [reviewForm, setReviewForm] = useState({
    note: '5',
    commentaire: ''
  })

  const refreshProductReviews = useCallback(async () => {
    setLoadingReviews(true)

    try {
      const reviewData = await productService.getProductReviews(id)
      const reviewItems = Array.isArray(reviewData?.avis?.data) ? reviewData.avis.data : []

      setReviews(reviewItems)
      setProduct((current) => current ? {
        ...current,
        note_moyenne: reviewData?.note_moyenne ?? current.note_moyenne,
        nombre_avis: reviewData?.total_avis ?? current.nombre_avis
      } : current)
    } catch (error) {
      toast.error('Impossible de charger les avis')
    } finally {
      setLoadingReviews(false)
    }
  }, [id])

  useEffect(() => {
    const fetchProduct = async () => {
      try {
        const response = await productService.getProduct(id)
        setProduct(response)
      } catch (error) {
        toast.error('Produit introuvable')
        navigate('/products')
      } finally {
        setLoading(false)
      }
    }

    fetchProduct()
  }, [id, navigate])

  useEffect(() => {
    refreshProductReviews()
  }, [refreshProductReviews])

  const image = resolveProductImage(product)
  const galleryImages = useMemo(() => {
    const candidates = Array.isArray(product?.gallery_images)
      ? product.gallery_images
      : []

    const images = [...candidates, image]
      .filter(Boolean)
      .filter((value, index, array) => array.indexOf(value) === index)

    return images
  }, [image, product])

  useEffect(() => {
    setActiveImageIndex(0)
  }, [galleryImages])

  const activeImage = galleryImages[activeImageIndex] || galleryImages[0]
  const hasProductImages = galleryImages.length > 0

  const specifications = useMemo(() => {
    const entries = Object.entries(product?.specifications || {})
      .filter(([, value]) => value !== null && value !== undefined && value !== '')

    return entries.length ? entries : fallbackSpecs
  }, [product])

  const descriptionBlocks = useMemo(
    () => buildDescriptionBlocks(product?.description),
    [product]
  )

  const productLeadDescription = useMemo(() => {
    const firstBlock = descriptionBlocks[0]
    return firstBlock || 'Un luminaire au design soigne pour transformer vos espaces avec elegance.'
  }, [descriptionBlocks])

  const addToCart = async () => {
    if (!user) {
      toast.error('Veuillez vous connecter pour ajouter ce produit au panier')
      navigate('/login')
      return
    }

    setAddingToCart(true)
    try {
      await api.post('/cart/add', {
        id_produit: product.id_produit,
        quantite: quantity
      })
      notifyCartUpdated()
      toast.success('Produit ajoute au panier')
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible d ajouter ce produit')
    } finally {
      setAddingToCart(false)
    }
  }

  const handleReviewChange = (event) => {
    const { name, value } = event.target

    setReviewForm((current) => ({
      ...current,
      [name]: value
    }))
  }

  const handleReviewSubmit = async (event) => {
    event.preventDefault()

    if (!user) {
      toast.error('Veuillez vous connecter pour laisser un avis')
      navigate('/login')
      return
    }

    setSubmittingReview(true)

    try {
      await productService.createProductReview({
        id_produit: product.id_produit,
        note: Number(reviewForm.note),
        commentaire: reviewForm.commentaire.trim()
      })

      toast.success('Avis ajoute avec succes')
      setReviewForm({
        note: '5',
        commentaire: ''
      })
      await refreshProductReviews()
      setActiveTab('avis')
    } catch (error) {
      const firstError = Object.values(error.response?.data?.errors || {})?.[0]?.[0]
      toast.error(firstError || error.response?.data?.message || 'Impossible d ajouter votre avis')
    } finally {
      setSubmittingReview(false)
    }
  }

  const formatPrice = (price) => (
    new Intl.NumberFormat('fr-MA', {
      style: 'currency',
      currency: 'MAD'
    }).format(price)
  )

  const showGalleryNavigation = galleryImages.length > 1
  const canReview = Boolean(user)

  const renderStars = (rating) => (
    <div className="stars">
      {[...Array(5)].map((_, index) => (
        <FaStar
          key={index}
          color={index < Math.round(Number(rating) || 0) ? '#f3b232' : '#e6dccd'}
        />
      ))}
    </div>
  )

  const formatReviewDate = (date) => (
    date ? new Date(date).toLocaleDateString('fr-FR') : ''
  )

  const getReviewerName = (review) => {
    const fullName = `${review.client?.prenom || ''} ${review.client?.nom || ''}`.trim()

    return fullName || review.client?.email || 'Utilisateur'
  }

  const goToPreviousImage = () => {
    setActiveImageIndex((current) => (
      current === 0 ? galleryImages.length - 1 : current - 1
    ))
  }

  const goToNextImage = () => {
    setActiveImageIndex((current) => (
      current === galleryImages.length - 1 ? 0 : current + 1
    ))
  }

  if (loading) {
    return (
      <div className="product-detail-loading">
        <div className="loading-spinner"></div>
      </div>
    )
  }

  if (!product) return null

  return (
    <div className="product-detail-page">
      <div className="container">
        <div className="detail-breadcrumb glass-card">
          <span>Accueil</span>
          <span>/</span>
          <span>Boutique</span>
          <span>/</span>
          <strong>{product.nom}</strong>
        </div>

        <div className="product-detail-grid">
          {hasProductImages ? (
            <section className="detail-gallery glass-card">
              <div className="detail-main-image">
                <button type="button" className="detail-zoom-btn" aria-label="Agrandir l image">
                  <FaSearchPlus />
                </button>
                {showGalleryNavigation ? (
                  <>
                    <span className="gallery-count">
                      {activeImageIndex + 1}/{galleryImages.length}
                    </span>
                    <button
                      type="button"
                      className="gallery-nav gallery-nav-prev"
                      onClick={goToPreviousImage}
                      aria-label="Image precedente"
                    >
                      <FaChevronLeft />
                    </button>
                    <button
                      type="button"
                      className="gallery-nav gallery-nav-next"
                      onClick={goToNextImage}
                      aria-label="Image suivante"
                    >
                      <FaChevronRight />
                    </button>
                  </>
                ) : null}
                <img
                  src={activeImage}
                  alt={product.nom || product.name}
                  onError={(event) => {
                    event.currentTarget.src = PRODUCT_IMAGE_FALLBACK
                  }}
                />
              </div>

              <div className="detail-thumbs">
                {galleryImages.map((galleryImage, index) => (
                  <button
                    key={`${galleryImage}-${index}`}
                    type="button"
                    className={`detail-thumb ${activeImageIndex === index ? 'active' : ''}`}
                    onClick={() => setActiveImageIndex(index)}
                  >
                    <img
                      src={galleryImage}
                      alt={`${product.nom || product.name} vue ${index + 1}`}
                      onError={(event) => {
                        event.currentTarget.src = PRODUCT_IMAGE_FALLBACK
                      }}
                    />
                  </button>
                ))}
              </div>
            </section>
          ) : null}

          <section className="detail-panel glass-card">
            <span className="chip detail-category-chip">{product.categorie?.nom || 'Lampadaires solaires'}</span>
            <h1 className="product-detail-title">{product.nom}</h1>

            <div className="product-detail-rating">
              <div className="stars">
                {[...Array(5)].map((_, index) => (
                  <FaStar
                    key={index}
                    color={index < Math.round(product.note_moyenne || 4) ? '#f3b232' : '#e6dccd'}
                  />
                ))}
              </div>
              <span>({product.nombre_avis || 128} avis)</span>
            </div>

            <div className="product-price-row">
              <div className="product-detail-price">{formatPrice(product.prix)}</div>
              {product.old_price && Number(product.old_price) > Number(product.prix) ? (
                <div className="product-old-price">{formatPrice(product.old_price)}</div>
              ) : null}
            </div>

            <div className="stock-info">
              <span></span>
              {product.stock > 0 ? 'En stock' : 'Rupture de stock'}
            </div>

            <div className="product-detail-quantity">
              <label>Quantite</label>
              <div className="quantity-row">
                <div className="quantity-control">
                  <button type="button" onClick={() => setQuantity(Math.max(1, quantity - 1))}>-</button>
                  <span>{quantity}</span>
                  <button type="button" onClick={() => setQuantity(quantity + 1)}>+</button>
                </div>

                <button
                  type="button"
                  onClick={addToCart}
                  disabled={addingToCart || product.stock === 0}
                  className="add-to-cart-main"
                >
                  <FaShoppingCart />
                  {addingToCart ? 'Ajout...' : 'Ajouter au panier'}
                </button>
              </div>
            </div>

            <div className="detail-actions">
              <button type="button" className="buy-now-btn">
                <FaHeart />
                Ajouter a la liste de souhaits
              </button>
            </div>

            <div className="product-features">
              <div className="feature-item">
                <FaTruck />
                <div>
                  <strong>Livraison rapide</strong>
                  <p>Livraison partout au Maroc sous 24h a 48h</p>
                </div>
              </div>
              <div className="feature-item">
                <FaLock />
                <div>
                  <strong>Paiement securise</strong>
                  <p>Paiement a la livraison ou en ligne</p>
                </div>
              </div>
              <div className="feature-item">
                <FaShieldAlt />
                <div>
                  <strong>Garantie 2 ans</strong>
                  <p>Sur tous nos lampadaires solaires</p>
                </div>
              </div>
              <div className="feature-item">
                <FaSyncAlt />
                <div>
                  <strong>Retour facile</strong>
                  <p>Retour sous 7 jours si vous n etes pas satisfait</p>
                </div>
              </div>
            </div>
          </section>
        </div>

        <section className="detail-tabs glass-card">
          <div className="detail-tab-head">
            <button
              type="button"
              className={activeTab === 'description' ? 'active' : ''}
              onClick={() => setActiveTab('description')}
            >
              Description
            </button>
            <button
              type="button"
              className={activeTab === 'specifications' ? 'active' : ''}
              onClick={() => setActiveTab('specifications')}
            >
              Caracteristiques
            </button>
            <button
              type="button"
              className={activeTab === 'avis' ? 'active' : ''}
              onClick={() => setActiveTab('avis')}
            >
              Avis
            </button>
          </div>

          {activeTab === 'description' ? (
            <div className="detail-tab-body">
              <article className="description-article">
                <div className="description-content-flow">
                  <h2>Une solution d eclairage solaire performante et durable</h2>
                  {(descriptionBlocks.length ? descriptionBlocks : [
                    productLeadDescription,
                    'Equipe d un panneau solaire haute performance et d une batterie lithium longue duree, il offre un eclairage puissant et fiable toute la nuit, sans consommation d electricite.',
                    'Son design moderne et robuste s integre parfaitement a tous vos amenagements exterieurs.'
                  ]).map((content, index) => (
                    <p key={index}>{content}</p>
                  ))}
                  <div className="description-spec-grid">
                    {specifications.slice(0, 8).map(([label, value]) => (
                      <span key={label}><FaCheck /> {label} : {String(value)}</span>
                    ))}
                  </div>
                </div>
              </article>
            </div>
          ) : activeTab === 'specifications' ? (
            <div className="detail-tab-body">
              <div className="spec-table">
                {specifications.map(([label, value]) => (
                  <div key={label} className="spec-row">
                    <span className="spec-label">{label}</span>
                    <span className="spec-value">{String(value)}</span>
                  </div>
                ))}
                </div>
            </div>
          ) : (
            <div className="detail-tab-body">
              <div className="product-reviews-layout">
                <section className="product-review-list">
                  <div className="product-review-summary">
                    <div>
                      <strong>{product.note_moyenne || 0}/5</strong>
                      <span>{product.nombre_avis || 0} avis clients</span>
                    </div>
                    {renderStars(product.note_moyenne || 0)}
                  </div>

                  {loadingReviews ? (
                    <div className="product-review-empty">Chargement des avis...</div>
                  ) : reviews.length ? (
                    reviews.map((review) => (
                      <article key={review.id_avis} className="product-review-item">
                        <div className="product-review-heading">
                          <div>
                            {renderStars(review.note)}
                            <strong>{getReviewerName(review)}</strong>
                          </div>
                          <time>{formatReviewDate(review.date_avis || review.created_at)}</time>
                        </div>
                        <p>{review.commentaire}</p>
                      </article>
                    ))
                  ) : (
                    <div className="product-review-empty">Aucun avis pour ce produit pour le moment.</div>
                  )}
                </section>

                {canReview ? (
                  <form className="product-review-form" onSubmit={handleReviewSubmit}>
                    <h3>Laisser un avis</h3>
                    <label>
                      <span>Note</span>
                      <select name="note" value={reviewForm.note} onChange={handleReviewChange} required>
                        <option value="5">5 etoiles</option>
                        <option value="4">4 etoiles</option>
                        <option value="3">3 etoiles</option>
                        <option value="2">2 etoiles</option>
                        <option value="1">1 etoile</option>
                      </select>
                    </label>
                    <label>
                      <span>Commentaire</span>
                      <textarea
                        name="commentaire"
                        rows="5"
                        value={reviewForm.commentaire}
                        onChange={handleReviewChange}
                        placeholder="Votre avis sur ce produit"
                        required
                      />
                    </label>
                    <button type="submit" disabled={submittingReview}>
                      {submittingReview ? 'Envoi en cours...' : 'Envoyer mon avis'}
                    </button>
                  </form>
                ) : (
                  <aside className="product-review-form product-review-locked">
                    <h3>Avis clients</h3>
                    <p>Connectez-vous et commandez ce produit pour partager votre avis.</p>
                    <button type="button" onClick={() => navigate('/login')}>
                      Se connecter
                    </button>
                  </aside>
                )}
              </div>
            </div>
          )}
        </section>

        <section className="product-detail-benefits">
          {detailBenefits.map(([icon, title, text]) => (
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
    </div>
  )
}

export default ProductDetail
