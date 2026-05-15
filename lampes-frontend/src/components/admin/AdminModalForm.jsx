import React from 'react'
import { FaTimes } from 'react-icons/fa'
import './AdminUi.css'

const AdminModalForm = ({
  isOpen,
  title,
  description,
  onClose,
  onSubmit,
  children,
  footer
}) => {
  if (!isOpen) {
    return null
  }

  return (
    <div className="admin-modal-backdrop" onClick={onClose}>
      <div className="admin-modal" onClick={(event) => event.stopPropagation()}>
        <div className="admin-modal-header">
          <div>
            <h3>{title}</h3>
            {description ? <p>{description}</p> : null}
          </div>
          <button type="button" className="admin-modal-close" onClick={onClose} aria-label="Fermer">
            <FaTimes />
          </button>
        </div>

        <form onSubmit={onSubmit}>
          {children}
          {footer}
        </form>
      </div>
    </div>
  )
}

export default AdminModalForm
