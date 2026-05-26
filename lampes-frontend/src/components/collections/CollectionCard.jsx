import React from 'react'
import { Link } from 'react-router-dom'
import { FaArrowRight } from 'react-icons/fa'
import './CollectionCard.css'

// Displays a single storefront collection card.
const CollectionCard = ({ collection, featured = false }) => {
  return (
    <article className={`collection-card glass-card ${featured ? 'collection-card-featured' : ''}`}>
      <div className="collection-card-image">
        <img src={collection.image} alt={collection.title} />
      </div>
      <div className="collection-card-content">
        <span className="chip">Collection</span>
        <h3>{collection.title}</h3>
        <p>{collection.description}</p>
        <Link to={`/boutique?collection=${encodeURIComponent(collection.slug || collection.id)}`} className="btn-outline">
          Voir collection <FaArrowRight />
        </Link>
      </div>
    </article>
  )
}

export default CollectionCard
