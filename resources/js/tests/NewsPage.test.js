import { mount, flushPromises } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import NewsPage from '../components/NewsPage.vue'
import axios from 'axios'
import { createRouter, createWebHistory } from 'vue-router'

// Mock Router
const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/news', component: { template: '<div>News</div>' } },
    { path: '/articles/:id', component: { template: '<div>Detail</div>' } },
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

describe('NewsPage Article Click', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    localStorage.clear()
  })

  it('renders articles, handles click, and marks as read', async () => {
    const articles = [
      { id: 1, title: 'Article 1', category: 'Tech', created_at: '2025-01-01', image_url: 'img1.jpg', content: 'Content 1' },
      { id: 2, title: 'Article 2', category: 'Cyber', created_at: '2025-01-02', image_url: 'img2.jpg', content: 'Content 2' },
      { id: 3, title: 'Article 3', category: 'Tech', created_at: '2025-01-03', image_url: 'img3.jpg', content: 'Content 3' },
      { id: 4, title: 'Article 4', category: 'Tech', created_at: '2025-01-04', image_url: 'img4.jpg', content: 'Content 4' },
      { id: 5, title: 'Article 5', category: 'Tech', created_at: '2025-01-05', image_url: 'img5.jpg', content: 'Content 5' },
      { id: 6, title: 'Article 6', category: 'Tech', created_at: '2025-01-06', image_url: 'img6.jpg', content: 'Content 6' }
    ]
    
    // NewsPage uses slice(0, 5) for trending, so make sure we have enough
    axios.get.mockResolvedValue({ data: articles })
    
    // Spy on router push
    const pushSpy = vi.spyOn(router, 'push')

    const wrapper = mount(NewsPage, {
      global: {
        plugins: [router]
      }
    })

    await flushPromises()

    // Check if trending articles are rendered (should be 5 based on slice)
    // Wait, computed property trending = slice(0, 5).
    // The template iterates v-for="t in trending".
    // Article 1 is Hero? No, Hero is articles[0].
    // Hero logic: const hero = computed(() => (articles.value.length ? articles.value[0] : null))
    // Trending logic: const trending = computed(() => articles.value.slice(0, 5))
    // Wait, Hero is also in Trending? 
    // Yes, slice(0, 5) includes index 0.
    // So the first article is both Hero and first in Trending list?
    // Usually you'd do slice(1, 6) for trending if hero is 0.
    // But the current code is: `slice(0, 5)`.
    // So it appears twice. That's fine for the test, just checking interaction.

    const articleElements = wrapper.findAll('article')
    expect(articleElements.length).toBeGreaterThan(0)
    
    // Click the first article in the trending list
    await articleElements[0].trigger('click')
    
    // Verify router push to the correct ID
    // articles[0] is id 1.
    expect(pushSpy).toHaveBeenCalledWith('/articles/1')
    
    // Verify localStorage
    const readArticles = JSON.parse(localStorage.getItem('read_articles'))
    expect(readArticles).toContain(1)
    
    // Check if class changed
    await wrapper.vm.$nextTick()
    
    // Re-find the element to check classes
    const updatedArticle = wrapper.findAll('article')[0]
    expect(updatedArticle.classes()).toContain('opacity-75')
    
    // Verify "Dibaca" badge exists
    expect(updatedArticle.text()).toContain('Dibaca')
  })
})
