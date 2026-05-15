import React from 'react'
import { FaStar } from 'react-icons/fa'
import './Newsletter.css'

const Newsletter = () => {
  return (
    <section className="spotlight-section" id="about">
      <div className="container">
        <div className="spotlight-panel glass-card">
          <div className="spotlight-copy">
            <span className="chip">A propos de Solarlight</span>
            <h2 className="section-title">Une boutique plus lumineuse, inspiree de votre reference et reliee a de vraies fonctions ecommerce.</h2>
            <p className="section-subtitle">
              Toute la boutique a ete orientee vers des blancs lumineux, des accents dores,
              une mise en valeur plus nette des produits, un panier fonctionnel et une identite premium plus soignee.
            </p>
          </div>

          <div className="spotlight-review">
            <FaStar />
            <p>
              "Les luminaires solaires et les lampes modernes doivent sembler haut de gamme, pas generiques. Cette refonte apporte cette sensation au parcours d achat."
            </p>
            <span>note editoriale Solarlight</span>
          </div>
        </div>
      </div>
    </section>
  )
}

export default Newsletter
