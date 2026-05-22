import React, { memo, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { FaShoppingCart, FaStar } from 'react-icons/fa'
import toast from 'react-hot-toast'
import { api } from '../../services/api'
import { useAuth } from '../contexts/AuthContext'
import { notifyCartUpdated } from '../../services/cartEvents'
import { resolveProductImage } from '../../utils/productImages'
import './ProductCard.css'

const priceFormatter = new Intl.NumberFormat('fr-MA', {
  style: 'currency',
  currency: 'MAD'
})

// Shows one product card in the catalog grid.
const ProductCard = ({ product }) => {
  const [imageLoaded, setImageLoaded] = useState(false)
  const [addingToCart, setAddingToCart] = useState(false)
  const navigate = useNavigate()
  const { user } = useAuth()
  const name = product.name || product.nom
  const image = resolveProductImage(product)
  const category = product.category?.name || product.categorie?.nom || product.categorie?.name || 'Collection signature'
  const description = product.short_description || product.description || 'Decouvrez ce luminaire solaire soigneusement selectionne.'
  const productId = product.id || product.id_produit
  const rating = Math.max(1, Math.round(product.note_moyenne || 4))
  const currentPrice = product.price ?? product.prix ?? 0
  const oldPrice = product.old_price

  // Adds the current product to the cart and refreshes the badge.
  const handleAddToCart = async () => {
    if (!user) {
      toast.error('Veuillez vous connecter pour ajouter un produit au panier')
      navigate('/login')
      return
    }

    setAddingToCart(true)

    try {
      await api.post('/cart/add', {
        id_produit: productId,
        quantite: 1
      })

      notifyCartUpdated()
      toast.success('Produit ajoute au panier')
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible d ajouter ce produit')
    } finally {
      setAddingToCart(false)
    }
  }

  return (
    <article className="product-card">
      {image ? (
        <Link to={`/produit/${productId}`} className="product-image-link">
          <div className="product-image">
            {!imageLoaded && <div className="product-image-skeleton shimmer"></div>}
            <img
              src={image}
              alt={name}
              loading="lazy"
              decoding="async"
              onLoad={() => setImageLoaded(true)}
              onError={() => setImageLoaded(true)}
              className={imageLoaded ? 'loaded' : 'loading'}
            />
          </div>
        </Link>
      ) : null}

      <div className="product-info">
        <div className="product-rating" aria-label={`${rating} etoiles sur 5`}>
          {[...Array(5)].map((_, index) => (
            <FaStar key={index} className={index < rating ? 'star filled' : 'star'} />
          ))}
        </div>

        <Link to={`/produit/${productId}`} className="product-title">
          {name}
        </Link>

        <p className="product-meta">{category}</p>
        <p className="product-description">{description}</p>

        <div className="product-footer">
          <div className="product-pricing">
            <span className="price">{priceFormatter.format(currentPrice)}</span>
            {(oldPrice && Number(oldPrice) > Number(currentPrice)) ? (
              <span className="old-price">{priceFormatter.format(oldPrice)}</span>
            ) : null}
          </div>

          <button type="button" className="product-cart-btn" onClick={handleAddToCart} disabled={addingToCart} aria-label="Ajouter au panier">
            <span>{addingToCart ? 'Ajout...' : 'Ajouter au panier'}</span>
            <FaShoppingCart />
          </button>
        </div>

        <Link to={`/produit/${productId}`} className="product-cta">
          Voir details
        </Link>
      </div>
    </article>
  )
}

export default memo(ProductCard)
