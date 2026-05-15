import React from 'react'
import { FaHeadset, FaLock, FaShieldAlt, FaTruck } from 'react-icons/fa'
import './AuthServiceBar.css'

const serviceCards = [
  [<FaLock />, 'Paiement securise', 'Commande protegee pour chaque achat'],
  [<FaTruck />, 'Livraison express', 'Preparation rapide partout au Maroc'],
  [<FaShieldAlt />, 'Garantie 2 ans', 'Concu pour un usage quotidien interieur et exterieur'],
  [<FaHeadset />, 'Service client', 'Accompagnement avant et apres votre commande']
]

const AuthServiceBar = () => (
  <section className="auth-service-bar" aria-label="Services Solarlight">
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
)

export default AuthServiceBar
