import React from 'react'
import { Link } from 'react-router-dom'
import { FaArrowRight } from 'react-icons/fa'
import './CategoriesPopulaires.css'

const CategoriesPopulaires = ({ categories }) => {
  return (
    <section className="categories-section">
      <div className="container">
        <div className="section-head">
          <div>
            <span className="chip">Collections</span>
            <h2 className="section-title">Choisir par ambiance</h2>
            <p className="section-subtitle">
              Parcourez les familles de produits les plus consultees par les clients
              qui recherchent un interieur plus doux et mieux compose.
            </p>
          </div>
          <Link to="/products" className="btn-outline">Voir tous les produits</Link>
        </div>

        <div className="categories-grid">
          {categories.map((category, index) => (
            <Link
              key={category.id_categorie}
              to={`/products?categorie_id=${category.id_categorie}`}
              className={`category-card category-tone-${(index % 3) + 1}`}
            >
              <div>
                <p className="category-label">Categorie</p>
                <h3>{category.nom}</h3>
                <span>{category.produits_count || 0} produits</span>
              </div>
              <FaArrowRight />
            </Link>
          ))}
        </div>
      </div>
    </section>
  )
}

export default CategoriesPopulaires
