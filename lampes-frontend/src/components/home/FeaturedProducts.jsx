import React from 'react'
import { Link } from 'react-router-dom'
import ProductCard from '../products/ProductCard'
import ScrollReveal from '../common/ScrollReveal'
import './FeaturedProducts.css'

// Shows the five homepage spotlight products.
const FeaturedProducts = ({ products, loading, error }) => {
  return (
    <ScrollReveal as="section" className="featured-products-section">
      <div className="container">
        <ScrollReveal className="section-head" direction="left">
          <div>
            <span className="chip">Produits vedettes</span>
            <h2 className="section-title">Nos 5 selections du moment</h2>
            <p className="section-subtitle">Une selection pensee avec soin pour vous offrir les meilleurs luminaires solaires.</p>
          </div>
          <Link to="/boutique" className="btn-outline">Voir la boutique</Link>
        </ScrollReveal>

        {error ? (
          <ScrollReveal className="featured-products-state glass-card">
            <p>{error}</p>
          </ScrollReveal>
        ) : loading ? (
          <div className="catalog-grid">
            {Array.from({ length: 5 }, (_, index) => (
              <ScrollReveal key={`featured-${index}`} className="product-card product-card-skeleton" delay={index * 80}>
                <div className="product-image shimmer"></div>
                <div className="product-info">
                  <div className="line-skeleton line-small shimmer"></div>
                  <div className="line-skeleton shimmer"></div>
                  <div className="line-skeleton line-medium shimmer"></div>
                </div>
              </ScrollReveal>
            ))}
          </div>
        ) : (
          <div className="catalog-grid">
            {products.map((product, index) => (
              <ScrollReveal key={product.id_produit || product.id} delay={index * 80}>
                <ProductCard product={product} />
              </ScrollReveal>
            ))}
          </div>
        )}
      </div>
    </ScrollReveal>
  )
}

export default FeaturedProducts
