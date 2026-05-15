import React, { useEffect, useMemo, useState } from 'react'
import { FaHeadset, FaLock, FaPen, FaPlus, FaShieldAlt, FaSyncAlt, FaTrash, FaTruck } from 'react-icons/fa'
import toast from 'react-hot-toast'
import AdminTable from '../admin/AdminTable'
import AdminModalForm from '../admin/AdminModalForm'
import ConfirmDeleteModal from '../admin/ConfirmDeleteModal'
import { productService } from '../../services/productService'
import { resolveProductImage } from '../../utils/productImages'
import '../admin/AdminUi.css'

const emptyForm = {
  nom: '',
  description: '',
  prix: '',
  stock: '',
  id_categorie: '',
  image_file: null,
  existing_image: ''
}

const adminBenefits = [
  [<FaLock />, 'Paiement securise', 'Commande protegee pour chaque achat'],
  [<FaTruck />, 'Livraison express', 'Preparation rapide partout au Maroc'],
  [<FaShieldAlt />, 'Garantie 2 ans', 'Concu pour un usage quotidien interieur et exterieur'],
  [<FaHeadset />, 'Service client', 'Accompagnement avant et apres votre commande']
]

const buildProductCode = (product) => (
  product.reference || product.sku || product.code || `SL-${String(product.nom || product.name || 'PRODUIT')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-zA-Z0-9]+/g, '-')
    .replace(/^-|-$/g, '')
    .toUpperCase()}`
)

