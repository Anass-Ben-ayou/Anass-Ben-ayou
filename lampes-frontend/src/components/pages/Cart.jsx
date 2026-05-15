import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { FaCcMastercard, FaCcVisa, FaCreditCard, FaHeadset, FaLock, FaShieldAlt, FaTruck } from 'react-icons/fa'
import toast from 'react-hot-toast'
import { api } from '../../services/api'
import CartItem from '../cart/CartItem'
import './Cart.css'

const cartBenefits = [
  [<FaLock />, 'Paiement securise', 'Commande protegee pour chaque achat'],
  [<FaTruck />, 'Livraison express', 'Preparation rapide partout au Maroc'],
  [<FaShieldAlt />, 'Garantie 2 ans', 'Concu pour un usage quotidien interieur et exterieur'],
  [<FaHeadset />, 'Service client', 'Accompagnement avant et apres votre commande']
]

const FREE_DELIVERY_THRESHOLD = 500
const DELIVERY_FEE = 30

const Cart = () => {
  const [cart, setCart] = useState({ items: [], total: 0, total_items: 0 })
  const [loading, setLoading] = useState(true)
  const [submittingOrder, setSubmittingOrder] = useState(false)
  const [paymentConfig, setPaymentConfig] = useState({
    card_enabled: false,
    card_mode: 'disabled',
    card_label: 'Visa / Mastercard',
    card_message: 'Chargement des options de paiement...',
    stripe_publishable_key: ''
  })
  const [checkoutForm, setCheckoutForm] = useState({
    adresse: '',
    ville: '',
    code_postal: '',
    pays: 'Maroc',
    methode_paiement: 'livraison'
  })

  useEffect(() => {
    const fetchCartAndPaymentConfig = async () => {
      try {
        const [cartResponse, paymentResponse] = await Promise.all([
          api.get('/cart'),
          api.get('/orders/payment-config')
        ])

        const paymentData = paymentResponse.data?.data || {
          card_enabled: false,
          card_mode: 'disabled',
          card_label: 'Visa / Mastercard',
          card_message: 'Le paiement par carte est indisponible pour le moment.'
        }
        setCart(cartResponse.data.data)
        setPaymentConfig(paymentData)
        setCheckoutForm((current) => ({
          ...current,
          methode_paiement: paymentData.card_enabled ? 'carte' : 'livraison'
        }))
      } catch (error) {
        toast.error(error.response?.data?.message || 'Impossible de charger le panier')
      } finally {
        setLoading(false)
      }
    }

    fetchCartAndPaymentConfig()
  }, [])

  const refreshCart = async () => {
    const response = await api.get('/cart')
    setCart(response.data.data)
  }

  const updateQuantity = async (id_ligne, quantite) => {
    if (quantite < 1) return
    try {
      await api.put(`/cart/${id_ligne}`, { quantite })
      await refreshCart()
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible de modifier la quantite')
    }
  }

  const removeItem = async (id_ligne) => {
    try {
      await api.delete(`/cart/${id_ligne}`)
      await refreshCart()
      toast.success('Produit retire du panier')
    } catch (error) {
      toast.error('Erreur lors de la suppression')
    }
  }

  const formatPrice = (price) => (
    new Intl.NumberFormat('fr-MA', {
      style: 'currency',
      currency: 'MAD'
    }).format(price)
  )
  const deliveryFee = Number(cart.total) >= FREE_DELIVERY_THRESHOLD ? 0 : DELIVERY_FEE
  const orderTotal = Number(cart.total) + deliveryFee

  const handleCheckoutChange = (event) => {
    const { name, value } = event.target
    setCheckoutForm((current) => ({
      ...current,
      [name]: value
    }))
  }

  const handleCheckout = async (event) => {
    event.preventDefault()

    if (checkoutForm.methode_paiement === 'carte' && !paymentConfig.card_enabled) {
      toast.error(paymentConfig.card_message)
      setCheckoutForm((current) => ({
        ...current,
        methode_paiement: 'livraison'
      }))
      return
    }

    setSubmittingOrder(true)

    try {
      if (checkoutForm.methode_paiement === 'carte') {
        const payload = {
          adresse: checkoutForm.adresse,
          ville: checkoutForm.ville,
          code_postal: checkoutForm.code_postal,
          pays: checkoutForm.pays
        }

        // React sends only shipping details; Stripe card data is entered on Stripe Checkout, never in this app.
        const response = await api.post('/checkout/create-payment', payload)

        const checkoutUrl = response.data?.data?.checkout_url

        if (!checkoutUrl) {
          throw new Error('Lien de paiement introuvable.')
        }

        window.location.href = checkoutUrl
        return
      }

      await api.post('/orders', checkoutForm)

      await refreshCart()
      toast.success('Commande creee avec succes')
    } catch (error) {
      const validationErrors = error.response?.data?.errors
      const firstValidationError = validationErrors
        ? Object.values(validationErrors).flat()[0]
        : null
      toast.error(firstValidationError || error.response?.data?.message || error.message || 'Impossible de lancer le paiement')
    } finally {
      setSubmittingOrder(false)
    }
  }

  if (loading) {
    return (
      <div className="cart-loading">
        <div className="loading-spinner"></div>
      </div>
    )
  }

  if (cart.items.length === 0) {
    return (
      <div className="cart-empty">
        <div className="container">
          <div className="cart-empty-card glass-card">
            <h2>Votre panier est vide</h2>
            <p>Ajoutez quelques luminaires pour construire votre ambiance ideale.</p>
            <Link to="/products" className="btn-primary">Decouvrir la boutique</Link>
          </div>
        </div>
      </div>
    )
  }

  return (
    <div className="cart-page">
      <div className="container">
        <section className="cart-shell glass-card">
          <div className="cart-head">
            <div>
              <div className="cart-breadcrumb">
                <Link to="/">Accueil</Link>
                <span>/</span>
                <strong>Panier</strong>
              </div>
              <h1 className="cart-title">Votre selection lumineuse</h1>
            </div>
            <p>{cart.total_items} article(s) dans votre panier</p>
          </div>

          <div className="cart-grid">
            <div className="cart-items">
              {cart.items.map((item) => (
                <CartItem
                  key={item.id_ligne}
                  item={item}
                  formatPrice={formatPrice}
                  onUpdateQuantity={updateQuantity}
                  onRemove={removeItem}
                />
              ))}
            </div>

            <aside className="cart-summary">
              <h3>Resume de commande</h3>
              <div className="summary-row">
                <span>Sous-total</span>
                <span>{formatPrice(cart.total)}</span>
              </div>
              <div className="summary-row">
                <span>Livraison</span>
                <strong className={deliveryFee === 0 ? 'is-free' : 'is-paid'}>{deliveryFee === 0 ? 'Gratuite' : formatPrice(deliveryFee)}</strong>
              </div>
              <div className="summary-row">
                <span>Protection</span>
                <strong>Incluse</strong>
              </div>
              <div className="summary-total">
                <span>Total</span>
                <span>{formatPrice(orderTotal)}</span>
              </div>

              <form className="checkout-form" onSubmit={handleCheckout}>
                <div className="checkout-field">
                  <label htmlFor="adresse">Adresse</label>
                  <input id="adresse" name="adresse" placeholder="Entrez votre adresse" value={checkoutForm.adresse} onChange={handleCheckoutChange} required />
                </div>

                <div className="checkout-field">
                  <label htmlFor="pays">Pays</label>
                  <select id="pays" name="pays" value={checkoutForm.pays} onChange={handleCheckoutChange} required>
                    <option value="Maroc">Maroc</option>
                    <option value="France">France</option>
                    <option value="Espagne">Espagne</option>
                  </select>
                </div>

                <div className="checkout-grid">
                  <div className="checkout-field">
                    <label htmlFor="ville">Ville</label>
                    <input id="ville" name="ville" placeholder="Ville" value={checkoutForm.ville} onChange={handleCheckoutChange} required />
                  </div>
                  <div className="checkout-field">
                    <label htmlFor="code_postal">Code postal</label>
                    <input id="code_postal" name="code_postal" placeholder="Code postal" value={checkoutForm.code_postal} onChange={handleCheckoutChange} required />
                  </div>
                </div>

                {checkoutForm.methode_paiement === 'carte' ? (
                  <div className="demo-card-fields" aria-label="Carte de test">
                    <div className="checkout-field">
                      <label htmlFor="card_name">Nom sur la carte</label>
                      <input id="card_name" value="SOLARLIGHT CLIENT" readOnly />
                    </div>
                    <div className="checkout-field">
                      <label htmlFor="card_number">Numero de carte</label>
                      <input id="card_number" value="4242 4242 4242 4242" readOnly />
                    </div>
                    <div className="checkout-grid checkout-grid-card">
                      <div className="checkout-field">
                        <label htmlFor="card_month">Mois</label>
                        <input id="card_month" value="12" readOnly />
                      </div>
                      <div className="checkout-field">
                        <label htmlFor="card_year">Annee</label>
                        <input id="card_year" value="2028" readOnly />
                      </div>
                      <div className="checkout-field">
                        <label htmlFor="card_cvc">CVC</label>
                        <input id="card_cvc" value="123" readOnly />
                      </div>
                    </div>
                  </div>
                ) : null}

                <div className="checkout-methods">
                  <label className={`checkout-method ${checkoutForm.methode_paiement === 'carte' ? 'selected' : ''} ${!paymentConfig.card_enabled ? 'disabled' : ''}`}>
                    <input
                      type="radio"
                      name="methode_paiement"
                      value="carte"
                      checked={checkoutForm.methode_paiement === 'carte'}
                      onChange={handleCheckoutChange}
                      disabled={!paymentConfig.card_enabled}
                    />
                    <span>
                      <span className="checkout-method-title">
                        <FaCreditCard />
                        Carte bancaire
                        <span className="card-brands"><FaCcVisa /><FaCcMastercard /></span>
                      </span>
                      <small>
                        {paymentConfig.card_enabled
                          ? `Mode test local, compatible ${paymentConfig.card_label}`
                          : paymentConfig.card_message}
                      </small>
                    </span>
                  </label>

                  <label className={`checkout-method ${checkoutForm.methode_paiement === 'livraison' ? 'selected' : ''}`}>
                    <input
                      type="radio"
                      name="methode_paiement"
                      value="livraison"
                      checked={checkoutForm.methode_paiement === 'livraison'}
                      onChange={handleCheckoutChange}
                    />
                    <span>
                      <span className="checkout-method-title">
                        <FaTruck />
                        Paiement a la livraison
                      </span>
                      <small>La commande sera creee sans paiement en ligne</small>
                    </span>
                  </label>
                </div>

                <button
                  type="submit"
                  className="checkout-btn"
                  disabled={submittingOrder}
                >
                  {submittingOrder
                    ? checkoutForm.methode_paiement === 'carte'
                      ? 'Redirection securisee...'
                      : 'Confirmation en cours...'
                    : checkoutForm.methode_paiement === 'carte'
                      ? 'Payer maintenant'
                      : 'Confirmer la commande'}
                </button>
              </form>
            </aside>
          </div>

          <div className="cart-benefits-strip">
            {cartBenefits.map(([icon, title, text]) => (
              <article key={title}>
                <span>{icon}</span>
                <div>
                  <strong>{title}</strong>
                  <p>{text}</p>
                </div>
              </article>
            ))}
          </div>
        </section>
      </div>
    </div>
  )
}

export default Cart
