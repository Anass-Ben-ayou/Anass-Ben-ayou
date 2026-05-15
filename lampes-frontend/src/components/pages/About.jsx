import React from 'react'
import { FaBullseye, FaHeadset, FaHeart, FaLock, FaShieldAlt, FaShoppingBag, FaSun, FaTruck } from 'react-icons/fa'
import './About.css'

const aboutCards = [
  [
    <FaBullseye />,
    'Notre objectif',
    'Proposer une boutique specialisee dans les lampes solaires avec une navigation simple, un choix clair et une presentation soignee.'
  ],
  [
    <FaSun />,
    'Les avantages du solaire',
    'Moins de consommation, plus d autonomie et un eclairage adapte aux exterieurs du quotidien comme aux petits projets d amenagement.'
  ],
  [
    <FaShoppingBag />,
    'Un e-commerce moderne',
    'Un catalogue plus fluide, des collections lisibles, des fiches detaillees et une experience plus propre du produit au panier.'
  ],
  [
    <FaHeart />,
    'Pourquoi nous choisir',
    'Pour la coherence du style, la selection des references, le confort d achat et un accompagnement pense pour inspirer confiance.'
  ]
]

const serviceCards = [
  [<FaLock />, 'Paiement securise', 'Commande protegee pour chaque achat'],
  [<FaTruck />, 'Livraison express', 'Preparation rapide partout au Maroc'],
  [<FaShieldAlt />, 'Garantie 2 ans', 'Concu pour un usage quotidien interieur et exterieur'],
  [<FaHeadset />, 'Service client', 'Accompagnement avant et apres votre commande']
]

const About = () => (
  <main className="about-page">
    <div className="container">
      <section className="about-hero">
        <div>
          <span className="chip">A propos</span>
          <h1>Solarlight, une vision plus responsable de <span>l eclairage.</span></h1>
          <p>
            Nous construisons une boutique e-commerce dediee aux lampes solaires pour offrir une experience
            plus moderne, plus pratique et plus inspiree autour de l eclairage.
          </p>
        </div>
      </section>

      <section className="about-card-grid">
        {aboutCards.map(([icon, title, text]) => (
          <article key={title} className="about-info-card">
            <span>{icon}</span>
            <div>
              <h2>{title}</h2>
              <p>{text}</p>
            </div>
          </article>
        ))}
      </section>

      <section className="about-service-strip">
        {serviceCards.map(([icon, title, text]) => (
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
  </main>
)

export default About
