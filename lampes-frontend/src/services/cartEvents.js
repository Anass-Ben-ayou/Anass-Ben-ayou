export const CART_UPDATED_EVENT = 'cart-updated'

// Broadcasts a cart refresh signal to any part of the UI that listens for it.
export const notifyCartUpdated = () => {
  if (typeof window === 'undefined') return
  window.dispatchEvent(new Event(CART_UPDATED_EVENT))
}
