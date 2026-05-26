import React, { useEffect, useState } from 'react'
import toast from 'react-hot-toast'
import { api } from '../../services/api'
import '../admin/AdminUi.css'

const Orders = () => {
  const [orders, setOrders] = useState([])
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    const loadOrders = async () => {
      setLoading(true)

      try {
        const response = await api.get('/orders')
        setOrders(Array.isArray(response.data?.data?.data) ? response.data.data.data : [])
      } catch (error) {
        toast.error(error.response?.data?.message || 'Impossible de charger vos commandes')
      } finally {
        setLoading(false)
      }
    }

    loadOrders()
  }, [])

  return (
    <div className="admin-page">
      <div className="container">
        <div className="admin-shell">
          <section className="admin-panel glass-card">
            <div className="admin-panel-header">
              <div>
                <h1>Mes commandes</h1>
                <p>Retrouvez vos commandes payees, leurs statuts et leurs adresses de livraison.</p>
              </div>
            </div>

            {loading ? <div className="loading-spinner"></div> : (
              <div className="admin-table-wrap">
                <table className="admin-table">
                  <thead>
                    <tr>
                      <th>Commande</th>
                      <th>Produits</th>
                      <th>Montant</th>
                      <th>Statut</th>
                      <th>Paiement</th>
                      <th>Adresse</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    {orders.length > 0 ? orders.map((order) => (
                      <tr key={order.id_commande}>
                        <td>#{order.id_commande}</td>
                        <td>
                          {(order.ligne_commandes || order.ligneCommandes || []).map((line) => (
                            <div key={line.id_ligne_commande || `${order.id_commande}-${line.id_produit}`}>
                              {line.produit?.nom || 'Produit'} x{line.quantite}
                            </div>
                          ))}
                        </td>
                        <td>{new Intl.NumberFormat('fr-MA', { style: 'currency', currency: order.currency || 'MAD' }).format(order.total || 0)}</td>
                        <td>{order.statut}</td>
                        <td>{order.payment_status || order.paiement?.payment_status || '-'}</td>
                        <td>{[
                          order.livraison?.adresse,
                          order.livraison?.ville,
                          order.livraison?.code_postal,
                          order.livraison?.pays
                        ].filter(Boolean).join(', ') || '-'}</td>
                        <td>{order.date_commande ? new Date(order.date_commande).toLocaleString('fr-MA') : '-'}</td>
                      </tr>
                    )) : (
                      <tr>
                        <td colSpan="7">Aucune commande trouvee.</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
            )}
          </section>
        </div>
      </div>
    </div>
  )
}

export default Orders