const AdminProducts = () => {
  const [products, setProducts] = useState([])
  const [categories, setCategories] = useState([])
  const [loading, setLoading] = useState(false)
  const [submitting, setSubmitting] = useState(false)
  const [deleting, setDeleting] = useState(false)
  const [editingProduct, setEditingProduct] = useState(null)
  const [deleteTarget, setDeleteTarget] = useState(null)
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [form, setForm] = useState(emptyForm)
  const [errors, setErrors] = useState({})

  const imagePreview = useMemo(() => (
    form.image_file ? URL.createObjectURL(form.image_file) : form.existing_image
  ), [form.existing_image, form.image_file])

  useEffect(() => () => {
    if (imagePreview && form.image_file) {
      URL.revokeObjectURL(imagePreview)
    }
  }, [form.image_file, imagePreview])

  const loadAdminData = async () => {
    setLoading(true)

    try {
      const [categoryData, productData] = await Promise.all([
        productService.getCategories(),
        productService.getAdminProducts({ per_page: 100 })
      ])

      setCategories(Array.isArray(categoryData) ? categoryData : [])
      setProducts(Array.isArray(productData?.data) ? productData.data : [])
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible de charger les produits admin')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    loadAdminData()
  }, [])

  const rows = useMemo(() => (
    products.map((product) => ({
      id: product.id_produit || product.id,
      name: product.nom || product.name,
      category: product.categorie?.nom || product.category?.name || 'Sans categorie',
      price: product.prix ?? product.price ?? 0,
      stock: product.stock ?? 0,
      image: resolveProductImage(product),
      code: buildProductCode(product),
      raw: product
    }))
  ), [products])

  const columns = [
    {
      key: 'name',
      label: 'Produit',
      render: (row) => (
        <div className="admin-product-mini">
          <img src={row.image} alt={row.name} />
          <div>
            <strong>{row.name}</strong>
            <span>{row.code}</span>
          </div>
        </div>
      )
    },
    {
      key: 'price',
      label: 'Prix',
      render: (row) => new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(row.price)
    },
    {
      key: 'stock',
      label: 'Stock',
      render: (row) => `${row.stock}`
    },
    {
      key: 'category',
      label: 'Categorie',
      render: (row) => row.category
    },
    {
      key: 'status',
      label: 'Statut',
      render: (row) => (
        <span className={`admin-chip admin-status-chip ${Number(row.stock) <= 4 ? 'is-warning' : 'is-success'}`}>
          {Number(row.stock) <= 4 ? 'Faible stock' : 'En stock'}
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
            className="admin-secondary-btn admin-icon-action"
            onClick={() => handleEdit(row.raw)}
            aria-label="Modifier"
          >
            <FaPen />
          </button>
          <button
            type="button"
            className="admin-danger-btn admin-icon-action"
            onClick={() => setDeleteTarget(row.raw)}
            aria-label="Supprimer"
          >
            <FaTrash />
          </button>
        </div>
      )
    }
  ]

  const resetForm = () => {
    setForm(emptyForm)
    setErrors({})
    setEditingProduct(null)
  }

  const handleAdd = () => {
    resetForm()
    setIsModalOpen(true)
  }

  const handleEdit = (product) => {
    setEditingProduct(product)
    setErrors({})
    setForm({
      nom: product.nom || product.name || '',
      description: product.description || '',
      prix: String(product.prix ?? product.price ?? ''),
      stock: String(product.stock ?? ''),
      id_categorie: String(product.id_categorie || product.categorie?.id_categorie || product.category?.id || ''),
      image_file: null,
      existing_image: resolveProductImage(product)
    })
    setIsModalOpen(true)
  }

  const handleChange = (event) => {
    const { name, value, files } = event.target

    setErrors((current) => ({
      ...current,
      [name]: ''
    }))

    if (name === 'image_file') {
      setForm((current) => ({
        ...current,
        image_file: files?.[0] || null
      }))
      return
    }

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
        nom: form.nom.trim(),
        description: form.description.trim(),
        prix: form.prix,
        stock: form.stock,
        id_categorie: form.id_categorie,
        image_file: form.image_file
      }

      if (editingProduct) {
        await productService.updateAdminProduct(editingProduct.id_produit || editingProduct.id, payload)
        toast.success('Produit modifie avec succes')
      } else {
        await productService.createAdminProduct(payload)
        toast.success('Produit cree avec succes')
      }

      setIsModalOpen(false)
      resetForm()
      await loadAdminData()
    } catch (error) {
      const errorBag = error.response?.data?.errors || {}
      const normalizedErrors = Object.fromEntries(
        Object.entries(errorBag).map(([key, value]) => [key, Array.isArray(value) ? value[0] : value])
      )

      setErrors(normalizedErrors)
      toast.error(Object.values(normalizedErrors)[0] || error.response?.data?.message || 'Impossible d enregistrer le produit')
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
      await productService.deleteAdminProduct(deleteTarget.id_produit || deleteTarget.id)
      toast.success('Produit supprime avec succes')
      setDeleteTarget(null)
      await loadAdminData()
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible de supprimer le produit')
    } finally {
      setDeleting(false)
    }
  }

  return (
    <div className="admin-page admin-products-page">
      <div className="container">
        <div className="admin-shell">
          <section className="admin-panel glass-card">
            <div className="admin-panel-header">
              <div>
                <div className="admin-breadcrumb">
                  <span>Accueil</span>
                  <span>/</span>
                  <strong>Produits</strong>
                </div>
                <h1>Gestion des Produits</h1>
                <p>Gerez et suivez vos produits. Modifiez les prix, le stock, les categories et le statut.</p>
              </div>
              <div className="admin-toolbar">
                <button type="button" className="admin-secondary-btn" onClick={loadAdminData} disabled={loading}>
                  <FaSyncAlt />
                  {loading ? 'Chargement...' : 'Rafraichir'}
                </button>
                <button type="button" className="admin-primary-btn" onClick={handleAdd}>
                  <FaPlus />
                  Ajouter un produit
                </button>
              </div>
            </div>

            <AdminTable columns={columns} rows={rows} emptyMessage="Aucun produit trouve." />
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
        title={editingProduct ? 'Modifier un produit' : 'Ajouter un produit'}
        description="Renseignez les informations du produit. L image peut etre changee a tout moment."
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
              {submitting ? 'Enregistrement...' : (editingProduct ? 'Enregistrer' : 'Creer')}
            </button>
          </div>
        )}
      >
        <div className="admin-form-grid">
          <div className="admin-field">
            <label htmlFor="product-name">Nom</label>
            <input id="product-name" name="nom" value={form.nom} onChange={handleChange} required />
            {errors.nom ? <small className="admin-field-error">{errors.nom}</small> : null}
          </div>

          <div className="admin-field">
            <label htmlFor="product-category">Categorie</label>
            <select id="product-category" name="id_categorie" value={form.id_categorie} onChange={handleChange} required>
              <option value="">Choisir une categorie</option>
              {categories.map((category) => (
                <option key={category.id_categorie || category.id} value={category.id_categorie || category.id}>
                  {category.nom || category.name}
                </option>
              ))}
            </select>
            {errors.id_categorie ? <small className="admin-field-error">{errors.id_categorie}</small> : null}
          </div>

          <div className="admin-field admin-field-full">
            <label htmlFor="product-description">Description</label>
            <textarea id="product-description" name="description" value={form.description} onChange={handleChange} required />
            {errors.description ? <small className="admin-field-error">{errors.description}</small> : null}
          </div>

          <div className="admin-field">
            <label htmlFor="product-price">Prix</label>
            <input id="product-price" name="prix" type="number" min="0" step="0.01" value={form.prix} onChange={handleChange} required />
            {errors.prix ? <small className="admin-field-error">{errors.prix}</small> : null}
          </div>

          <div className="admin-field">
            <label htmlFor="product-stock">Stock</label>
            <input id="product-stock" name="stock" type="number" min="0" value={form.stock} onChange={handleChange} required />
            {errors.stock ? <small className="admin-field-error">{errors.stock}</small> : null}
          </div>

          <div className="admin-field admin-field-full">
            <label htmlFor="product-image">Image</label>
            <input id="product-image" name="image_file" type="file" accept="image/*" onChange={handleChange} required={!editingProduct} />
            {errors.image_file ? <small className="admin-field-error">{errors.image_file}</small> : null}
          </div>

          {imagePreview ? (
            <div className="admin-field admin-field-full">
              <div className="admin-image-preview-box">
                <img src={imagePreview} alt="Apercu du produit" />
                <div>
                  <strong>Apercu de l image</strong>
                  <p>{editingProduct ? 'Laisser vide pour conserver l image actuelle.' : 'L image choisie sera utilisee pour le produit.'}</p>
                </div>
              </div>
            </div>
          ) : null}
        </div>
      </AdminModalForm>

      <ConfirmDeleteModal
        isOpen={Boolean(deleteTarget)}
        title="Supprimer le produit"
        itemLabel={deleteTarget ? (deleteTarget.nom || deleteTarget.name) : ''}
        deleting={deleting}
        onClose={() => setDeleteTarget(null)}
        onConfirm={handleDelete}
      />
    </div>
  )
}

export default AdminProducts
