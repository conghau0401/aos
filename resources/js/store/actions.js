const actions = {
    getMenus: async ({ commit }) => {
        try {
            let response = await axios.get(apiURL + '/category-menu')
            commit('GET_MENU', response)
        } catch (error) {
            console.log(error)
        }
    },
    getSliders: async ({ commit }) => {
        try {
            let response = await axios.get(apiURL + '/banners')
            commit('GET_SLIDER', response)
        } catch (error) {
            console.log(error)
        }
    },
    getProducts: async ({ commit }) => {
        try {
            let response = await axios.get(apiURL + '/carts')
            commit('GET_CART', response)
        } catch (error) {
            console.log(error)
            outError()
        }
    },
    getWishlistProducts: async ({ commit }) => {
        try {
            let response = await axios.get(apiURL + '/product/wishlist')
            commit('GET_WISHLIST_PRODUCT', response);
        } catch (error) {
            console.log(error)
            outError()
        }
    },
    getRegularProducts: async ({ commit }) => {
        try {
            let response = await axios.get(apiURL + '/product/regular')
            commit('GET_REGULAR_PRODUCT', response);
        } catch (error) {
            console.log(error)
            outError()
        }
    },
    getMarginRates: async ({ commit }) => {
        try {
            let response = await axios.get(apiURL + '/carts/margin-rate')
            commit('GET_MARGIN_RATE', response);
        } catch (error) {
            console.log(error)
            outError()
        }
    },
    getStoreSettings: async ({ commit }) => {
        try {
            let response = await axios.get(apiURL + '/user/store-settings')
            commit('GET_INFO_SETTING', response);
        } catch (error) {
            console.log(error)
            outError()
        }
    },
    getCenterInfos: async ({ commit }) => {
        try {
            let response = await axios.get(apiURL + '/core/center-info')
            commit('GET_CENTER_INFO', response);
        } catch (error) {
            console.log(error)
            outError()
        }
    },
    getStoreInfos: async ({ commit }) => {
        try {
            let response = await axios.get(apiURL + '/user/store')
            commit('GET_STORE_INFO', response)
        } catch (error) {
            console.log(error)
            outError()
        }
    },
}

export default actions
