export const addToCart = {
    methods: {
        addToCart(event, product, ds_product_id, image, ds_option_id, quantity, shipping_method, page='') {
            let price = 0;
            let option_name;
            let quantityPerPack;
            let bottlePrice
            let containerUnitPrice
            let supplyUnitPrice
            let limited
            let stockQty
            let option_sell_yn
            //check optionName
            if (!product.optionName) {
                for(let i in product.options) {
                    if (product.options[i].optionId == ds_option_id) {
                        price = product.options[i].price
                        if (product.options[i].optionNm != undefined) {
                            option_name = product.options[i].optionNm
                        } else {
                            option_name = product.options[i].optionName
                        }
                        quantityPerPack = product.options[i].quantityPerPack
                        bottlePrice = product.options[i].bottlePrice
                        containerUnitPrice = product.options[i].containerUnitPrice
                        supplyUnitPrice = product.options[i].supplyUnitPrice
                        limited = product.options[i].limited
                        stockQty = product.options[i].stockQty
                        option_sell_yn = product.options[i].option_sell_yn
                    }
                }
            }
            // page cart
            if (product.hasOwnProperty('option_sell_yn')) {
                limited = product.limited
                stockQty = product.stockQty
                option_sell_yn = product.option_sell_yn
            }
            let infomationProduct = {
                "ds_option_id": ds_option_id,
                "ds_product_id": ds_product_id,
                "maxOrder": product.maxOrder,
                "productTp": product.productTp,
                "shipping_method": String(shipping_method),
                "quantity": quantity,
                "price": price,
                "marketableSize": product.marketableSize,
                "productNm": product.productNm,
                "product_image": image,
                "optionName": option_name,
                "quantityPerPack": quantityPerPack,
                "bottlePrice": bottlePrice,
                "containerUnitPrice": containerUnitPrice,
                "supplyUnitPrice": supplyUnitPrice,
                "limited": limited,
                "stockQty": stockQty,
                "option_sell_yn": option_sell_yn,
            }
            if (infomationProduct.option_sell_yn == true || (infomationProduct.limited == true && infomationProduct.stockQty < this.quantity)) { // check avalible add cart
                alert(this.$t("modal.notEnoughInventory"))
            } else if (this.quantity === 0 && product.maxOrder != 0) { // check quantity
                alert(this.$t("modal.quantityZero"))
            } else if (product.maxOrder === 0) {
                alert("Out of stock")
            } else {
                if (page != 'popup') {
                    // animation add to cart
                    event.preventDefault();
                    event.stopPropagation();
                    let cart = $('#cart');
                    let el = $(event.target)
                    let item = el.parents(".ar-product");
                    let img = item.find(".img-product").find("img");
                    let cartTopOffset = cart.offset().top - item.offset().top
                    let cartLeftOffset = cart.offset().left - item.offset().left;
                    let flyingImg = $('<img class="b-flying-img">');
                    flyingImg.attr('src', img.attr('src'));
                    flyingImg.css({'width': '200', 'height': '200', 'border-radius': '50%'});
                    flyingImg.animate({
                        top: cartTopOffset,
                        left: cartLeftOffset,
                        width: 30,
                        height: 30,
                        opacity: 0.1
                    }, 800, function () {
                        flyingImg.remove();
                    });
                    item.append(flyingImg);
                }

                let cartText = $(".num-cart");
                let numCart = parseInt(cartText.text());
                let flag = 0;
                for (let i=0; i < this.productInCart.length; i++) {
                    if (ds_product_id == this.productInCart[i].ds_product_id && ds_option_id == this.productInCart[i].ds_option_id && shipping_method == this.productInCart[i].shipping_method) {
                        flag = 1
                    }
                }
                if (flag == 0) {
                    //add new product
                    numCart = this.productInCart.length + 1;
                    this.$store.commit('addProduct', infomationProduct)
                } else {
                    //update product
                    this.$store.commit('updateProduct', infomationProduct);
                }
                cartText.text(numCart);

                if (page == 'popup') {
                    alert(this.$t("modal.addCartSuccess"))
                    this._cancel()
                    this.$emit("isProductInCart", true)
                }
                // uncheck
                let routeName = this.$route.name
                if (routeName == 'PlanProduct' || routeName == 'DiscountProduct' || routeName == 'WishlistProduct') {
                    this.toggleCheckBox(ds_product_id)
                }

                // add product in api cart
                axios.post(apiURL + "/carts/create" , {
                    "ds_product_id": ds_product_id,
                    "ds_option_id": ds_option_id,
                    "quantity": quantity,
                    "shipping_method": shipping_method,
                })
                .then(response => {
                    console.log(response)
                })
                .catch(error => {
                    console.log(error);
                });
            }
        }
    }
}
