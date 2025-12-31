import { mount, flushPromises } from '@vue/test-utils'
import { describe, it, expect, vi, beforeAll } from 'vitest'
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
    addListener: vi.fn(), // deprecated
    removeListener: vi.fn(), // deprecated
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  })),
})

describe('App Sidebar Colors and Footer', () => {
  it('login button has correct color classes for light/dark themes', async () => {
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

    // Open sidebar
    const hamburger = wrapper.find('button[aria-label="Menu"]')
    await hamburger.trigger('click')
    await flushPromises()
    
    // Find sidebar login button
    const sidebar = wrapper.find('.fixed.inset-0')
    expect(sidebar.exists()).toBe(true)
    
    const loginLink = sidebar.findAll('a').find(a => a.text().includes('Login'))
    expect(loginLink).toBeDefined()
    
    const classes = loginLink.classes()
    expect(classes).toContain('text-gray-900')
    expect(classes).toContain('dark:text-white')
    expect(classes).toContain('duration-300')
  })

  it('footer has correct social media icons', () => {
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

    const footer = wrapper.find('footer')
    expect(footer.exists()).toBe(true)

    // Check for Instagram
    const instagram = footer.find('a[href*="instagram.com"]')
    expect(instagram.exists()).toBe(true)
    expect(instagram.attributes('href')).toContain('instagram.com/tekno.nalar')

    // Check for Discord
    const discord = footer.find('a[href*="discord.gg"]')
    expect(discord.exists()).toBe(true)
    expect(discord.attributes('href')).toContain('discord.gg/ZWdqkPSby4')

    // Check for Telegram (should NOT exist)
    const telegram = footer.find('a[href*="t.me"]')
    expect(telegram.exists()).toBe(false)
    const telegram2 = footer.find('a[href*="telegram.me"]')
    expect(telegram2.exists()).toBe(false)
  })
})
