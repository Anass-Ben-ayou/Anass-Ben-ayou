import {
  clearStoredToken,
  getStoredToken,
  storeToken
} from './services/authStorage'

describe('authStorage', () => {
  afterEach(() => {
    clearStoredToken()
  })

  test('stores and retrieves a session token', () => {
    storeToken('demo-token')

    expect(getStoredToken()).toBe('demo-token')
  })

  test('clears the stored token', () => {
    storeToken('demo-token')
    clearStoredToken()

    expect(getStoredToken()).toBeNull()
  })
})
