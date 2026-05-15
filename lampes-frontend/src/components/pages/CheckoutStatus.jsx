import React, { useEffect, useMemo, useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import toast from 'react-hot-toast'
import { api } from '../../services/api'
import './CheckoutStatus.css'

const contentByPath = {
  '/checkout/success': {
    chip: 'Paiement confirme',
    title: 'Votre paiement a ete accepte.',
    fallbackMessage: 'Votre commande est enregistree et le montant a ete envoye vers votre compte marchand.',
  },
  '/checkout/pending': {
    chip: 'Paiement en attente',
    title: 'Votre paiement est en cours de verification.',
    fallbackMessage: 'Nous attendons encore la confirmation finale de la passerelle de paiement.',
  },
  '/checkout/failed': {
    chip: 'Paiement echoue',
    title: 'Le paiement n a pas pu etre finalise.',
    fallbackMessage: 'Aucun ordre paye n a ete cree. Vous pouvez reessayer quand vous le souhaitez.',
  },
}

const CheckoutStatus = () => {
  const location = useLocation()
  const [loading, setLoading] = useState(false)
  const [message, setMessage] = useState('')
  const [order, setOrder] = useState(null)
  const [confirmed, setConfirmed] = useState(false)
  const view = useMemo(() => contentByPath[location.pathname] || contentByPath['/checkout/failed'], [location.pathname])

  useEffect(() => {
    const orderId = new URLSearchParams(location.search).get('order_id')

    if (!orderId || location.pathname === '/checkout/failed') {
      return
    }

    const loadOrder = async () => {
      setLoading(true)

      try {
        const response = await api.get(`/orders/${orderId}`)
        setOrder(response.data?.data || null)
        setConfirmed(location.pathname === '/checkout/success')
        setMessage(view.fallbackMessage)
        if (location.pathname === '/checkout/success') {
          toast.success('Paiement confirme')
        }
      } catch (error) {
        setMessage(error.response?.data?.message || view.fallbackMessage)
      } finally {
        setLoading(false)
      }
    }

    loadOrder()
  }, [location.pathname, location.search, view.fallbackMessage])

  return (
    <div className="checkout-status-page">
      <div className="container">
        <div className="checkout-status-card glass-card">
          <span className="chip">{loading ? 'Verification' : view.chip}</span>
          <h1>{loading ? 'Verification de votre paiement...' : view.title}</h1>
          <p>{message || view.fallbackMessage}</p>

          {order && (
            <div className="checkout-order-summary">
              <div>
                <span>Commande</span>
                <strong>#{order.id_commande}</strong>
              </div>
              <div>
                <span>Total</span>
                <strong>{new Intl.NumberFormat('fr-MA', { style: 'currency', currency: order.currency || 'MAD' }).format(order.total || 0)}</strong>
              </div>
            </div>
          )}

          <div className="checkout-status-actions">
            <Link to={confirmed ? '/orders' : '/checkout'} className="btn-primary">
              {confirmed ? 'Voir mes commandes' : 'Retour au paiement'}
            </Link>
            <Link to="/boutique" className="btn-outline">Continuer mes achats</Link>
          </div>
        </div>
      </div>
    </div>
  )
}

export default CheckoutStatus
