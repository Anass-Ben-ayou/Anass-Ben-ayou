import React, { useEffect, useMemo, useState } from 'react'
import toast from 'react-hot-toast'
import { api } from '../../services/api'
import AdminTable from '../admin/AdminTable'
import '../admin/AdminUi.css'

const statusClass = (status) => String(status || '').toLowerCase().replace(/[^a-z0-9]+/g, '-')

const AdminOrders = () => {
  const [orders, setOrders] = useState([])
  const [loading, setLoading] = useState(false)

  const loadOrders = async () => {
    setLoading(true)

    try {
      const response = await api.get('/admin/orders')
      setOrders(Array.isArray(response.data?.data?.data) ? response.data.data.data : [])
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible de charger les commandes')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    loadOrders()
  }, [])

  const rows = useMemo(() => (
    orders.map((order) => ({
      id: order.id_commande,
      customer: `${order.client?.prenom || ''} ${order.client?.nom || ''}`.trim() || order.client?.email || 'Client',
      total: order.total,
      status: order.statut,
      paymentStatus: order.payment_status || order.paiement?.payment_status || order.paiement?.statut || 'pending',
      address: [
        order.livraison?.adresse,
        order.livraison?.ville,
        order.livraison?.code_postal,
        order.livraison?.pays
      ].filter(Boolean).join(', '),
      date: order.date_commande,
      order
    }))
  ), [orders])

  const columns = [
    { key: 'id', label: 'Commande', render: (row) => `#${row.id}` },
    { key: 'customer', label: 'Client' },
    {
      key: 'total',
      label: 'Montant',
      render: (row) => new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(row.total || 0)
    },
    {
      key: 'address',
      label: 'Adresse',
      render: (row) => row.address || '-'
    },
    {
      key: 'status',
      label: 'Statut commande',
      render: (row) => (
        <span className={`admin-chip admin-status-chip is-${statusClass(row.status)}`}>
          {row.status || '-'}
        </span>
      )
    },
    {
      key: 'paymentStatus',
      label: 'Statut paiement',
      render: (row) => (
        <span className={`admin-chip admin-status-chip is-${statusClass(row.paymentStatus)}`}>
          {row.paymentStatus || '-'}
        </span>
      )
    },
    {
      key: 'date',
      label: 'Date',
      render: (row) => row.date ? new Date(row.date).toLocaleString('fr-MA') : '-'
    }
  ]

  return (
    <div className="admin-page">
      <div className="container">
        <div className="admin-shell">
          <section className="admin-panel glass-card">
            <div className="admin-panel-header">
              <div>
                <h1>Commandes</h1>
                <p>Suivez les commandes, leurs montants et leur statut de paiement.</p>
              </div>
              <div className="admin-toolbar">
                <button type="button" className="admin-secondary-btn" onClick={loadOrders} disabled={loading}>
                  {loading ? 'Chargement...' : 'Rafraichir'}
                </button>
              </div>
            </div>

            <AdminTable columns={columns} rows={rows} emptyMessage="Aucune commande trouvee." />
          </section>
        </div>
      </div>
    </div>
  )
}

export default AdminOrders
