import React from 'react'
import './ProductFilters.css'

// Renders the boutique filters and keeps the inputs grouped in one place.
const ProductFilters = ({ filters, categories, onChange, onReset }) => {
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
          value={filters.categorie_id}
          onChange={(event) => onChange({ categorie_id: event.target.value })}
        >
          <option value="">Toutes les categories</option>
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
