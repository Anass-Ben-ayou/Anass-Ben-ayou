import React from 'react'
import { FaBoxOpen, FaCreditCard, FaHeadset, FaShieldAlt } from 'react-icons/fa'
import './MeilleuresVentes.css'

const MeilleuresVentes = () => {
  return (
    <section className="story-section">
      <div className="container">
        <div className="story-benefits glass-card">
          <article className="story-stat">
            <FaCreditCard />
            <strong>Paiement securise</strong>
            <span>Paiement carte avec prise en charge Visa et Mastercard.</span>
          </article>
          <article className="story-stat">
            <FaBoxOpen />
            <strong>Livraison express</strong>
            <span>Expedition rapide depuis notre selection de luminaires.</span>
          </article>
          <article className="story-stat">
            <FaShieldAlt />
            <strong>Garantie 2 ans</strong>
            <span>Des produits fiables concus pour durer au quotidien.</span>
          </article>
          <article className="story-stat">
            <FaHeadset />
            <strong>Service client</strong>
            <span>Une vraie assistance avant l achat et apres la livraison.</span>
          </article>
        </div>
      </div>
    </section>
  )
}

export default MeilleuresVentes
