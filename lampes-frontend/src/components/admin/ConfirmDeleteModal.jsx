import React from 'react'
import AdminModalForm from './AdminModalForm'

const ConfirmDeleteModal = ({
  isOpen,
  title,
  itemLabel,
  deleting,
  onClose,
  onConfirm
}) => (
  <AdminModalForm
    isOpen={isOpen}
    title={title}
    description=""
    onClose={onClose}
    onSubmit={(event) => {
      event.preventDefault()
      onConfirm()
    }}
    footer={(
      <div className="admin-form-actions">
        <button type="button" className="admin-secondary-btn" onClick={onClose}>
          Annuler
        </button>
        <button type="submit" className="admin-danger-btn" disabled={deleting}>
          {deleting ? 'Suppression...' : 'Supprimer'}
        </button>
      </div>
    )}
  >
    <p className="admin-danger-copy">
      Voulez-vous vraiment supprimer <strong>{itemLabel}</strong> ?
      Cette action est definitive.
    </p>
  </AdminModalForm>
)

export default ConfirmDeleteModal
