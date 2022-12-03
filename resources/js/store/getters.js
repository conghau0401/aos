const getters = {
    getMenus: (state) => state.menus,
    getSliders: (state) => state.sliders,
    getProducts: (state) => state.products,
    getWishlistProducts: (state) => state.wishlistProducts,
    getRegularProducts: (state) => state.regularProducts,
    getMarginRates: (state) => state.marginRates,
    getStoreSettings: (state) => state.storeSettings,
    getCenterInfos: (state) => state.centerInfos,
    getConditionsSearchOrder: (state) => state.conditionsSearchOrder,
    getStoreInfos: (state) => state.storeInfos,
}

export default getters
