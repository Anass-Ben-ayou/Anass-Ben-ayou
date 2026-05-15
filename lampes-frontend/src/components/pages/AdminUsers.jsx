import React, { useEffect, useMemo, useState } from 'react'
import { FaHeadset, FaLock, FaPen, FaPlus, FaShieldAlt, FaSyncAlt, FaTrash, FaTruck } from 'react-icons/fa'
import toast from 'react-hot-toast'
import AdminTable from '../admin/AdminTable'
import AdminModalForm from '../admin/AdminModalForm'
import ConfirmDeleteModal from '../admin/ConfirmDeleteModal'
import { userService } from '../../services/userService'
import '../admin/AdminUi.css'

const emptyForm = {
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'user'
}

const adminBenefits = [
  [<FaLock />, 'Paiement securise', 'Commande protegee pour chaque achat'],
  [<FaTruck />, 'Livraison express', 'Preparation rapide partout au Maroc'],
  [<FaShieldAlt />, 'Garantie 2 ans', 'Concu pour un usage quotidien interieur et exterieur'],
  [<FaHeadset />, 'Service client', 'Accompagnement avant et apres votre commande']
]

const AdminUsers = () => {
  const [users, setUsers] = useState([])
  const [loading, setLoading] = useState(false)
  const [submitting, setSubmitting] = useState(false)
  const [deleting, setDeleting] = useState(false)
  const [editingUser, setEditingUser] = useState(null)
  const [deleteTarget, setDeleteTarget] = useState(null)
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [form, setForm] = useState(emptyForm)
  const [errors, setErrors] = useState({})

  const loadUsers = async () => {
    setLoading(true)

    try {
      const data = await userService.getAdminUsers()
      setUsers(Array.isArray(data) ? data : [])
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible de charger les utilisateurs')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    loadUsers()
  }, [])

  const rows = useMemo(() => (
    users.map((user) => ({
      id: user.id_client,
      name: `${user.prenom || ''} ${user.nom || ''}`.trim() || user.nom || 'Sans nom',
      email: user.email,
      role: user.role,
      raw: user
    }))
  ), [users])

  const columns = [
    {
      key: 'name',
      label: 'Utilisateur',
      render: (row) => (
        <div className="admin-user-mini">
          <div className="admin-user-avatar">
            {(row.name?.[0] || 'U').toUpperCase()}
          </div>
          <div>
            <strong>{row.name}</strong>
            <span>{row.email}</span>
          </div>
        </div>
      )
    },
    {
      key: 'role',
      label: 'Role',
      render: (row) => (
        <span className={`admin-chip admin-role-chip ${row.role === 'admin' ? 'is-admin' : 'is-user'}`}>
          {row.role === 'admin' ? 'admin' : 'user'}
        </span>
      )
    },
    {
      key: 'actions',
      label: 'Actions',
      render: (row) => (
        <div className="admin-row-actions">
          <button
            type="button"
            className="admin-secondary-btn"
            onClick={() => handleEdit(row.raw)}
          >
            <FaPen />
            Modifier
          </button>
          <button
            type="button"
            className="admin-danger-btn"
            onClick={() => setDeleteTarget(row.raw)}
          >
            <FaTrash />
            Supprimer
          </button>
        </div>
      )
    }
  ]

  const resetForm = () => {
    setForm(emptyForm)
    setErrors({})
    setEditingUser(null)
  }

  const handleAdd = () => {
    resetForm()
    setIsModalOpen(true)
  }

  const handleEdit = (user) => {
    setEditingUser(user)
    setErrors({})
    setForm({
      name: `${user.prenom || ''} ${user.nom || ''}`.trim(),
      email: user.email || '',
      password: '',
      password_confirmation: '',
      role: user.role === 'admin' ? 'admin' : 'user'
    })
    setIsModalOpen(true)
  }

  const handleChange = (event) => {
    const { name, value } = event.target

    setErrors((current) => ({
      ...current,
      [name]: ''
    }))

    setForm((current) => ({
      ...current,
      [name]: value
    }))
  }

  const handleSubmit = async (event) => {
    event.preventDefault()
    setSubmitting(true)
    setErrors({})

    try {
      const payload = {
        name: form.name.trim(),
        email: form.email.trim(),
        role: form.role
      }

      if (form.password) {
        payload.password = form.password
        payload.password_confirmation = form.password_confirmation
      }

      if (editingUser) {
        await userService.updateAdminUser(editingUser.id_client, payload)
        toast.success('Utilisateur modifie avec succes')
      } else {
        payload.password = form.password
        payload.password_confirmation = form.password_confirmation
        await userService.createAdminUser(payload)
        toast.success('Utilisateur cree avec succes')
      }

      setIsModalOpen(false)
      resetForm()
      await loadUsers()
    } catch (error) {
      const errorBag = error.response?.data?.errors || {}
      const normalizedErrors = Object.fromEntries(
        Object.entries(errorBag).map(([key, value]) => [key, Array.isArray(value) ? value[0] : value])
      )

      setErrors(normalizedErrors)
      toast.error(Object.values(normalizedErrors)[0] || error.response?.data?.message || 'Impossible d enregistrer l utilisateur')
    } finally {
      setSubmitting(false)
    }
  }

  const handleDelete = async () => {
    if (!deleteTarget) {
      return
    }

    setDeleting(true)

    try {
      await userService.deleteAdminUser(deleteTarget.id_client)
      toast.success('Utilisateur supprime avec succes')
      setDeleteTarget(null)
      await loadUsers()
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible de supprimer l utilisateur')
    } finally {
      setDeleting(false)
    }
  }

  return (
    <div className="admin-page">
      <div className="container">
        <div className="admin-shell">
          <section className="admin-panel glass-card">
            <div className="admin-panel-header">
              <div>
                <span className="admin-page-kicker">Utilisateurs</span>
                <h1>Gestion des utilisateurs</h1>
                <p>Ajoutez, modifiez ou supprimez des utilisateurs et attribuez le role administrateur si necessaire.</p>
              </div>
              <div className="admin-toolbar">
                <button type="button" className="admin-secondary-btn" onClick={loadUsers} disabled={loading}>
                  <FaSyncAlt />
                  {loading ? 'Chargement...' : 'Rafraichir'}
                </button>
                <button type="button" className="admin-primary-btn" onClick={handleAdd}>
                  <FaPlus />
                  Ajouter un utilisateur
                </button>
              </div>
            </div>

            <AdminTable columns={columns} rows={rows} emptyMessage="Aucun utilisateur trouve." />
          </section>

          <section className="admin-benefits-strip">
            {adminBenefits.map(([icon, title, text]) => (
              <article key={title}>
                <span>{icon}</span>
                <div>
                  <strong>{title}</strong>
                  <p>{text}</p>
                </div>
              </article>
            ))}
          </section>
        </div>
      </div>

      <AdminModalForm
        isOpen={isModalOpen}
        title={editingUser ? 'Modifier un utilisateur' : 'Ajouter un utilisateur'}
        description="Renseignez les informations du compte et choisissez le role admin ou user."
        onClose={() => {
          setIsModalOpen(false)
          resetForm()
        }}
        onSubmit={handleSubmit}
        footer={(
          <div className="admin-form-actions">
            <button
              type="button"
              className="admin-secondary-btn"
              onClick={() => {
                setIsModalOpen(false)
                resetForm()
              }}
            >
              Annuler
            </button>
            <button type="submit" className="admin-primary-btn" disabled={submitting}>
              {submitting ? 'Enregistrement...' : (editingUser ? 'Enregistrer' : 'Creer')}
            </button>
          </div>
        )}
      >
        <div className="admin-form-grid">
          <div className="admin-field admin-field-full">
            <label htmlFor="user-name">Nom</label>
            <input id="user-name" name="name" value={form.name} onChange={handleChange} required />
            {errors.name ? <small className="admin-field-error">{errors.name}</small> : null}
          </div>

          <div className="admin-field admin-field-full">
            <label htmlFor="user-email">Email</label>
            <input id="user-email" name="email" type="email" value={form.email} onChange={handleChange} required />
            {errors.email ? <small className="admin-field-error">{errors.email}</small> : null}
          </div>

          <div className="admin-field">
            <label htmlFor="user-password">{editingUser ? 'Nouveau mot de passe' : 'Mot de passe'}</label>
            <input
              id="user-password"
              name="password"
              type="password"
              value={form.password}
              onChange={handleChange}
              required={!editingUser}
            />
            {errors.password ? <small className="admin-field-error">{errors.password}</small> : null}
          </div>

          <div className="admin-field">
            <label htmlFor="user-password-confirmation">Confirmer le mot de passe</label>
            <input
              id="user-password-confirmation"
              name="password_confirmation"
              type="password"
              value={form.password_confirmation}
              onChange={handleChange}
              required={!editingUser || Boolean(form.password)}
            />
          </div>

          <div className="admin-field admin-field-full">
            <label htmlFor="user-role">Role</label>
            <select id="user-role" name="role" value={form.role} onChange={handleChange} required>
              <option value="user">user</option>
              <option value="admin">admin</option>
            </select>
            {errors.role ? <small className="admin-field-error">{errors.role}</small> : null}
          </div>
        </div>
      </AdminModalForm>

      <ConfirmDeleteModal
        isOpen={Boolean(deleteTarget)}
        title="Supprimer l utilisateur"
        itemLabel={deleteTarget ? `${deleteTarget.prenom || ''} ${deleteTarget.nom || ''}`.trim() : ''}
        deleting={deleting}
        onClose={() => setDeleteTarget(null)}
        onConfirm={handleDelete}
      />
    </div>
  )
}

export default AdminUsers
