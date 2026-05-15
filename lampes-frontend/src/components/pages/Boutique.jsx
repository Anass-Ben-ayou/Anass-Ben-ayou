import React, { useDeferredValue, useEffect, useMemo, useRef, useState } from 'react'
import { FaHeadset, FaLock, FaShieldAlt, FaSpinner, FaThLarge, FaThList, FaTruck } from 'react-icons/fa'
import ProductCard from '../products/ProductCard'
import ProductFilters from '../products/ProductFilters'
import { productService } from '../../services/productService'
import './Boutique.css'

const PAGE_SIZE = 8

const initialFilters = {
  search: '',
  categorie_id: '',
  prix_min: '',
  prix_max: '',
  sort: 'latest'
}

const boutiqueBenefits = [
  [<FaLock />, 'Paiement securise', 'Commande protegee pour chaque achat'],
  [<FaTruck />, 'Livraison express', 'Preparation rapide partout au Maroc'],
  [<FaShieldAlt />, 'Garantie 2 ans', 'Concu pour un usage quotidien interieur et exterieur'],
  [<FaHeadset />, 'Service client', 'Accompagnement avant et apres votre commande']
]

// Renders the boutique catalog with server pagination and infinite scroll.
const Boutique = () => {
  const [filters, setFilters] = useState(initialFilters)
  const [categories, setCategories] = useState([])
  const [products, setProducts] = useState([])
  const [currentPage, setCurrentPage] = useState(1)
  const [hasMore, setHasMore] = useState(true)
  const [loading, setLoading] = useState(true)
  const [loadingMore, setLoadingMore] = useState(false)
  const [error, setError] = useState('')
  const loadMoreRef = useRef(null)
  const latestRequestRef = useRef(0)
  const deferredFilters = useDeferredValue(filters)

  useEffect(() => {
    let cancelled = false

    const loadCategories = async () => {
      try {
        const response = await productService.getCategories()

        if (!cancelled) {
          setCategories(Array.isArray(response) ? response : [])
        }
      } catch (fetchError) {
        if (!cancelled) {
          setCategories([])
        }
      }
    }

    loadCategories()

    return () => {
      cancelled = true
    }
  }, [])

  useEffect(() => {
    setProducts([])
    setCurrentPage(1)
    setHasMore(true)
  }, [deferredFilters])

  useEffect(() => {
    let cancelled = false
    const requestId = ++latestRequestRef.current
    const isFirstPage = currentPage === 1

    if (isFirstPage) {
      setLoading(true)
    } else {
      setLoadingMore(true)
    }

    setError('')

    const loadProducts = async () => {
      try {
        const catalog = await productService.getProducts({
          ...deferredFilters,
          page: currentPage,
          per_page: PAGE_SIZE
        })

        if (cancelled || requestId !== latestRequestRef.current) {
          return
        }

        const incomingProducts = Array.isArray(catalog?.data) ? catalog.data : []
        const nextPage = Number(catalog?.current_page || currentPage)
        const lastPage = Number(catalog?.last_page || nextPage)

        setProducts((currentProducts) => {
          if (isFirstPage) {
            return incomingProducts
          }

          const seenIds = new Set(currentProducts.map((product) => product.id_produit || product.id))
          const freshProducts = incomingProducts.filter((product) => !seenIds.has(product.id_produit || product.id))

          return [...currentProducts, ...freshProducts]
        })
        setHasMore(nextPage < lastPage)
      } catch (fetchError) {
        if (!cancelled && requestId === latestRequestRef.current) {
          setError('Impossible de charger les produits pour le moment.')
          setHasMore(false)
        }
      } finally {
        if (!cancelled && requestId === latestRequestRef.current) {
          setLoading(false)
          setLoadingMore(false)
        }
      }
    }

    loadProducts()

    return () => {
      cancelled = true
    }
  }, [currentPage, deferredFilters])

  useEffect(() => {
    const target = loadMoreRef.current

    if (!target || loading || loadingMore || !hasMore) {
      return undefined
    }

    const observer = new IntersectionObserver(
      (entries) => {
        const [entry] = entries

        if (entry?.isIntersecting) {
          setLoadingMore(true)
          setCurrentPage((page) => page + 1)
        }
      },
      {
        rootMargin: '240px 0px'
      }
    )

    observer.observe(target)

    return () => {
      observer.disconnect()
    }
  }, [hasMore, loading, loadingMore])

  const activeFilterCount = useMemo(() => {
    return Object.entries(filters).filter(([key, value]) => value && !(key === 'sort' && value === 'latest')).length
  }, [filters])

  const handleFilterChange = (patch) => {
    setFilters((currentFilters) => ({
      ...currentFilters,
      ...patch
    }))
  }

  const handleFilterReset = () => {
    setFilters(initialFilters)
  }

  return (
    <main className="products-page boutique-page">
      <section className="products-hero">
        <div className="container">
          <div className="products-hero-panel">
            <div>
              <div className="products-breadcrumb">
                <span>Accueil</span>
                <span>/</span>
                <span>Boutique</span>
              </div>
              <h1>Nos luminaires <span>solaires</span></h1>
              <p>
                Explorez la collection SolarLight avec un chargement fluide :
                <br />des pages legeres et rapides pendant votre navigation.
              </p>
            </div>
          </div>
        </div>
      </section>

      <section className="products-layout container">
        <aside className="products-sidebar">
          <ProductFilters
            filters={filters}
            categories={categories}
            onChange={handleFilterChange}
            onReset={handleFilterReset}
          />
        </aside>

        <div className="products-content">
          <div className="products-header">
            <div>
              <h2>Boutique</h2>
              <p className="products-count">
                {products.length} produit{products.length > 1 ? 's' : ''} charge{products.length > 1 ? 's' : ''}
                {activeFilterCount > 0 ? ` avec ${activeFilterCount} filtre${activeFilterCount > 1 ? 's' : ''}` : ''}
              </p>
            </div>
            <div className="boutique-view-actions" aria-hidden="true">
              <button type="button"><FaThLarge /></button>
              <button type="button"><FaThList /></button>
            </div>
          </div>

          {error ? (
            <div className="no-results glass-card">
              <strong>Chargement indisponible</strong>
              <p>{error}</p>
            </div>
          ) : null}

              {loading ? (
            <div className="catalog-grid">
                {Array.from({ length: PAGE_SIZE }).map((_, index) => (
                <div key={index} className="catalog-skeleton-card glass-card">
                  <div className="catalog-skeleton-media shimmer"></div>
                  <div className="catalog-skeleton-line shimmer"></div>
                  <div className="catalog-skeleton-line short shimmer"></div>
                  <div className="catalog-skeleton-line shimmer"></div>
                </div>
              ))}
            </div>
          ) : products.length > 0 ? (
            <>
              <div className="catalog-grid">
                {products.map((product) => (
                  <ProductCard key={product.id || product.id_produit} product={product} />
                ))}
              </div>

              <div ref={loadMoreRef} className="catalog-load-more" aria-hidden="true">
                {loadingMore ? <><span>Chargement des produits supplementaires...</span><FaSpinner /></> : null}
                {!hasMore ? <span>Vous avez atteint la fin du catalogue.</span> : null}
              </div>
            </>
          ) : (
            <div className="no-results glass-card">
              <strong>Aucun produit ne correspond a votre recherche</strong>
              <p>Essayez un autre mot-cle ou reinitialisez les filtres.</p>
            </div>
          )}
        </div>
      </section>

      <section className="boutique-benefits">
        <div className="container">
          <div className="boutique-benefits-grid">
            {boutiqueBenefits.map(([icon, title, text]) => (
              <article key={title}>
                <span>{icon}</span>
                <div>
                  <strong>{title}</strong>
                  <p>{text}</p>
                </div>
              </article>
            ))}
          </div>
        </div>
      </section>
    </main>
  )
}

export default Boutique
