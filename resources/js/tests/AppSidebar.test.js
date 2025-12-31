import { mount, flushPromises } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import App from '../components/App.vue'
import axios from 'axios'
import { createRouter, createWebHistory } from 'vue-router'

// Mock Router
const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', component: { template: '<div>Home</div>' } },
    { path: '/news', component: { template: '<div>News</div>' } },
    { path: '/my-articles', component: { template: '<div>My Articles</div>' } },
    { path: '/admin/pending-articles', component: { template: '<div>Verify</div>' } },
    { path: '/admin/users', component: { template: '<div>Users</div>' } },
    { path: '/login', component: { template: '<div>Login</div>' } },
    { path: '/about', component: { template: '<div>About</div>' } },
  ]
})

// Mock Axios
vi.mock('axios', () => {
    return {
        default: {
            get: vi.fn(),
            post: vi.fn(),
            defaults: { headers: { common: {} } }
        }
    }
})

describe('App Sidebar', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    localStorage.clear()
    // Reset window.matchMedia mock if needed
    Object.defineProperty(window, 'matchMedia', {
      writable: true,
      value: vi.fn().mockImplementation(query => ({
        matches: false,
        media: query,
        onchange: null,
        addListener: vi.fn(), // deprecated
        removeListener: vi.fn(), // deprecated
        addEventListener: vi.fn(),
        removeEventListener: vi.fn(),
        dispatchEvent: vi.fn(),
      })),
    })
  })

  it('shows hamburger button when authenticated', async () => {
    localStorage.setItem('token', 'fake-token')
    
    const wrapper = mount(App, {
      global: {
        plugins: [router],
        stubs: {
          NotificationDropdown: true,
          ConfirmationModal: true
        }
      }
    })

    await flushPromises()
    
    // Check if hamburger button exists
    const hamburger = wrapper.find('button[aria-label="Menu"]')
    expect(hamburger.exists()).toBe(true)
  })

  it('shows hamburger button when not authenticated', async () => {
    // No token
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

    const hamburger = wrapper.find('button[aria-label="Menu"]')
    expect(hamburger.exists()).toBe(true)
  })

  it('fetches and shows sidebar menu on click', async () => {
    localStorage.setItem('token', 'fake-token')
    
    const menuItems = [
      { label: 'Artikel Saya', to: '/my-articles', icon: 'document' }
    ]
    axios.get.mockResolvedValue({ data: menuItems })

    const wrapper = mount(App, {
      global: {
        plugins: [router],
        stubs: {
          NotificationDropdown: true,
          ConfirmationModal: true,
          Transition: {
             template: '<div><slot /></div>'
          }
        }
      }
    })

    const hamburger = wrapper.find('button[aria-label="Menu"]')
    expect(hamburger.exists()).toBe(true)
    
    await hamburger.trigger('click')

    // Expect axios call
    expect(axios.get).toHaveBeenCalledWith('/sidebar-menu')
    
    await flushPromises()

    // Check if menu item is rendered
    expect(wrapper.text()).toContain('Artikel Saya')
  })
})
