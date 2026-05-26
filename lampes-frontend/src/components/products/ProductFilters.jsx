import React from 'react'
import './ProductFilters.css'

// Renders the boutique filters and keeps the inputs grouped in one place.
const ProductFilters = ({ filters, categories, collections = [], onChange, onReset }) => {
  const categorySelectValue = filters.collection ? `collection:${filters.collection}` : filters.categorie_id

  const handleCategoryChange = (event) => {
    const value = event.target.value

    if (!value) {
      onChange({ categorie_id: '', collection: '' })
      return
    }

    if (value.startsWith('collection:')) {
      onChange({ collection: value.replace('collection:', '') })
      return
    }

    onChange({ categorie_id: value })
  }

  return (
    <div className="filter-card">
      <h3 className="filter-title">Filtres boutique</h3>

      <div className="filter-group">
        <label className="filter-label">Recherche</label>
        <input
          type="search"
          className="filter-input"
          value={filters.search}
          onChange={(event) => onChange({ search: event.target.value })}
          placeholder="Nom du produit"
        />
      </div>

      <div className="filter-group">
        <label className="filter-label">Categorie</label>
        <select
          className="filter-select"
          value={categorySelectValue}
          onChange={handleCategoryChange}
        >
          <option value="">Toutes les categories</option>
          {collections.map((collection) => (
            <option key={`collection-${collection.slug || collection.id}`} value={`collection:${collection.slug || collection.id}`}>
              {collection.title}
            </option>
          ))}
          {categories.map((category) => (
            <option key={category.id_categorie || category.id} value={category.id_categorie || category.id}>
              {category.nom || category.name}
            </option>
          ))}
        </select>
      </div>

      <div className="filter-group">
        <label className="filter-label">Prix minimum</label>
        <input
          type="number"
          className="filter-input"
          value={filters.prix_min}
          onChange={(event) => onChange({ prix_min: event.target.value })}
          placeholder="0"
        />
      </div>

      <div className="filter-group">
        <label className="filter-label">Prix maximum</label>
        <input
          type="number"
          className="filter-input"
          value={filters.prix_max}
          onChange={(event) => onChange({ prix_max: event.target.value })}
          placeholder="3000"
        />
      </div>

      <div className="filter-group">
        <label className="filter-label">Trier</label>
        <select
          className="filter-select"
          value={filters.sort}
          onChange={(event) => onChange({ sort: event.target.value })}
        >
          <option value="latest">Plus recents</option>
          <option value="prix_asc">Prix croissant</option>
          <option value="prix_desc">Prix decroissant</option>
          <option value="nom_asc">Nom A a Z</option>
          <option value="nom_desc">Nom Z a A</option>
        </select>
      </div>

      <button type="button" className="btn-outline clear-filters" onClick={onReset}>
        Reinitialiser
      </button>
    </div>
  )
}

export default ProductFilters
