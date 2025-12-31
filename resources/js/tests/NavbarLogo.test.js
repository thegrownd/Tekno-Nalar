import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import App from '../components/App.vue'
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', component: { template: '<div>Home</div>' } },
    { path: '/login', component: { template: '<div>Login</div>' } }
  ]
})

vi.mock('axios', () => ({
  default: {
    get: vi.fn(() => Promise.resolve({ data: [] })),
    post: vi.fn(),
    defaults: { headers: { common: {} } }
  }
}))

// Mock window.matchMedia
Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: vi.fn().mockImplementation(query => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: vi.fn(),
    removeListener: vi.fn(),
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  })),
})

describe('Navbar Logo', () => {
  it('displays the company logo in the navbar', async () => {
    const wrapper = mount(App, {
      global: {
        plugins: [router],
        stubs: {
          NotificationDropdown: true,
          ConfirmationModal: true,
          Transition: { template: '<div><slot /></div>' }
        }
      }
    })

    const navbar = wrapper.find('nav')
    expect(navbar.exists()).toBe(true)

    const logo = navbar.find('img[alt="Logo Tekno Nalar"]')
    expect(logo.exists()).toBe(true)
    
    // In some test environments, src might be resolved or prefixed.
    // We check if it contains the path we expect.
    expect(logo.attributes('src')).toContain('/Image/logo.png')
    expect(logo.attributes('width')).toBe('40')
    
    // Check if it is inside the router-link
    // Find the link that points to home
    const link = navbar.find('a[href="/"]')
    expect(link.exists()).toBe(true)
    
    // Check if image is inside this link
    const logoInLink = link.find('img[alt="Logo Tekno Nalar"]')
    expect(logoInLink.exists()).toBe(true)
  })
})
