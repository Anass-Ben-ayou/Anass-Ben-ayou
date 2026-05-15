import React from 'react'
import { FaMinus, FaPlus, FaTrash } from 'react-icons/fa'
import { PRODUCT_IMAGE_FALLBACK, resolveProductImage } from '../../utils/productImages'
import './CartItem.css'

const CartItem = ({ item, formatPrice, onUpdateQuantity, onRemove }) => {
  return (
    <div className="cart-item">
      <img
        src={resolveProductImage(item.produit)}
        alt={item.produit.nom}
        className="cart-item-image"
        onError={(event) => {
          event.currentTarget.src = PRODUCT_IMAGE_FALLBACK
        }}
      />

      <div className="cart-item-info">
        <h3>{item.produit.nom}</h3>
        <p className="cart-item-meta">{item.produit.categorie?.nom || 'Luminaire premium'}</p>
        <p className="cart-item-price">{formatPrice(item.prix_unitaire)}</p>
      </div>

      <div className="cart-item-controls">
        <button
          type="button"
          onClick={() => onUpdateQuantity(item.id_ligne, item.quantite - 1)}
          aria-label={`Diminuer la quantite de ${item.produit.nom}`}
        >
          <FaMinus />
        </button>
        <span>{item.quantite}</span>
        <button
          type="button"
          onClick={() => onUpdateQuantity(item.id_ligne, item.quantite + 1)}
          aria-label={`Augmenter la quantite de ${item.produit.nom}`}
        >
          <FaPlus />
        </button>
      </div>

      <div className="cart-item-side">
        <strong>{formatPrice(item.sous_total)}</strong>
        <button type="button" onClick={() => onRemove(item.id_ligne)} className="remove-btn">
          <FaTrash />
          Retirer
        </button>
      </div>
    </div>
  )
}

export default CartItem
