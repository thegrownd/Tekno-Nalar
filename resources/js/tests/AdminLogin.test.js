import { mount, flushPromises } from '@vue/test-utils'
import AdminLogin from '../components/AdminLogin.vue'
import axios from 'axios'
import { vi, describe, it, expect, beforeEach } from 'vitest'

vi.mock('axios')

describe('AdminLogin.vue', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    localStorage.clear()
    // Mock window.location
    delete window.location
    window.location = { href: '' }
  })

  it('does not show default credentials', () => {
    const wrapper = mount(AdminLogin, {
      global: {
        stubs: ['router-link']
      }
    })
    expect(wrapper.text()).not.toContain('admin@gmail.com')
    expect(wrapper.text()).not.toContain('Nagasaya1')
    
    // Check inputs are empty
    const emailInput = wrapper.find('input[type="email"]')
    const passwordInput = wrapper.find('input[type="password"]')
    expect(emailInput.element.value).toBe('')
    expect(passwordInput.element.value).toBe('')
  })

  it('handles successful login', async () => {
    const wrapper = mount(AdminLogin, {
      global: {
        stubs: ['router-link']
      }
    })
    
    axios.post.mockResolvedValueOnce({
      data: {
        access_token: 'fake-token',
        user: { id: 1, is_admin: true },
        is_super_admin: true
      }
    })

    await wrapper.find('input[type="email"]').setValue('admin@test.com')
    await wrapper.find('input[type="password"]').setValue('password')
    await wrapper.find('form').trigger('submit')
    
    expect(axios.post).toHaveBeenCalledWith('/auth/login', {
      email: 'admin@test.com',
      password: 'password'
    })
    
    await flushPromises()
    
    expect(localStorage.getItem('token')).toBe('fake-token')
    expect(localStorage.getItem('is_super_admin')).toBe('true')
    expect(window.location.href).toBe('/admin/pending-articles')
  })

  it('activates lockout after 3 failed attempts', async () => {
    vi.useFakeTimers()
    const wrapper = mount(AdminLogin, {
      global: {
        stubs: ['router-link']
      }
    })
    
    axios.post.mockRejectedValue({
      response: { data: { message: 'Invalid credentials' } }
    })

    // Attempt 1
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.vm.loginAttempts).toBe(1)
    
    // Attempt 2
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.vm.loginAttempts).toBe(2)
    
    // Attempt 3
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.vm.loginAttempts).toBe(3)
    expect(wrapper.vm.isLocked).toBe(true)
    
    // Check if button is disabled
    // Note: disabled attribute might be empty string in DOM, but exists check is safer
    expect(wrapper.find('button').element.disabled).toBe(true)
    
    expect(wrapper.text()).toContain('Terlalu banyak percobaan gagal')

    vi.useRealTimers()
  })
})