import { api } from './api'

export const userService = {
  async getAdminUsers() {
    const response = await api.get('/admin/users')
    return response.data.data || []
  },

  async createAdminUser(payload) {
    const response = await api.post('/admin/users', payload)
    return response.data.data
  },

  async updateAdminUser(id, payload) {
    const response = await api.put(`/admin/users/${id}`, payload)
    return response.data.data
  },

  async deleteAdminUser(id) {
    const response = await api.delete(`/admin/users/${id}`)
    return response.data
  }
}
