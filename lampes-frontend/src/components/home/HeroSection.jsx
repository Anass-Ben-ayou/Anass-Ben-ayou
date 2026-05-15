import React from 'react'
import { Link } from 'react-router-dom'
import ProductCard from '../products/ProductCard'
import { PRODUCT_IMAGE_FALLBACK, resolveProductImage } from '../../utils/productImages'
import './HeroSection.css'

const HeroSection = ({ heroProduct, featuredProducts, loading }) => {
  return (
    <section className="hero-section">
      <div className="container">
        <div className="hero-board glass-card">
          <div className="hero-stage-visual">
            <img
              src={resolveProductImage(heroProduct)}
              alt={heroProduct?.nom || 'lampe mise en avant Solarlight'}
              className="hero-banner-image"
              onError={(event) => {
                event.currentTarget.src = PRODUCT_IMAGE_FALLBACK
              }}
            />
            <div className="hero-copy">
              <span className="chip">Collection Solarlight</span>
              <h1 className="section-title">Un eclairage premium, lumineux et naturel a vivre.</h1>
              <p>
                Une boutique orientee solaire avec des silhouettes elegantes, des details dores
                et une presentation plus nette inspiree de votre reference.
              </p>

              <div className="hero-actions">
                <Link to="/products" className="btn-primary">Decouvrir les lampes solaires</Link>
                <Link to="/products?sort=popularite" className="btn-outline">Voir les collections</Link>
              </div>
            </div>
          </div>

          <div className="hero-products-panel product-ribbon">
            <div className="price-badge">
              <span>{new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(heroProduct?.prix || 649)}</span>
            </div>
            {loading ? (
              <div className="home-products-strip">
                {featuredProducts.map((key) => (
                  <div key={key} className="product-card product-card-skeleton">
                    <div className="product-image shimmer"></div>
                    <div className="product-info">
                      <div className="line-skeleton line-small shimmer"></div>
                      <div className="line-skeleton shimmer"></div>
                      <div className="line-skeleton line-medium shimmer"></div>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <div className="home-products-strip">
                {featuredProducts.map((product) => (
                  <ProductCard key={product.id_produit} product={product} />
                ))}
              </div>
            )}
          </div>
        </div>
      </div>
    </section>
  )
}

export default HeroSection
