import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import Articles from '../components/Articles.vue'
import { createRouter, createWebHistory } from 'vue-router'

// Mock axios
vi.mock('axios', () => ({
  default: {
    get: vi.fn(() => Promise.resolve({ data: [] })),
    post: vi.fn(),
    delete: vi.fn()
  }
}))

// Mock router
const router = createRouter({
  history: createWebHistory(),
  routes: [{ path: '/', component: { template: '<div>Home</div>' } }]
})

describe('Articles.vue - Pro Modal', () => {
  it('opens modal when "Gabung Sekarang" is clicked', async () => {
    const wrapper = mount(Articles, {
      global: {
        plugins: [router],
        stubs: {
            ConfirmModal: true,
            Transition: {
                template: '<div><slot /></div>'
            }
        }
      }
    })

    // Find the button
    const joinBtn = wrapper.findAll('a').find(a => a.text().includes('Gabung Sekarang'))
    expect(joinBtn.exists()).toBe(true)

    // Click it
    await joinBtn.trigger('click')

    // Check if modal text is visible
    expect(wrapper.text()).toContain('Fitur ini masih dalam pengembangan')
    expect(wrapper.text()).toContain('Kami sedang bekerja keras')
  })

  it('closes modal when close button is clicked', async () => {
    const wrapper = mount(Articles, {
      global: {
        plugins: [router],
        stubs: {
            ConfirmModal: true,
            Transition: {
                template: '<div><slot /></div>'
            }
        }
      }
    })

    // Open modal first
    const joinBtn = wrapper.findAll('a').find(a => a.text().includes('Gabung Sekarang'))
    await joinBtn.trigger('click')
    expect(wrapper.text()).toContain('Fitur ini masih dalam pengembangan')

    // Find close button inside modal
    const closeBtn = wrapper.findAll('button').find(b => b.text() === 'Tutup')
    expect(closeBtn.exists()).toBe(true)

    // Click close
    await closeBtn.trigger('click')

    // Check if modal is gone (text not found)
    // Note: Since we stubbed Transition, it might still be in DOM but v-if false.
    // Actually v-if removes it from DOM.
    expect(wrapper.text()).not.toContain('Fitur ini masih dalam pengembangan')
  })
})
