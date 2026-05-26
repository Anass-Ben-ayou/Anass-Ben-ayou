import { api, publicApi } from './api'

const CACHE_TTL_MS = 60 * 1000
const requestCache = new Map()

const clearProductCache = () => {
  requestCache.clear()
}

// Removes empty filter values before requests hit the API validators.
const sanitizeParams = (params = {}) => (
  Object.fromEntries(
    Object.entries(params).filter(([, value]) => value !== undefined && value !== null && value !== '')
  )
)

// Builds a stable cache key from the endpoint and query params.
const buildCacheKey = (path, params = {}) => {
  const sortedParams = Object.entries(sanitizeParams(params))
    .sort(([left], [right]) => left.localeCompare(right))

  return JSON.stringify([path, sortedParams])
}

// Reuses recent public responses to avoid duplicate network work.
const getCachedResponse = async (path, params = {}, client = api) => {
  const normalizedParams = sanitizeParams(params)
  const cacheKey = buildCacheKey(path, normalizedParams)
  const cachedEntry = requestCache.get(cacheKey)

  if (cachedEntry && Date.now() - cachedEntry.timestamp < CACHE_TTL_MS) {
    return cachedEntry.data
  }

  const response = await client.get(path, { params: normalizedParams })
  const data = response.data.data

  requestCache.set(cacheKey, {
    data,
    timestamp: Date.now()
  })

  return data
}

export const productService = {
  // Fetches the full product list for the boutique page.
  async getProducts(params = {}) {
    return getCachedResponse('/products', params)
  },

  async getAdminProducts(params = {}) {
    const response = await api.get('/admin/products', {
      params: sanitizeParams(params)
    })

    return response.data.data
  },

  // Fetches only the homepage spotlight products.
  async getFeaturedProducts() {
    return (await getCachedResponse('/products/featured')) || []
  },

  // Fetches a single product by id or slug.
  async getProduct(id) {
    return getCachedResponse(`/products/${id}`)
  },

  async getProductReviews(id) {
    const response = await api.get(`/products/${id}/reviews`)
    return response.data.data
  },

  async getPurchasedReviewProducts() {
    const response = await api.get('/reviews/purchased-products')
    return response.data.data
  },

  async createProductReview(payload) {
    const response = await api.post('/reviews', payload)
    clearProductCache()
    return response.data
  },

  // Fetches the category list for filters and sections.
  async getCategories() {
    return (await getCachedResponse('/categories')) || []
  },

  async getAllCategories() {
    return (await getCachedResponse('/categories', { include_empty: 1 })) || []
  },

  // Fetches products belonging to one category.
  async getCategoryProducts(id, params = {}) {
    return getCachedResponse(`/categories/${id}/products`, params)
  },

  // Fetches storefront collection cards.
  async getCollections() {
    return (await getCachedResponse('/collections')) || []
  },

  // Fetches the products for one storefront collection.
  async getCollectionProducts(id) {
    return getCachedResponse(`/collections/${id}/products`)
  },

  // Fetches products sorted as best sellers.
  async getBestSellers() {
    return (await getCachedResponse('/products/best-sellers')) || []
  },

  // Fetches the newest products for the homepage.
  async getNewProducts() {
    return (await getCachedResponse('/products/nouveautes')) || []
  },

  async createAdminProduct(payload) {
    const response = await api.post('/admin/products', buildProductFormData(payload), {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    clearProductCache()
    return response.data.data
  },

  async updateAdminProduct(id, payload) {
    const response = await api.post(`/admin/products/${id}`, buildProductFormData({
      ...payload,
      _method: 'PUT'
    }), {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    clearProductCache()
    return response.data.data
  },

  async deleteAdminProduct(id) {
    const response = await api.delete(`/admin/products/${id}`)
    clearProductCache()
    return response.data
  },

  // Sends the public contact form.
  async sendContactMessage(payload) {
    const response = await api.post('/contact', payload)
    return response.data
  },

  async getMyContactMessages() {
    const response = await api.get('/contact-messages')
    return response.data.data
  },

  async getAdminContactMessages() {
    const response = await api.get('/admin/contact-messages')
    return response.data.data
  },

  async getAdminSiteReviews() {
    const response = await api.get('/admin/site-reviews')
    return response.data.data
  },

  // Fetches public contact-page testimonials.
  async getSiteReviews() {
    return (await getCachedResponse('/reviews', {}, publicApi)) || []
  },

  // Stores a public testimonial from the contact page.
  async createSiteReview(payload) {
    const response = await publicApi.post('/reviews', payload)
    // Clear the cached reviews list so the freshly submitted user message appears immediately.
    clearProductCache()
    return response.data
  }
}

const buildProductFormData = (payload) => {
    const formData = new FormData()

    Object.entries(payload).forEach(([key, value]) => {
      if (value === undefined || value === null || value === '') {
        return
      }

      if (key === 'image_file' && value instanceof File) {
        formData.append('image_file', value)
        return
      }

      if (key === 'gallery_images' && Array.isArray(value)) {
        value.forEach((image, index) => {
          if (image) {
            formData.append(`gallery_images[${index}]`, image)
          }
        })
        return
      }

      if (key === 'specifications' && typeof value === 'object' && !Array.isArray(value)) {
        Object.entries(value).forEach(([specKey, specValue]) => {
          formData.append(`specifications[${specKey}]`, specValue)
        })
        return
      }

      formData.append(key, value)
    })

    return formData
}

export { clearProductCache }
