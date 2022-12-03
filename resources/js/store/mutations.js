const mutations = {
    GET_MENU (state, response) {
        state.menus = response.data
    },
    GET_SLIDER (state, response) {
        state.sliders = response.data.data
    },
    GET_CART (state, response) {
        state.products = response.data
        state.shippingFee = response.data.shipping_fee
    },
    GET_WISHLIST_PRODUCT (state, response) {
        state.wishlistProducts = response.data
    },
    GET_REGULAR_PRODUCT (state, response) {
        state.regularProducts = response.data.data.selected
    },
    GET_MARGIN_RATE (state, response) {
        state.marginRates = response.data.data
    },
    GET_INFO_SETTING (state, response) {
        state.storeSettings = response.data.data != null ? response.data.data : {}
    },
    GET_CENTER_INFO (state, response) {
        state.centerInfos = response.data.data
        // set icon
        let favicon = document.querySelector("link[rel~='icon']")
        favicon.href = state.centerInfos.faviconUrl
    },
    GET_STORE_INFO (state, response) {
        state.storeInfos = response.data.data
    },
    addProduct (state, product) {
        state.products.data.push(product)
    },
    updateProduct (state, product) {
        if (state.products.data.length == 0) {
            state.products.data.push(product)
        } else {
            // if exist => update quantity
            let flag = 0;
            for (let index in state.products.data) {
                if(product.ds_product_id == state.products.data[index].ds_product_id
                    && product.ds_option_id == state.products.data[index].ds_option_id
                    && product.shipping_method == state.products.data[index].shipping_method)
                {
                    state.products.data[index].quantity = product.quantity
                    flag = 1
                    break
                }
            }
            // if not exist => push to state
            if (flag == 0) {
                state.products.data.push(product)
            }
        }
        // delete quantity 0
        if (product.quantity == 0) {
            for (let index in state.products.data) {
                if(product.ds_product_id == state.products.data[index].ds_product_id
                    && product.ds_option_id == state.products.data[index].ds_option_id
                    && product.shipping_method == state.products.data[index].shipping_method)
                {
                    state.products.data.splice(index, 1)
                }
            }
        }
    },
    updateQuantityProduct (state, cart) {
        // delete quantity 0
        if (cart.quantity == 0) {
            for (let index in state.products.data) {
                if(cart.id == state.products.data[index].id) {
                    state.products.data.splice(index, 1)
                }
            }
        }
        // if exist => update quantity
        let flag = 0;
        for (let index in state.products.data) {
            if(cart.id == state.products.data[index].id) {
                state.products.data[index].quantity = cart.quantity
                flag = 1
                break
            }
        }
        // if not exist => push to state
        if (flag == 0) {
            state.products.data.push(product)
        }
    },
    removeCart (state, type) {
        if (state.products.data != undefined){
            if (type == null) {
                state.products.data = []
            } else if (type == 'pickup') {
                let listProduct = state.products.data.filter(item => {
                    return item.shipping_method != 1
                });
                state.products.data = listProduct
            } else if (type == 'delivery') {
                let listProduct = state.products.data.filter(item => {
                    return item.shipping_method != 2
                });
                state.products.data = listProduct
            }
        }
    },
    addRegularProduct (state, product) {
        state.regularProducts.data.push(product)
    },
    updateRegularProduct (state, product) {
        state.regularProducts = state.regularProducts.map(prd => {
            if (prd.optionId == product.optionId && prd.productId == product.productId) {
                prd.quantity = product.quantity
            }
            return prd;
        })
    },
    deleteProductInCart (state, arrId) {
        let data = state.products.data.filter(item => !arrId.includes(item.id));
        state.products.data = data
    },
    switchShippingMethodCart (state, {ids, shippingMethod}) {
        state.products.data = state.products.data.map(product => {
            if (ids.includes(product.id)) {
                product.shipping_method = shippingMethod;
            }
            return product;
        })
    },
    updateCheckedItem (state, {item, routeName}) {
        if (routeName == 'ShoppingCart') {
            // if empty => push to state
            if (state.checkedItem.length == 0) {
                state.checkedItem.push(item)
            } else {
                // if exist => update quantity
                let flag = 0;
                for (let index in state.checkedItem) {
                    if(item.ds_product_id == state.checkedItem[index].ds_product_id
                        && item.ds_option_id == state.checkedItem[index].ds_option_id
                        && item.shipping_method == state.checkedItem[index].shipping_method)
                    {
                        state.checkedItem[index].quantityUpdate = item.quantityUpdate
                        flag = 1
                        break
                    }
                }
                // if not exist => push to state
                if (flag == 0) {
                    state.checkedItem.push(item)
                }
            }
            if (!state.idsChecked.includes(item.id)) {
                state.idsChecked.push(item.id)
            }
        } else if (routeName == 'PlanProduct' || routeName == 'DiscountProduct' || routeName == 'WishlistProduct') {
            // if empty => push to state
            if (state.checkedItem.length == 0) {
                state.checkedItem.push(item)
            } else {
                // if exist => update quantity
                let flag = 0;
                for (let index in state.checkedItem) {
                    if(item.productId == state.checkedItem[index].productId) {
                        state.checkedItem[index].quantityUpdate = item.quantityUpdate
                        state.checkedItem[index].compareOptionId = item.compareOptionId
                        flag = 1
                        break
                    }
                }
                // if not exist => push to state
                if (flag == 0) {
                    state.checkedItem.push(item)
                }
            }
        }  else if (routeName == 'RegularDelivery') {
            // if empty => push to state
            if (state.checkedItem.length == 0) {
                state.checkedItem.push(item)
            } else {
                // if exist => update quantity
                let flag = 0;
                for (let index in state.checkedItem) {
                    if(item.idMerge == state.checkedItem[index].idMerge) {
                        state.checkedItem[index].quantityUpdate = item.quantityUpdate
                        state.checkedItem[index].weekdayChecked = item.weekdayChecked
                        flag = 1
                        break
                    }
                }
                // if not exist => push to state
                if (flag == 0) {
                    state.checkedItem.push(item)
                }
            }
        }
    },
    updateIdsChecked (state, id) {
        if (state.idsChecked.length == 0) {
            state.idsChecked.push(id)
        } else if (!state.idsChecked.includes(id)) {
            // if not exist => push to state
            state.idsChecked.push(id)
        } else {
            // if exist => remove from state
            for (let key in state.idsChecked) {
                if (state.idsChecked[key] == id) {
                    state.idsChecked.splice(key, 1)
                }
            }
        }
    },
    addIdsChecked (state, id) {
        if (!state.idsChecked.includes(id)) {
            // if not exist => push to state
            state.idsChecked.push(id)
        }
    },
}

export default mutations
