import React, { useEffect, useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import { useLocation } from 'react-router-dom'
import { FaBoxOpen, FaEnvelope, FaHeart, FaMapMarkerAlt, FaPlusCircle, FaStar, FaUserShield, FaUsers } from 'react-icons/fa'
import toast from 'react-hot-toast'
import { useAuth } from '../contexts/AuthContext'
import { productService } from '../../services/productService'
import { userService } from '../../services/userService'
import { resolveProductImage } from '../../utils/productImages'
import './Dashboard.css'

const emptyProductForm = {
  nom: '',
  description: '',
  prix: '',
  stock: '10',
  id_categorie: '',
  image_file: null,
  existing_image: ''
}

const emptyUserForm = {
  nom: '',
  prenom: '',
  email: '',
  telephone: '',
  role: 'user',
  password: '',
  password_confirmation: ''
}

const Dashboard = () => {
  const { user } = useAuth()
  const location = useLocation()
  const isAdmin = user?.role === 'admin' || user?.email === 'admin@lampes.ma'
  const [adminSection, setAdminSection] = useState('products')
  const [categories, setCategories] = useState([])
  const [adminProducts, setAdminProducts] = useState([])
  const [adminUsers, setAdminUsers] = useState([])
  const [contactMessages, setContactMessages] = useState([])
  const [siteReviews, setSiteReviews] = useState([])
  const [adminFeedbackTab, setAdminFeedbackTab] = useState('messages')
  const [showAdminForm, setShowAdminForm] = useState(Boolean(isAdmin))
  const [showUserForm, setShowUserForm] = useState(false)
  const [submittingProduct, setSubmittingProduct] = useState(false)
  const [submittingUser, setSubmittingUser] = useState(false)
  const [loadingProducts, setLoadingProducts] = useState(false)
  const [loadingUsers, setLoadingUsers] = useState(false)
  const [loadingMessages, setLoadingMessages] = useState(false)
  const [loadingSiteReviews, setLoadingSiteReviews] = useState(false)
  const [deletingProductId, setDeletingProductId] = useState(null)
  const [deletingUserId, setDeletingUserId] = useState(null)
  const [editingProductId, setEditingProductId] = useState(null)
  const [editingUserId, setEditingUserId] = useState(null)
  const [productForm, setProductForm] = useState(emptyProductForm)
  const [userForm, setUserForm] = useState(emptyUserForm)
  const [productErrors, setProductErrors] = useState({})
  const [userErrors, setUserErrors] = useState({})
  const registrationDate = user?.date_inscription
    ? new Date(user.date_inscription).toLocaleDateString('fr-FR')
    : '-'

  const imagePreview = useMemo(() => (
    productForm.image_file ? URL.createObjectURL(productForm.image_file) : productForm.existing_image
  ), [productForm.existing_image, productForm.image_file])

  useEffect(() => {
    if (isAdmin) {
      setShowAdminForm(true)
    }
  }, [isAdmin])

  useEffect(() => {
    if (!isAdmin) {
      return
    }

    const params = new URLSearchParams(location.search)
    const section = params.get('section')

    if (section === 'users' || section === 'products' || section === 'messages') {
      setAdminSection(section)
    }
  }, [isAdmin, location.search])

  useEffect(() => () => {
    if (imagePreview && productForm.image_file) {
      URL.revokeObjectURL(imagePreview)
    }
  }, [imagePreview, productForm.image_file])

  useEffect(() => {
    if (!isAdmin) {
      return
    }

    const loadAdminData = async () => {
      try {
        const [categoryData, productData, userData, messageData, reviewData] = await Promise.all([
          productService.getCategories(),
          productService.getAdminProducts({ per_page: 50 }),
          userService.getAdminUsers(),
          productService.getAdminContactMessages(),
          productService.getAdminSiteReviews()
        ])

        setCategories(Array.isArray(categoryData) ? categoryData : [])
        setAdminProducts(Array.isArray(productData?.data) ? productData.data : [])
        setAdminUsers(Array.isArray(userData) ? userData : [])
        setContactMessages(Array.isArray(messageData) ? messageData : [])
        setSiteReviews(Array.isArray(reviewData) ? reviewData : [])
      } catch (error) {
        toast.error('Impossible de charger les donnees administrateur')
      }
    }

    loadAdminData()
  }, [isAdmin])

  useEffect(() => {
    if (isAdmin || !user) {
      return
    }

    const loadUserMessages = async () => {
      setLoadingMessages(true)

      try {
        const messageData = await productService.getMyContactMessages()
        setContactMessages(Array.isArray(messageData) ? messageData : [])
      } catch (error) {
        toast.error('Impossible de charger vos messages')
      } finally {
        setLoadingMessages(false)
      }
    }

    loadUserMessages()
  }, [isAdmin, user])

  const refreshAdminProducts = async () => {
    setLoadingProducts(true)

    try {
      const productData = await productService.getAdminProducts({ per_page: 50 })
      setAdminProducts(Array.isArray(productData?.data) ? productData.data : [])
    } catch (error) {
      toast.error('Impossible de rafraichir la liste des produits')
    } finally {
      setLoadingProducts(false)
    }
  }

  const refreshAdminUsers = async () => {
    setLoadingUsers(true)

    try {
      const userData = await userService.getAdminUsers()
      setAdminUsers(Array.isArray(userData) ? userData : [])
    } catch (error) {
      toast.error('Impossible de rafraichir la liste des utilisateurs')
    } finally {
      setLoadingUsers(false)
    }
  }

  const refreshContactMessages = async () => {
    setLoadingMessages(true)

    try {
      const messageData = isAdmin
        ? await productService.getAdminContactMessages()
        : await productService.getMyContactMessages()

      setContactMessages(Array.isArray(messageData) ? messageData : [])
    } catch (error) {
      toast.error(isAdmin ? 'Impossible de rafraichir les messages' : 'Impossible de charger vos messages')
    } finally {
      setLoadingMessages(false)
    }
  }

  const refreshSiteReviews = async () => {
    setLoadingSiteReviews(true)

    try {
      const reviewData = await productService.getAdminSiteReviews()
      setSiteReviews(Array.isArray(reviewData) ? reviewData : [])
    } catch (error) {
      toast.error('Impossible de rafraichir les avis utilisateurs')
    } finally {
      setLoadingSiteReviews(false)
    }
  }

  const refreshAdminFeedback = () => (
    adminFeedbackTab === 'messages' ? refreshContactMessages() : refreshSiteReviews()
  )

  const formatMessageDate = (date) => (
    date ? new Date(date).toLocaleString('fr-FR') : '-'
  )

  const resetProductForm = () => {
    setProductForm(emptyProductForm)
    setProductErrors({})
    setEditingProductId(null)
  }

  const resetUserForm = () => {
    setUserForm(emptyUserForm)
    setUserErrors({})
    setEditingUserId(null)
  }

  const handleProductChange = (event) => {
    const { name, value, files } = event.target

    setProductErrors((current) => ({
      ...current,
      [name]: ''
    }))

    if (name === 'image_file') {
      setProductForm((current) => ({
        ...current,
        image_file: files?.[0] || null
      }))
      return
    }

    setProductForm((current) => ({
      ...current,
      [name]: value
    }))
  }

  const handleUserChange = (event) => {
    const { name, value } = event.target

    setUserErrors((current) => ({
      ...current,
      [name]: ''
    }))

    setUserForm((current) => ({
      ...current,
      [name]: value
    }))
  }

  const handleEditProduct = (product) => {
    setAdminSection('products')
    setEditingProductId(product.id_produit || product.id)
    setShowAdminForm(true)
    setProductErrors({})
    setProductForm({
      nom: product.nom || product.name || '',
      description: product.description || '',
      prix: String(product.prix ?? product.price ?? ''),
      stock: String(product.stock ?? 0),
      id_categorie: String(product.id_categorie || product.category_id || product.categorie?.id_categorie || product.category?.id || ''),
      image_file: null,
      existing_image: resolveProductImage(product)
    })
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  const handleEditUser = (adminUser) => {
    setAdminSection('users')
    setEditingUserId(adminUser.id_client)
    setShowUserForm(true)
    setUserErrors({})
    setUserForm({
      nom: adminUser.nom || '',
      prenom: adminUser.prenom || '',
      email: adminUser.email || '',
      telephone: adminUser.telephone || '',
      role: adminUser.role || 'user',
      password: '',
      password_confirmation: ''
    })
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  const handleProductSubmit = async (event) => {
    event.preventDefault()
    setSubmittingProduct(true)
    setProductErrors({})

    try {
      const payload = {
        nom: productForm.nom.trim(),
        description: productForm.description.trim(),
        prix: productForm.prix,
        stock: productForm.stock,
        id_categorie: productForm.id_categorie,
        image_file: productForm.image_file
      }

      if (editingProductId) {
        await productService.updateAdminProduct(editingProductId, payload)
        toast.success('Produit modifie avec succes')
      } else {
        await productService.createAdminProduct(payload)
        toast.success('Produit ajoute avec succes')
      }

      resetProductForm()
      await refreshAdminProducts()
      setShowAdminForm(false)
    } catch (error) {
      const errorBag = error.response?.data?.errors || {}
      const normalizedErrors = Object.fromEntries(
        Object.entries(errorBag).map(([key, value]) => [key, Array.isArray(value) ? value[0] : value])
      )

      setProductErrors(normalizedErrors)
      const firstError = Object.values(normalizedErrors)[0]
      toast.error(firstError || error.response?.data?.message || 'Impossible d enregistrer le produit')
    } finally {
      setSubmittingProduct(false)
    }
  }

  const handleUserSubmit = async (event) => {
    event.preventDefault()
    setSubmittingUser(true)
    setUserErrors({})

    try {
      const payload = {
        nom: userForm.nom.trim(),
        prenom: userForm.prenom.trim(),
        email: userForm.email.trim(),
        telephone: userForm.telephone.trim(),
        role: userForm.role
      }

      if (userForm.password) {
        payload.password = userForm.password
        payload.password_confirmation = userForm.password_confirmation
      }

      if (editingUserId) {
        await userService.updateAdminUser(editingUserId, payload)
        toast.success('Utilisateur modifie avec succes')
      } else {
        payload.password = userForm.password
        payload.password_confirmation = userForm.password_confirmation
        await userService.createAdminUser(payload)
        toast.success('Utilisateur ajoute avec succes')
      }

      resetUserForm()
      await refreshAdminUsers()
      setShowUserForm(false)
    } catch (error) {
      const errorBag = error.response?.data?.errors || {}
      const normalizedErrors = Object.fromEntries(
        Object.entries(errorBag).map(([key, value]) => [key, Array.isArray(value) ? value[0] : value])
      )

      setUserErrors(normalizedErrors)
      const firstError = Object.values(normalizedErrors)[0]
      toast.error(firstError || error.response?.data?.message || 'Impossible d enregistrer l utilisateur')
    } finally {
      setSubmittingUser(false)
    }
  }

  const handleDeleteProduct = async (product) => {
    const productId = product.id_produit || product.id
    const productName = product.nom || product.name || 'ce produit'

    if (!window.confirm(`Supprimer ${productName} ?`)) {
      return
    }

    setDeletingProductId(productId)

    try {
      await productService.deleteAdminProduct(productId)
      toast.success('Produit supprime avec succes')

      if (editingProductId === productId) {
        resetProductForm()
        setShowAdminForm(false)
      }

      await refreshAdminProducts()
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible de supprimer le produit')
    } finally {
      setDeletingProductId(null)
    }
  }

  const handleDeleteUser = async (adminUser) => {
    if (!window.confirm(`Supprimer ${adminUser.prenom} ${adminUser.nom} ?`)) {
      return
    }

    setDeletingUserId(adminUser.id_client)

    try {
      await userService.deleteAdminUser(adminUser.id_client)
      toast.success('Utilisateur supprime avec succes')

      if (editingUserId === adminUser.id_client) {
        resetUserForm()
        setShowUserForm(false)
      }

      await refreshAdminUsers()
    } catch (error) {
      toast.error(error.response?.data?.message || 'Impossible de supprimer l utilisateur')
    } finally {
      setDeletingUserId(null)
    }
  }

  const adminNavigation = [
    { id: 'products', label: 'Produits', icon: <FaBoxOpen /> },
    { id: 'users', label: 'Gestion utilisateurs', icon: <FaUsers /> },
    { id: 'messages', label: 'Messages / Avis', icon: <FaEnvelope /> },
    { id: 'orders', label: 'Commandes', icon: <FaBoxOpen /> },
    { id: 'payments', label: 'Paiements', icon: <FaUserShield /> }
  ]
  const adminSectionTitle = {
    products: 'Apercu des produits',
    users: 'Apercu des utilisateurs',
    messages: 'Messages et avis utilisateurs'
  }[adminSection] || 'Administration'
  const canShowAdminFormToggle = isAdmin && (adminSection === 'products' || adminSection === 'users')
  const formatCurrency = (value) => (
    new Intl.NumberFormat('fr-MA', {
      style: 'currency',
      currency: 'MAD',
      maximumFractionDigits: 0
    }).format(value)
  )
  const totalStockValue = adminProducts.reduce((sum, product) => (
    sum + (Number(product.prix ?? product.price ?? 0) * Number(product.stock ?? 0))
  ), 0)
  const totalStock = adminProducts.reduce((sum, product) => sum + Number(product.stock ?? 0), 0)
  const lowStockProducts = adminProducts.filter((product) => Number(product.stock ?? 0) <= 4).length
  const adminStats = {
    products: [
      ['Total produits', adminProducts.length, 'Produits au total'],
      ['Valeur du stock', formatCurrency(totalStockValue), 'Valeur totale'],
      ['En stock', totalStock, 'Unites disponibles'],
      ['Faible stock', lowStockProducts, 'Produits a reapprovisionner']
    ],
    users: [
      ['Utilisateurs', adminUsers.length, 'Comptes clients'],
      ['Admins', adminUsers.filter((adminUser) => adminUser.role === 'admin').length, 'Acces administrateur'],
      ['Clients', adminUsers.filter((adminUser) => adminUser.role !== 'admin').length, 'Comptes utilisateurs'],
      ['Messages', contactMessages.length, 'Demandes recues']
    ],
    messages: [
      ['Messages', contactMessages.length, 'Depuis le formulaire contact'],
      ['Avis', siteReviews.length, 'Temoignages recus'],
      ['Approuves', siteReviews.filter((review) => review.is_approved).length, 'Avis visibles'],
      ['En attente', siteReviews.filter((review) => !review.is_approved).length, 'A verifier']
    ]
  }
  const userStats = [
    ['Profil', user ? 'Actif' : '-', 'Compte client'],
    ['Messages', contactMessages.length, 'Messages envoyes'],
    ['Telephone', user?.telephone ? 'Ajoute' : 'Manquant', 'Information de contact'],
    ['Inscription', registrationDate, 'Date du compte']
  ]
  const overviewStats = isAdmin ? (adminStats[adminSection] || adminStats.products) : userStats

  return (
    <div className="dashboard-page">
      <div className="container">
        <div className="dashboard-layout">
          <aside className="dashboard-sidebar glass-card">
            <span className="chip">{isAdmin ? 'Espace admin' : 'Espace client'}</span>
            <h2>{user ? `${user.prenom} ${user.nom}` : 'Votre compte'}</h2>
            <p>
              {isAdmin
                ? 'Gerez les produits, les comptes, les messages et les controles essentiels.'
                : 'Retrouvez votre profil, vos messages et les informations utiles de votre compte.'}
            </p>

            <div className="dashboard-menu">
              {isAdmin ? (
                adminNavigation.map((item) => (
                  item.id === 'orders' ? (
                    <Link key={item.id} to="/admin/orders" className="dashboard-menu-btn">
                      {item.icon}
                      {item.label}
                    </Link>
                  ) : item.id === 'payments' ? (
                    <Link key={item.id} to="/admin/payments" className="dashboard-menu-btn">
                      {item.icon}
                      {item.label}
                    </Link>
                  ) : (
                    <button
                      key={item.id}
                      type="button"
                      className={`dashboard-menu-btn ${adminSection === item.id ? 'active' : ''}`}
                      onClick={() => setAdminSection(item.id)}
                    >
                      {item.icon}
                      {item.label}
                    </button>
                  )
                ))
              ) : (
                <>
                  <div><FaUserShield /> Profil</div>
                  <div><FaBoxOpen /> Commandes</div>
                  <div><FaHeart /> Favoris</div>
                  <div><FaMapMarkerAlt /> Adresses</div>
                </>
              )}
            </div>

            {canShowAdminFormToggle ? (
              <button
                type="button"
                className="dashboard-admin-toggle"
                onClick={() => {
                  if (adminSection === 'users') {
                    if (showUserForm) {
                      resetUserForm()
                    }

                    setShowUserForm((current) => !current)
                    return
                  }

                  if (showAdminForm) {
                    resetProductForm()
                  }

                  setShowAdminForm((current) => !current)
                }}
              >
                <FaPlusCircle />
                {adminSection === 'users'
                  ? (showUserForm ? 'Fermer le formulaire' : 'Ajouter un utilisateur')
                  : (showAdminForm ? 'Fermer le formulaire' : 'Ajouter un produit')}
              </button>
            ) : null}
          </aside>

          <section className="dashboard-main">
            <div className="dashboard-card glass-card">
              <div className="dashboard-card-header">
                <div>
                  <span className="dashboard-kicker">{isAdmin ? 'Gestion SolarLight' : 'Tableau de bord client'}</span>
                  <h1>
                    {isAdmin
                      ? adminSectionTitle
                      : 'Mon espace'}
                  </h1>
                </div>
                {canShowAdminFormToggle ? (
                  <button
                    type="button"
                    className="dashboard-admin-toggle dashboard-admin-toggle-inline"
                    onClick={() => {
                      if (adminSection === 'users') {
                        if (showUserForm) {
                          resetUserForm()
                        }

                        setShowUserForm((current) => !current)
                        return
                      }

                      if (showAdminForm) {
                        resetProductForm()
                      }

                      setShowAdminForm((current) => !current)
                    }}
                  >
                    <FaPlusCircle />
                    {adminSection === 'users'
                      ? (showUserForm ? 'Masquer le formulaire' : 'Ajouter un utilisateur')
                      : (showAdminForm ? 'Masquer le formulaire' : 'Ajouter un produit')}
                  </button>
                ) : null}
              </div>

              <div className="dashboard-info">
                {overviewStats.map(([label, value, text]) => (
                  <div className="info-group" key={label}>
                    <label>{label}</label>
                    <p>{value}</p>
                    <span>{text}</span>
                  </div>
                ))}
              </div>
            </div>

            {isAdmin && adminSection === 'products' && showAdminForm ? (
              <div className="dashboard-card glass-card">
                <div className="admin-product-header">
                  <div>
                    <h2>{editingProductId ? 'Modifier le produit' : 'Ajouter un produit'}</h2>
                    <p>
                      {editingProductId
                        ? 'Mettez a jour le produit selectionne puis enregistrez les changements.'
                        : 'Ajoutez un produit ici. Il sera visible dans la liste admin et dans la boutique utilisateur.'}
                    </p>
                  </div>
                  <Link to="/boutique" className="admin-preview-link">Voir la boutique</Link>
                </div>

                <form className="admin-product-form" onSubmit={handleProductSubmit}>
                  <div className="admin-form-grid">
                    <label className="admin-field">
                      <span>Produit</span>
                      <input name="nom" value={productForm.nom} onChange={handleProductChange} required />
                      {productErrors.nom ? <small className="admin-field-error">{productErrors.nom}</small> : null}
                    </label>

                    <label className="admin-field">
                      <span>Categorie</span>
                      <select name="id_categorie" value={productForm.id_categorie} onChange={handleProductChange} required>
                        <option value="">Choisir une categorie</option>
                        {categories.map((category) => (
                          <option key={category.id_categorie || category.id} value={category.id_categorie || category.id}>
                            {category.nom || category.name}
                          </option>
                        ))}
                      </select>
                      {productErrors.id_categorie ? <small className="admin-field-error">{productErrors.id_categorie}</small> : null}
                    </label>

                    <label className="admin-field admin-field-full">
                      <span>Description</span>
                      <textarea name="description" value={productForm.description} onChange={handleProductChange} rows="8" required />
                      {productErrors.description ? <small className="admin-field-error">{productErrors.description}</small> : null}
                    </label>

                    <label className="admin-field">
                      <span>Prix</span>
                      <input name="prix" type="number" min="0" step="0.01" value={productForm.prix} onChange={handleProductChange} required />
                      {productErrors.prix ? <small className="admin-field-error">{productErrors.prix}</small> : null}
                    </label>

                    <label className="admin-field">
                      <span>Stock</span>
                      <input name="stock" type="number" min="0" value={productForm.stock} onChange={handleProductChange} required />
                      {productErrors.stock ? <small className="admin-field-error">{productErrors.stock}</small> : null}
                    </label>

                    <label className="admin-field admin-field-full">
                      <span>Image</span>
                      <input name="image_file" type="file" accept="image/*" onChange={handleProductChange} required={!editingProductId} />
                      {productErrors.image_file ? <small className="admin-field-error">{productErrors.image_file}</small> : null}
                    </label>

                    {imagePreview ? (
                      <div className="admin-image-preview">
                        <img src={imagePreview} alt="Apercu du produit" />
                      </div>
                    ) : null}
                  </div>

                  <div className="admin-form-actions">
                    {editingProductId ? (
                      <button
                        type="button"
                        className="admin-cancel-btn"
                        onClick={() => {
                          resetProductForm()
                          setShowAdminForm(false)
                        }}
                      >
                        Annuler
                      </button>
                    ) : null}
                    <button type="submit" className="dashboard-admin-submit" disabled={submittingProduct}>
                      {submittingProduct
                        ? (editingProductId ? 'Modification en cours...' : 'Ajout en cours...')
                        : (editingProductId ? 'Enregistrer les changements' : 'Ajouter le produit')}
                    </button>
                  </div>
                </form>
              </div>
            ) : null}

            {isAdmin && adminSection === 'users' && showUserForm ? (
              <div className="dashboard-card glass-card">
                <div className="admin-product-header">
                  <div>
                    <h2>{editingUserId ? 'Modifier un utilisateur' : 'Ajouter un utilisateur'}</h2>
                    <p>Vous pouvez creer un compte, modifier ses informations ou lui donner le role administrateur.</p>
                  </div>
                </div>

                <form className="admin-product-form" onSubmit={handleUserSubmit}>
                  <div className="admin-form-grid">
                    <label className="admin-field">
                      <span>Nom</span>
                      <input name="nom" value={userForm.nom} onChange={handleUserChange} required />
                      {userErrors.nom ? <small className="admin-field-error">{userErrors.nom}</small> : null}
                    </label>

                    <label className="admin-field">
                      <span>Prenom</span>
                      <input name="prenom" value={userForm.prenom} onChange={handleUserChange} required />
                      {userErrors.prenom ? <small className="admin-field-error">{userErrors.prenom}</small> : null}
                    </label>

                    <label className="admin-field">
                      <span>Email</span>
                      <input name="email" type="email" value={userForm.email} onChange={handleUserChange} required />
                      {userErrors.email ? <small className="admin-field-error">{userErrors.email}</small> : null}
                    </label>

                    <label className="admin-field">
                      <span>Telephone</span>
                      <input name="telephone" value={userForm.telephone} onChange={handleUserChange} required />
                      {userErrors.telephone ? <small className="admin-field-error">{userErrors.telephone}</small> : null}
                    </label>

                    <label className="admin-field">
                      <span>Role</span>
                      <select name="role" value={userForm.role} onChange={handleUserChange} required>
                        <option value="user">User</option>
                        <option value="admin">Administrateur</option>
                      </select>
                      {userErrors.role ? <small className="admin-field-error">{userErrors.role}</small> : null}
                    </label>

                    <label className="admin-field">
                      <span>{editingUserId ? 'Nouveau mot de passe' : 'Mot de passe'}</span>
                      <input
                        name="password"
                        type="password"
                        value={userForm.password}
                        onChange={handleUserChange}
                        required={!editingUserId}
                      />
                      {userErrors.password ? <small className="admin-field-error">{userErrors.password}</small> : null}
                    </label>

                    <label className="admin-field">
                      <span>Confirmation mot de passe</span>
                      <input
                        name="password_confirmation"
                        type="password"
                        value={userForm.password_confirmation}
                        onChange={handleUserChange}
                        required={!editingUserId || Boolean(userForm.password)}
                      />
                    </label>
                  </div>

                  <div className="admin-form-actions">
                    {editingUserId ? (
                      <button
                        type="button"
                        className="admin-cancel-btn"
                        onClick={() => {
                          resetUserForm()
                          setShowUserForm(false)
                        }}
                      >
                        Annuler
                      </button>
                    ) : null}
                    <button type="submit" className="dashboard-admin-submit" disabled={submittingUser}>
                      {submittingUser
                        ? (editingUserId ? 'Modification en cours...' : 'Creation en cours...')
                        : (editingUserId ? 'Enregistrer les changements' : 'Ajouter l utilisateur')}
                    </button>
                  </div>
                </form>
              </div>
            ) : null}

            {isAdmin && adminSection === 'products' ? (
              <div className="dashboard-card glass-card">
                <div className="admin-product-header">
                  <div>
                    <h2>Liste des produits admin</h2>
                    <p>Retrouvez et gerez tous vos produits en un seul endroit.</p>
                  </div>
                  <button type="button" className="admin-refresh-btn" onClick={refreshAdminProducts} disabled={loadingProducts}>
                    {loadingProducts ? 'Actualisation...' : 'Rafraichir'}
                  </button>
                </div>

                <div className="admin-product-list">
                  {adminProducts.map((product) => (
                    <article key={product.id_produit || product.id} className="admin-product-item">
                      <img src={resolveProductImage(product)} alt={product.nom || product.name} className="admin-product-thumb" />
                      <div className="admin-product-copy">
                        <strong>{product.nom || product.name}</strong>
                        <span>{product.categorie?.nom || product.category?.nom || product.category?.name || 'Sans categorie'}</span>
                      </div>
                      <div className="admin-product-meta">
                        <strong>{new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(product.prix ?? product.price ?? 0)}</strong>
                        <span>{product.stock} en stock</span>
                      </div>
                      <div className="admin-product-actions">
                        <button
                          type="button"
                          className="admin-action-btn"
                          onClick={() => handleEditProduct(product)}
                        >
                          Modifier
                        </button>
                        <button
                          type="button"
                          className="admin-action-btn admin-delete-btn"
                          onClick={() => handleDeleteProduct(product)}
                          disabled={deletingProductId === (product.id_produit || product.id)}
                        >
                          {deletingProductId === (product.id_produit || product.id) ? 'Suppression...' : 'Supprimer'}
                        </button>
                      </div>
                    </article>
                  ))}
                </div>
              </div>
            ) : null}

            {isAdmin && adminSection === 'users' ? (
              <div className="dashboard-card glass-card">
                <div className="admin-product-header">
                  <div>
                    <h2>Liste des utilisateurs</h2>
                    <p>Ajoutez, modifiez, supprimez des utilisateurs et attribuez le role administrateur.</p>
                  </div>
                  <button type="button" className="admin-refresh-btn" onClick={refreshAdminUsers} disabled={loadingUsers}>
                    {loadingUsers ? 'Actualisation...' : 'Rafraichir'}
                  </button>
                </div>

                <div className="admin-product-list">
                  {adminUsers.map((adminUser) => (
                    <article key={adminUser.id_client} className="admin-product-item admin-user-item">
                      <div className="admin-user-avatar">
                        {(adminUser.prenom?.[0] || adminUser.nom?.[0] || 'U').toUpperCase()}
                      </div>
                      <div className="admin-product-copy">
                        <strong>{`${adminUser.prenom || ''} ${adminUser.nom || ''}`.trim()}</strong>
                        <span>{adminUser.email}</span>
                      </div>
                      <div className="admin-product-meta">
                        <strong>{adminUser.role === 'admin' ? 'Administrateur' : 'User'}</strong>
                        <span>{adminUser.telephone || 'Sans telephone'}</span>
                      </div>
                      <div className="admin-product-actions">
                        <button
                          type="button"
                          className="admin-action-btn"
                          onClick={() => handleEditUser(adminUser)}
                        >
                          Modifier
                        </button>
                        <button
                          type="button"
                          className="admin-action-btn admin-delete-btn"
                          onClick={() => handleDeleteUser(adminUser)}
                          disabled={deletingUserId === adminUser.id_client}
                        >
                          {deletingUserId === adminUser.id_client ? 'Suppression...' : 'Supprimer'}
                        </button>
                      </div>
                    </article>
                  ))}
                </div>
              </div>
            ) : null}

            {isAdmin && adminSection === 'messages' ? (
              <div className="dashboard-card glass-card">
                <div className="admin-product-header">
                  <div>
                    <h2>{adminFeedbackTab === 'messages' ? 'Messages envoyes depuis Contactez-nous' : 'Avis envoyes par les utilisateurs'}</h2>
                    <p>
                      {adminFeedbackTab === 'messages'
                        ? 'Chaque message envoye depuis le formulaire Contactez-nous apparait ici.'
                        : 'Chaque temoignage envoye depuis Laisser un temoignage apparait ici.'}
                    </p>
                  </div>
                  <button
                    type="button"
                    className="admin-refresh-btn"
                    onClick={refreshAdminFeedback}
                    disabled={loadingMessages || loadingSiteReviews}
                  >
                    {loadingMessages || loadingSiteReviews ? 'Actualisation...' : 'Rafraichir'}
                  </button>
                </div>

                <div className="admin-feedback-tabs" role="tablist" aria-label="Changer entre messages et avis">
                  <button
                    type="button"
                    className={adminFeedbackTab === 'messages' ? 'active' : ''}
                    onClick={() => setAdminFeedbackTab('messages')}
                  >
                    Messages utilisateurs
                  </button>
                  <button
                    type="button"
                    className={adminFeedbackTab === 'reviews' ? 'active' : ''}
                    onClick={() => setAdminFeedbackTab('reviews')}
                  >
                    Avis utilisateurs
                  </button>
                </div>

                <div className="contact-message-list">
                  {adminFeedbackTab === 'messages' ? (
                    contactMessages.length ? contactMessages.map((message) => (
                      <article key={message.id_contact_message} className="contact-message-item">
                        <div className="contact-message-heading">
                          <div>
                            <strong>{message.subject}</strong>
                            <span>{message.name} - {message.email}</span>
                          </div>
                          <time>{formatMessageDate(message.created_at)}</time>
                        </div>
                        <p>{message.message}</p>
                      </article>
                    )) : (
                      <div className="contact-message-empty">
                        {loadingMessages ? 'Chargement des messages...' : 'Aucun message pour le moment.'}
                      </div>
                    )
                  ) : (
                    siteReviews.length ? siteReviews.map((review) => (
                      <article key={review.id} className="contact-message-item">
                        <div className="contact-message-heading">
                          <div>
                            <strong>{review.customer_name}</strong>
                            <span>{review.email || 'Email non renseigne'} - {review.is_approved ? 'Approuve' : 'En attente'}</span>
                          </div>
                          <time>{review.date || formatMessageDate(review.created_at)}</time>
                        </div>
                        <div className="admin-review-stars" aria-label={`${review.rating} etoiles`}>
                          {Array.from({ length: 5 }, (_, index) => (
                            <FaStar key={index} className={index < Number(review.rating) ? 'filled' : ''} />
                          ))}
                        </div>
                        <p>{review.comment}</p>
                      </article>
                    )) : (
                      <div className="contact-message-empty">
                        {loadingSiteReviews ? 'Chargement des avis...' : 'Aucun avis pour le moment.'}
                      </div>
                    )
                  )}
                </div>
              </div>
            ) : null}

            {!isAdmin ? (
              <>
                <div className="dashboard-card glass-card">
                  <div className="dashboard-card-header">
                    <div>
                      <span className="dashboard-kicker">Profil</span>
                      <h2>Informations du compte</h2>
                    </div>
                  </div>

                  <div className="dashboard-info dashboard-profile-info">
                    <div className="info-group">
                      <label>Nom complet</label>
                      <p>{user ? `${user.prenom} ${user.nom}` : '-'}</p>
                    </div>
                    <div className="info-group">
                      <label>Adresse e-mail</label>
                      <p>{user?.email || '-'}</p>
                    </div>
                    <div className="info-group">
                      <label>Telephone</label>
                      <p>{user?.telephone || '-'}</p>
                    </div>
                    <div className="info-group">
                      <label>Date d inscription</label>
                      <p>{registrationDate}</p>
                    </div>
                  </div>
                </div>

                <div className="dashboard-card glass-card">
                  <div className="admin-product-header">
                    <div>
                      <h2>Mes messages envoyes</h2>
                      <p>Les messages envoyes depuis Contactez-nous avec votre email sont affiches ici.</p>
                    </div>
                    <button type="button" className="admin-refresh-btn" onClick={refreshContactMessages} disabled={loadingMessages}>
                      {loadingMessages ? 'Actualisation...' : 'Rafraichir'}
                    </button>
                  </div>

                  <div className="contact-message-list">
                    {contactMessages.length ? contactMessages.map((message) => (
                      <article key={message.id_contact_message} className="contact-message-item">
                        <div className="contact-message-heading">
                          <div>
                            <strong>{message.subject}</strong>
                            <span>{message.email}</span>
                          </div>
                          <time>{formatMessageDate(message.created_at)}</time>
                        </div>
                        <p>{message.message}</p>
                      </article>
                    )) : (
                      <div className="contact-message-empty">
                        {loadingMessages ? 'Chargement des messages...' : 'Aucun message envoye pour le moment.'}
                      </div>
                    )}
                  </div>
                </div>

                <div className="dashboard-panels">
                  <article className="dashboard-mini glass-card">
                    <h3>Statut du compte</h3>
                    <p>Compte actif et pret pour vos prochaines commandes.</p>
                  </article>
                  <article className="dashboard-mini glass-card">
                    <h3>Conseil deco</h3>
                    <p>Associez une lampe de table chaude avec une suspension graphique pour equilibrer la piece.</p>
                  </article>
                </div>
              </>
            ) : (
              <div className="dashboard-panels">
                <article className="dashboard-mini glass-card">
                  <h3>Securite admin</h3>
                  <p>Les droits admin sont maintenant geres par un role utilisateur et verifies cote backend.</p>
                </article>
                <article className="dashboard-mini glass-card">
                  <h3>Gestion utilisateurs</h3>
                  <p>Depuis la navigation admin, vous pouvez creer des comptes, modifier leurs infos et changer leur role.</p>
                </article>
              </div>
            )}
          </section>
        </div>
      </div>
    </div>
  )
}

export default Dashboard
