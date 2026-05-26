import React, { useDeferredValue, useEffect, useMemo, useRef, useState } from 'react'
import { FaHeadset, FaLock, FaShieldAlt, FaSpinner, FaThLarge, FaThList, FaTruck } from 'react-icons/fa'
import { useSearchParams } from 'react-router-dom'
import ProductCard from '../products/ProductCard'
import ProductFilters from '../products/ProductFilters'
import ScrollReveal from '../common/ScrollReveal'
import { productService } from '../../services/productService'
import './Boutique.css'

const PAGE_SIZE = 2
const LOAD_MORE_ROOT_MARGIN = '720px 0px'

const initialFilters = {
  search: '',
  categorie_id: '',
  collection: '',
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
  const [searchParams, setSearchParams] = useSearchParams()
  const [filters, setFilters] = useState(() => ({
    ...initialFilters,
    collection: searchParams.get('collection') || '',
    categorie_id: searchParams.get('categorie_id') || '',
    search: searchParams.get('search') || ''
  }))
  const [categories, setCategories] = useState([])
  const [collections, setCollections] = useState([])
  const [products, setProducts] = useState([])
  const [currentPage, setCurrentPage] = useState(1)
  const [hasMore, setHasMore] = useState(true)
  const [loading, setLoading] = useState(true)
  const [loadingMore, setLoadingMore] = useState(false)
  const [error, setError] = useState('')
  const [viewMode, setViewMode] = useState('grid')
  const loadMoreRef = useRef(null)
  const latestRequestRef = useRef(0)
  const deferredFilters = useDeferredValue(filters)

  useEffect(() => {
    let cancelled = false

    const loadFilters = async () => {
      try {
        const [categoryResponse, collectionResponse] = await Promise.all([
          productService.getCategories(),
          productService.getCollections()
        ])

        if (!cancelled) {
          setCategories(Array.isArray(categoryResponse) ? categoryResponse : [])
          setCollections(Array.isArray(collectionResponse) ? collectionResponse : [])
        }
      } catch (fetchError) {
        if (!cancelled) {
          setCategories([])
          setCollections([])
        }
      }
    }

    loadFilters()

    return () => {
      cancelled = true
    }
  }, [])

  useEffect(() => {
    const nextCollection = searchParams.get('collection') || ''
    const nextCategory = searchParams.get('categorie_id') || ''
    const nextSearch = searchParams.get('search') || ''

    setFilters((currentFilters) => {
      if (
        currentFilters.collection === nextCollection &&
        currentFilters.categorie_id === nextCategory &&
        currentFilters.search === nextSearch
      ) {
        return currentFilters
      }

      return {
        ...currentFilters,
        collection: nextCollection,
        categorie_id: nextCategory,
        search: nextSearch
      }
    })
  }, [searchParams])

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
        rootMargin: LOAD_MORE_ROOT_MARGIN
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
    setFilters((currentFilters) => {
      const nextFilters = {
        ...currentFilters,
        ...patch
      }

      if (Object.prototype.hasOwnProperty.call(patch, 'collection') && patch.collection) {
        nextFilters.categorie_id = ''
      }

      if (Object.prototype.hasOwnProperty.call(patch, 'categorie_id') && patch.categorie_id) {
        nextFilters.collection = ''
      }

      const nextParams = {}
      ;['collection', 'categorie_id', 'search'].forEach((key) => {
        if (nextFilters[key]) {
          nextParams[key] = nextFilters[key]
        }
      })
      setSearchParams(nextParams, { replace: true })

      return nextFilters
    })
  }

  const handleFilterReset = () => {
    setFilters(initialFilters)
    setSearchParams({}, { replace: true })
  }

  const activeCollection = useMemo(() => (
    collections.find((collection) => (collection.slug || collection.id) === filters.collection)
  ), [collections, filters.collection])

  return (
    <main className="products-page boutique-page">
      <ScrollReveal as="section" className="products-hero">
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
                {activeCollection
                  ? `Collection ${activeCollection.title} : tous les produits correspondants sont filtres automatiquement.`
                  : 'Explorez la collection SolarLight avec un chargement fluide :'}
                {!activeCollection ? <><br />des pages legeres et rapides pendant votre navigation.</> : null}
              </p>
            </div>
          </div>
        </div>
      </ScrollReveal>

      <section className="products-layout container">
        <ScrollReveal as="aside" className="products-sidebar" direction="left">
          <ProductFilters
            filters={filters}
            categories={categories}
            collections={collections}
            onChange={handleFilterChange}
            onReset={handleFilterReset}
          />
        </ScrollReveal>

        <ScrollReveal className="products-content" direction="right">
          <div className="products-header">
            <div>
              <h2>{activeCollection?.title || 'Boutique'}</h2>
              <p className="products-count">
                {products.length} produit{products.length > 1 ? 's' : ''} charge{products.length > 1 ? 's' : ''}
                {activeFilterCount > 0 ? ` avec ${activeFilterCount} filtre${activeFilterCount > 1 ? 's' : ''}` : ''}
              </p>
            </div>
            <div className="boutique-view-actions" aria-label="Changer l affichage des produits">
              <button
                type="button"
                className={viewMode === 'grid' ? 'active' : ''}
                onClick={() => setViewMode('grid')}
                aria-label="Afficher en grille"
                aria-pressed={viewMode === 'grid'}
              >
                <FaThLarge />
              </button>
              <button
                type="button"
                className={viewMode === 'list' ? 'active' : ''}
                onClick={() => setViewMode('list')}
                aria-label="Afficher en liste"
                aria-pressed={viewMode === 'list'}
              >
                <FaThList />
              </button>
            </div>
          </div>

          {error ? (
            <ScrollReveal className="no-results glass-card">
              <strong>Chargement indisponible</strong>
              <p>{error}</p>
            </ScrollReveal>
          ) : null}

          {loading ? (
            <div className="catalog-grid">
              {Array.from({ length: PAGE_SIZE }).map((_, index) => (
                <ScrollReveal key={index} className="catalog-skeleton-card glass-card" delay={index * 60}>
                  <div className="catalog-skeleton-media shimmer"></div>
                  <div className="catalog-skeleton-line shimmer"></div>
                  <div className="catalog-skeleton-line short shimmer"></div>
                  <div className="catalog-skeleton-line shimmer"></div>
                </ScrollReveal>
              ))}
            </div>
          ) : products.length > 0 ? (
            <>
              <div className={`catalog-grid ${viewMode === 'list' ? 'catalog-list' : ''}`}>
                {products.map((product, index) => (
                  <ScrollReveal key={product.id || product.id_produit} delay={(index % PAGE_SIZE) * 60}>
                    <ProductCard product={product} />
                  </ScrollReveal>
                ))}
              </div>

              <div ref={loadMoreRef} className="catalog-load-more" aria-hidden="true">
                {loadingMore ? <><span>Chargement des produits supplementaires...</span><FaSpinner /></> : null}
                {!hasMore ? <span>Vous avez atteint la fin du catalogue.</span> : null}
              </div>
            </>
          ) : (
            <ScrollReveal className="no-results glass-card">
              <strong>Aucun produit ne correspond a votre recherche</strong>
              <p>Essayez un autre mot-cle ou reinitialisez les filtres.</p>
            </ScrollReveal>
          )}
        </ScrollReveal>
      </section>

      <ScrollReveal as="section" className="boutique-benefits">
        <div className="container">
          <div className="boutique-benefits-grid">
            {boutiqueBenefits.map(([icon, title, text], index) => (
              <ScrollReveal as="article" key={title} delay={index * 80}>
                <span>{icon}</span>
                <div>
                  <strong>{title}</strong>
                  <p>{text}</p>
                </div>
              </ScrollReveal>
            ))}
          </div>
        </div>
      </ScrollReveal>
    </main>
  )
}

export default Boutique
