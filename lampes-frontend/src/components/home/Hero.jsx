import React from 'react'
import { Link } from 'react-router-dom'
import { FaArrowRight } from 'react-icons/fa'
import './Hero.css'

// Renders the homepage hero with the main brand message.
const Hero = () => {
  return (
    <section className="hero-home">
      <div className="container">
        <div className="hero-home-panel glass-card">
          <div className="hero-home-copy">
            <span className="chip">Solarlight</span>
            <h1 className="section-title">L eclairage solaire qui <span>sublime</span> vos espaces.</h1>
            <p className="section-subtitle">
              Solarlight propose une boutique moderne dediee aux luminaires solaires, alliant design elegant,
              durabilite et performance pour illuminer chaque instant, tout en respectant la nature.
            </p>
            <div className="hero-home-actions">
              <Link to="/boutique" className="btn-primary">Voir la boutique <FaArrowRight /></Link>
              <Link to="/collections" className="btn-outline">Explorer les collections</Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}

export default Hero
