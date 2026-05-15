import React, { useEffect, useState } from 'react'
import { FaHeadset, FaLock, FaShieldAlt, FaTruck } from 'react-icons/fa'
import CollectionCard from '../collections/CollectionCard'
import { productService } from '../../services/productService'
import './Collections.css'

const fallbackCollections = [
  {
    id: 'jardin',
    slug: 'jardin',
    title: 'Lampes solaires jardin',
    description: 'Des lampes exterieures pensees pour les allees, terrasses et coins detente.',
    image: 'https://images.unsplash.com/photo-1567459169668-95d355371bda?auto=format&fit=crop&w=1100&q=88'
  },
  {
    id: 'projecteurs',
    slug: 'projecteurs',
    title: 'Projecteurs solaires',
    description: 'Des solutions lumineuses plus directes pour facades, acces et zones exterieures.',
    image: 'https://images.unsplash.com/photo-1616423841125-830766df3cd4?auto=format&fit=crop&w=1100&q=88'
  },
  {
    id: 'appliques',
    slug: 'appliques',
    title: 'Appliques murales solaires',
    description: 'Un eclairage mural simple a poser pour l entree, les couloirs et les murs exterieurs.',
    image: 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1100&q=88'
  },
  {
    id: 'guirlandes',
    slug: 'guirlandes',
    title: 'Guirlandes solaires',
    description: 'Des ambiances plus douces pour jardins, pergolas et repas en plein air.',
    image: 'https://images.unsplash.com/photo-1567459169668-95d355371bda?auto=format&fit=crop&w=1100&q=88'
  },
  {
    id: 'kits',
    slug: 'kits',
    title: 'Kits solaires',
    description: 'Des ensembles complets pour equiper vos espaces avec une signature lumineuse coherente.',
    image: 'https://images.unsplash.com/photo-1600607688969-a5bfcd646154?auto=format&fit=crop&w=1300&q=88'
  }
]

const collectionBenefits = [
  [<FaLock />, 'Paiement securise', 'Commande protegee pour chaque achat'],
  [<FaTruck />, 'Livraison express', 'Preparation rapide partout au Maroc'],
  [<FaShieldAlt />, 'Garantie 2 ans', 'Concu pour un usage quotidien interieur et exterieur'],
  [<FaHeadset />, 'Service client', 'Accompagnement avant et apres votre commande']
]

// Displays the storefront collection overview cards.
const Collections = () => {
  const [collections, setCollections] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    // Loads all public product collections for the collections page.
    const fetchCollections = async () => {
      try {
        const data = await productService.getCollections()
        setCollections(Array.isArray(data) ? data : [])
      } catch (loadError) {
        setError('Impossible de charger les collections pour le moment.')
      } finally {
        setLoading(false)
      }
    }

    fetchCollections()
  }, [])

  const visibleCollections = collections.length > 0 ? collections : fallbackCollections

  return (
    <div className="collections-page">
      <div className="container">
        <section className="collections-hero">
          <div>
            <div className="collections-breadcrumb">
              <span>Accueil</span>
              <span>/</span>
              <span>Collections</span>
            </div>
            <h1>Des univers <span>solaires</span><br />pour chaque usage</h1>
            <p>
              Explorez des selections plus claires par type d eclairage pour le jardin, la facade,
              les allees, les terrasses et les mises en ambiance.
            </p>
          </div>
        </section>

        {error ? (
          <div className="collections-state glass-card">{error}</div>
        ) : loading ? (
          <div className="collections-grid">
            {Array.from({ length: 5 }, (_, index) => (
              <div key={`collection-skeleton-${index}`} className="collection-card glass-card collection-skeleton"></div>
            ))}
          </div>
        ) : (
          <div className="collections-grid">
            {visibleCollections.map((collection, index) => (
              <CollectionCard key={collection.id || collection.slug || collection.title} collection={collection} featured={index === 4} />
            ))}
          </div>
        )}

        <section className="collections-benefits">
          {collectionBenefits.map(([icon, title, text]) => (
            <article key={title}>
              <span>{icon}</span>
              <div>
                <strong>{title}</strong>
                <p>{text}</p>
              </div>
            </article>
          ))}
        </section>
      </div>
    </div>
  )
}

export default Collections
