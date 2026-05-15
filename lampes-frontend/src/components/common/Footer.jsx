import React from 'react'
import { FaEnvelope, FaFacebookF, FaInstagram, FaLinkedinIn, FaMapMarkerAlt, FaPhone, FaPinterestP, FaSearch } from 'react-icons/fa'
import './Footer.css'

const Footer = () => {
  return (
    <div className="footer-wrap">
      <div className="container">
        <footer className="footer glass-card">
          <div className="footer-benefits">
            <div><strong>Paiement securise</strong><span>Commande protegee pour chaque achat</span></div>
            <div><strong>Livraison express</strong><span>Preparation rapide partout au Maroc</span></div>
            <div><strong>Garantie 2 ans</strong><span>Concu pour un usage quotidien interieur et exterieur</span></div>
            <div><strong>Service client</strong><span>Accompagnement avant et apres votre commande</span></div>
          </div>

          <div className="footer-grid">
            <div className="footer-brand">
              <div className="footer-logo">
                <span>SOLAR</span><strong>LIGHT</strong>
              </div>
              <p>
                Specialiste de l eclairage professionnel, nous proposons des lampes alliant design,
                performance et durabilite.
              </p>

              <div className="footer-socials" aria-label="Reseaux sociaux">
                <a href="/contact" aria-label="Facebook"><FaFacebookF /></a>
                <a href="/contact" aria-label="Instagram"><FaInstagram /></a>
                <a href="/contact" aria-label="LinkedIn"><FaLinkedinIn /></a>
                <a href="/contact" aria-label="Pinterest"><FaPinterestP /></a>
              </div>

              <form className="footer-newsletter">
                <input type="email" placeholder="Abonnez-vous a notre newsletter" />
                <button type="button" aria-label="S abonner">
                  <FaSearch />
                </button>
              </form>
            </div>

            <div className="footer-section" id="about">
              <h4>Navigation</h4>
              <a href="/">Accueil</a>
              <a href="/boutique">Produits</a>
              <a href="/collections">Collections</a>
              <a href="/a-propos">A propos</a>
              <a href="/contact">Contact</a>
            </div>

            <div className="footer-section" id="contact">
              <h4>Contact</h4>
              <p><FaEnvelope /> contact@solarlight.ma</p>
              <p><FaPhone /> +212 6 82 34 56 78</p>
              <p><FaMapMarkerAlt /> Taza, Morocco</p>
            </div>

            <div className="footer-section">
              <h4>Newsletter</h4>
              <p>Inscrivez-vous a notre newsletter pour recevoir nos offres et nouveautes.</p>
              <a href="/boutique">Voir toute la boutique</a>
              <p><FaInstagram /> @solarlight</p>
            </div>
          </div>

          <div className="footer-bottom">
            <p>(c) 2026 Solarlight. Tous droits reserves.</p>
            <p>Visa, Mastercard, paiement securise et accompagnement premium.</p>
          </div>
        </footer>
      </div>
    </div>
  )
}

export default Footer
