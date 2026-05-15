import React, { useEffect, useMemo, useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { FaBars, FaSearch, FaShoppingCart, FaTimes, FaUserCircle } from 'react-icons/fa'
import { useAuth } from '../contexts/AuthContext'
import { api } from '../../services/api'
import { CART_UPDATED_EVENT } from '../../services/cartEvents'
import './Header.css'

const navigationItems = [
  { label: 'Accueil', to: '/', isActive: (location) => location.pathname === '/' },
  {
    label: 'Boutique',
    to: '/boutique',
    isActive: (location) => location.pathname === '/boutique' || location.pathname === '/products'
  },
  {
    label: 'Collections',
    to: '/collections',
    isActive: (location) => location.pathname.startsWith('/collections')
  },
  { label: 'A propos', to: '/a-propos', isActive: (location) => location.pathname === '/a-propos' },
  { label: 'Contact', to: '/contact', isActive: (location) => location.pathname === '/contact' }
]

const Header = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false)
  const [cartCount, setCartCount] = useState(0)
  const { user, logout } = useAuth()
  const location = useLocation()
  const isAdmin = user?.role === 'admin' || user?.email === 'admin@lampes.ma'
  const activeItems = useMemo(() => {
    const items = isAdmin
      ? [
          ...navigationItems,
          { label: 'Utilisateurs', to: '/admin/users', isActive: (current) => current.pathname === '/admin/users' },
          { label: 'Commandes', to: '/admin/orders', isActive: (current) => current.pathname === '/admin/orders' },
          { label: 'Produits', to: '/admin/products', isActive: (current) => current.pathname === '/admin/products' }
        ]
      : navigationItems

    return items.map((item) => ({ ...item, active: item.isActive(location) }))
  }, [isAdmin, location])

  const handleMenuToggle = () => {
    setIsMenuOpen((value) => !value)
  }

  const handleLinkClick = () => {
    setIsMenuOpen(false)
  }

  useEffect(() => {
    const fetchCount = async () => {
      if (!user) {
        setCartCount(0)
        return
      }

      try {
        const response = await api.get('/cart/count', { skipAuthRedirect: true })
        setCartCount(response.data?.data?.count || 0)
      } catch (error) {
        setCartCount(0)
      }
    }

    fetchCount()
    window.addEventListener(CART_UPDATED_EVENT, fetchCount)

    return () => {
      window.removeEventListener(CART_UPDATED_EVENT, fetchCount)
    }
  }, [user, location.pathname])

  return (
    <header className="header-shell">
      <div className="container">
        <div className="header">
          <Link to="/" className="logo" onClick={handleLinkClick}>
            <span>SOLAR</span><strong>LIGHT</strong>
          </Link>

          <nav className={`nav-menu ${isMenuOpen ? 'active' : ''}`}>
            {activeItems.map((item) => (
              <Link
                key={item.label}
                to={item.to}
                className={`nav-link ${item.active ? 'active' : ''}`}
                onClick={handleLinkClick}
              >
                {item.label}
              </Link>
            ))}
          </nav>

          <div className="header-tools">
            <Link to="/boutique" className="header-search" aria-label="Rechercher des produits">
              <FaSearch />
            </Link>

            <Link to="/cart" className="icon-btn" aria-label="Ouvrir le panier">
              <FaShoppingCart />
              <span className="cart-pill">{cartCount}</span>
            </Link>

            {user ? (
              <div className="user-menu">
                <button className="icon-btn user-btn" type="button" aria-label="Ouvrir le menu du compte">
                  <FaUserCircle />
                </button>
                <div className="user-dropdown glass-card">
                  <div className="user-dropdown-profile">
                    <strong>{`${user.prenom || ''} ${user.nom || ''}`.trim() || 'Client Solarlight'}</strong>
                    <small>{user.email || 'Compte connecte'}</small>
                  </div>
                  <Link to="/dashboard" onClick={handleLinkClick}>Mon espace</Link>
                  <Link to="/orders" onClick={handleLinkClick}>Mes commandes</Link>
                  <Link to="/cart" onClick={handleLinkClick}>Panier</Link>
                  <Link to={isAdmin ? '/dashboard?section=messages' : '/dashboard'} onClick={handleLinkClick}>Messages</Link>
                  <button type="button" onClick={logout}>Se deconnecter</button>
                </div>
              </div>
            ) : (
              <Link to="/login" className="icon-btn login-btn" aria-label="Se connecter">
                <FaUserCircle />
              </Link>
            )}

            <button
              type="button"
              className="mobile-menu-btn"
              onClick={handleMenuToggle}
              aria-label="Afficher le menu"
            >
              {isMenuOpen ? <FaTimes /> : <FaBars />}
            </button>
          </div>
        </div>
      </div>
    </header>
  )
}

export default Header
