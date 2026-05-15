import React from 'react'
import { Link } from 'react-router-dom'
import ProductCard from '../products/ProductCard'
import './FeaturedProducts.css'

// Shows the five homepage spotlight products.
const FeaturedProducts = ({ products, loading, error }) => {
  return (
    <section className="featured-products-section">
      <div className="container">
        <div className="section-head">
          <div>
            <span className="chip">Produits vedettes</span>
            <h2 className="section-title">Nos 5 selections du moment</h2>
            <p className="section-subtitle">Une selection pensee avec soin pour vous offrir les meilleurs luminaires solaires.</p>
          </div>
          <Link to="/boutique" className="btn-outline">Voir la boutique</Link>
        </div>

        {error ? (
          <div className="featured-products-state glass-card">
            <p>{error}</p>
          </div>
        ) : loading ? (
          <div className="catalog-grid">
            {Array.from({ length: 5 }, (_, index) => (
              <div key={`featured-${index}`} className="product-card product-card-skeleton">
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
          <div className="catalog-grid">
            {products.map((product) => (
              <ProductCard key={product.id_produit || product.id} product={product} />
            ))}
          </div>
        )}
      </div>
    </section>
  )
}

export default FeaturedProducts
