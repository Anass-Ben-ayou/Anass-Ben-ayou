import React, { useEffect, useState } from 'react'
import { FaBolt, FaHeadset, FaLeaf, FaLock, FaShieldAlt, FaSun, FaTools, FaTruck } from 'react-icons/fa'
import Hero from '../home/Hero'
import FeaturedProducts from '../home/FeaturedProducts'
import ScrollReveal from '../common/ScrollReveal'
import { productService } from '../../services/productService'
import './Home.css'

const benefits = [
  {
    icon: <FaLeaf />,
    title: 'Eco-responsable',
    text: 'Des solutions lumineuses durables qui reduisent votre empreinte et vos couts.'
  },
  {
    icon: <FaBolt />,
    title: 'Economie d energie',
    text: 'Une consommation reduite grace a des panneaux solaires haute performance.'
  },
  {
    icon: <FaTools />,
    title: 'Installation facile',
    text: 'Des luminaires faciles a installer, sans cablage, pour une utilisation immediate.'
  },
  {
    icon: <FaShieldAlt />,
    title: 'Eclairage exterieur',
    text: 'Des lampes concues pour resister aux intemperies et garantir la securite.'
  }
]

const serviceCards = [
  [<FaLock />, 'Paiement securise', 'Commandez en toute securite sur notre site.'],
  [<FaTruck />, 'Livraison express', 'Livraison rapide partout au Maroc.'],
  [<FaShieldAlt />, 'Garantie 2 ans', 'Sur tous nos luminaires solaires.'],
  [<FaHeadset />, 'Service client', 'Nous sommes disponibles pour vous accompagner.']
]

const Home = () => {
  const [featuredProducts, setFeaturedProducts] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    const fetchFeaturedProducts = async () => {
      try {
        const products = await productService.getFeaturedProducts()
        setFeaturedProducts(products)
      } catch (loadError) {
        setError('Impossible de charger les produits mis en avant pour le moment.')
      } finally {
        setLoading(false)
      }
    }

    fetchFeaturedProducts()
  }, [])

  return (
    <div className="home-page">
      <Hero />

      <ScrollReveal as="section" className="solar-about-section">
        <div className="container">
          <div className="solar-about-card">
            <div>
              <span className="chip">A propos de nos lampes solaires</span>
              <h2>Une boutique claire, moderne et specialisee dans l eclairage solaire.</h2>
              <p>
                Chaque produit est concu pour offrir la meilleure qualite, une longue duree de vie et un eclairage efficace.
                Nous selectionnons avec soin des luminaires solaires performants pour garder une empreinte lumineuse positive,
                a la fois esthetique et durable.
              </p>
            </div>
            <FaSun />
          </div>
        </div>
      </ScrollReveal>

      <ScrollReveal as="section" className="benefits-section">
        <div className="container">
          <span className="chip">Benefices</span>
          <h2>Pourquoi choisir Solarlight</h2>
          <div className="benefits-grid">
            {benefits.map((benefit, index) => (
              <ScrollReveal
                as="article"
                key={benefit.title}
                className="benefit-card"
                delay={index * 90}
              >
                <span>{benefit.icon}</span>
                <div>
                  <h3>{benefit.title}</h3>
                  <p>{benefit.text}</p>
                </div>
              </ScrollReveal>
            ))}
          </div>
        </div>
      </ScrollReveal>

      <FeaturedProducts products={featuredProducts} loading={loading} error={error} />

      <ScrollReveal as="section" className="service-strip-section">
        <div className="container">
          <div className="service-strip-grid">
            {serviceCards.map(([icon, title, text], index) => (
              <ScrollReveal
                as="article"
                key={title}
                className="service-card"
                delay={index * 80}
              >
                <span>{icon}</span>
                <div>
                  <h3>{title}</h3>
                  <p>{text}</p>
                </div>
              </ScrollReveal>
            ))}
          </div>
        </div>
      </ScrollReveal>
    </div>
  )
}

export default Home
