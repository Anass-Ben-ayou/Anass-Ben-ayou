import React, { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import ProductCard from '../products/ProductCard'
import { productService } from '../../services/productService'
import './CollectionDetails.css'

// Shows the products that belong to a single storefront collection.
const CollectionDetails = () => {
  const { id } = useParams()
  const [collection, setCollection] = useState(null)
  const [products, setProducts] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    // Loads one collection and its related products from the API.
    const fetchCollection = async () => {
      setLoading(true)
      setError('')

      try {
        const payload = await productService.getCollectionProducts(id)
        setCollection(payload?.collection || null)
        setProducts(Array.isArray(payload?.products) ? payload.products : [])
      } catch (loadError) {
        setCollection(null)
        setProducts([])
        setError('Impossible de charger cette collection pour le moment.')
      } finally {
        setLoading(false)
      }
    }

    fetchCollection()
  }, [id])

  return (
    <div className="collection-details-page">
      <div className="container">
        <Link to="/collections" className="back-link">Retour aux collections</Link>

        {error ? (
          <div className="collection-details-state glass-card">{error}</div>
        ) : loading ? (
          <div className="collection-details-state glass-card">Chargement de la collection...</div>
        ) : (
          <>
            <section className="collection-details-hero glass-card">
              <div className="collection-details-copy">
                <span className="chip">Collection</span>
                <h1>{collection?.title || 'Collection Solarlight'}</h1>
                <p>{collection?.description}</p>
              </div>
              {collection?.image ? (
                <div className="collection-details-visual">
                  <img src={collection.image} alt={collection.title} />
                </div>
              ) : null}
            </section>

            <section>
              <div className="section-head">
                <div>
                  <span className="chip">Produits lies</span>
                  <h2 className="section-title">Selection de la collection</h2>
                </div>
              </div>

              {products.length > 0 ? (
                <div className="catalog-grid">
                  {products.map((product) => (
                    <ProductCard key={product.id_produit || product.id} product={product} />
                  ))}
                </div>
              ) : (
                <div className="collection-details-state glass-card">
                  Aucun produit n est encore disponible dans cette collection.
                </div>
              )}
            </section>
          </>
        )}
      </div>
    </div>
  )
}

export default CollectionDetails
