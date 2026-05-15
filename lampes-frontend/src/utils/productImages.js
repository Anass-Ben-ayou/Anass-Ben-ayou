export const PRODUCT_IMAGE_FALLBACK = 'https://via.placeholder.com/900x900?text=Solarlight'

const API_URL = (
  process.env.REACT_APP_API_URL ||
  'http://localhost:8000/api/v1'
).replace(/\/+$/, '')

const API_ORIGIN = API_URL.replace(/\/api(?:\/v1)?$/, '')

const normalizeImageUrl = (value) => {
  if (!value || typeof value !== 'string') {
    return null
  }

  if (/^https?:\/\//i.test(value) || value.startsWith('data:') || value.startsWith('blob:')) {
    return value
  }

  if (value.startsWith('/')) {
    return `${API_ORIGIN}${value}`
  }

  if (value.startsWith('storage/') || value.startsWith('catalog-import/')) {
    return `${API_ORIGIN}/${value}`
  }

  return `${API_ORIGIN}/storage/${value.replace(/^\/+/, '')}`
}

export const resolveProductImage = (product) => {
  if (!product) {
    return PRODUCT_IMAGE_FALLBACK
  }

  const galleryImage = Array.isArray(product.gallery_images)
    ? product.gallery_images.find(Boolean)
    : null

  const image = galleryImage
    || product.image_url
    || product.image
    || PRODUCT_IMAGE_FALLBACK

  return normalizeImageUrl(image) || PRODUCT_IMAGE_FALLBACK
}
