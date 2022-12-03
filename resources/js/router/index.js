import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes';

const router = createRouter({
    history: createWebHistory(),
    routes, // short for `routes: routes`
    // scroll to top
    scrollBehavior(to, from, savedPosition) {
        return {
            el: '#app',
            top: 0,
        }
    },
})

// Add title page
const DEFAULT_TITLE = 'Doosoun AOS';
router.beforeEach((to, from, next) => {
    document.title = to.meta.title || DEFAULT_TITLE
    next()
})

export default router
