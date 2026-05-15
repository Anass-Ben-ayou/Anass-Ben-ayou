import React, { useEffect, useMemo, useState } from 'react'
import toast from 'react-hot-toast'
import { api } from '../../services/api'
import AdminTable from '../admin/AdminTable'
import '../admin/AdminUi.css'

const statusClass = (status) => String(status || '').toLowerCase().replace(/[^a-z0-9]+/g, '-')

const AdminPayments = () => {
  const [payments, setPayments] = useState([])
  const [loading, setLoading] = useState(false)

  const loadPayments = async () => {
    setLoading(true)

    try {
      const response = await api.get('/admin/payments')
      setPayments(Array.isArray(response.data?.data?.data) ? response.data.data.data : [])
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible de charger les paiements')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    loadPayments()
  }, [])

  const rows = useMemo(() => (
    payments.map((payment) => ({
      id: payment.id_paiement,
      customer: `${payment.commande?.client?.prenom || ''} ${payment.commande?.client?.nom || ''}`.trim() || payment.commande?.client?.email || 'Client',
      orderId: payment.id_commande,
      amount: payment.montant,
      gateway: payment.payment_gateway || payment.methode || '-',
      status: payment.payment_status || payment.statut || '-',
      transactionId: payment.transaction_id || payment.reference_externe || '-',
      cardBrand: payment.card_brand || '-',
      cardLast4: payment.card_last4 || '-',
      date: payment.date_paiement
    }))
  ), [payments])

  const columns = [
    { key: 'customer', label: 'Client' },
    { key: 'orderId', label: 'Commande', render: (row) => `#${row.orderId}` },
    {
      key: 'amount',
      label: 'Montant',
      render: (row) => new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(row.amount || 0)
    },
    { key: 'gateway', label: 'Gateway' },
    {
      key: 'status',
      label: 'Statut',
      render: (row) => (
        <span className={`admin-chip admin-status-chip is-${statusClass(row.status)}`}>
          {row.status || '-'}
        </span>
      )
    },
    { key: 'transactionId', label: 'Transaction' },
    { key: 'cardBrand', label: 'Carte' },
    { key: 'cardLast4', label: 'Last4' },
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
                <h1>Paiements</h1>
                <p>Consultez les transactions, la passerelle, le statut et les metadonnees carte non sensibles.</p>
              </div>
              <div className="admin-toolbar">
                <button type="button" className="admin-secondary-btn" onClick={loadPayments} disabled={loading}>
                  {loading ? 'Chargement...' : 'Rafraichir'}
                </button>
              </div>
            </div>

            <AdminTable columns={columns} rows={rows} emptyMessage="Aucun paiement trouve." />
          </section>
        </div>
      </div>
    </div>
  )
}

export default AdminPayments
