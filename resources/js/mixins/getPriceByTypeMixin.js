export const getPriceByTypeMixin = {
    methods: {
        getPriceByType(list, type = 1) {
            if (list != null) {
                let price1 = 0
                let price2 = 0
                let price3 = 0
                for (let index in list) {
                    let getPrice = this.calculatePrice(list[index].productTp, list[index], this.getMarginRates, this.getStoreSettings)
                    if (list[index].productTp == 1) {
                        price1 += getPrice.price * list[index].quantity
                    } else if (list[index].productTp == 2) {
                        price2 += getPrice.price * list[index].quantity
                    } else if (list[index].productTp == 3) {
                        price3 += getPrice.price * list[index].quantity
                    }
                }
                if (type == 1) {
                    return price1
                } else if (type == 2) {
                    return price2
                } else if (type == 3) {
                    return price3
                }
            }
        },
        getRegularPriceByType(list, type = 1) {
            if (list != null) {
                let price1 = 0
                let price2 = 0
                let price3 = 0
                for (let index in list) {
                    if (list[index].productTp == 1) {
                        price1 = price1 + (list[index].supplyUnitPrice * list[index].quantity)
                    } else if (list[index].productTp == 2) {
                        price2 = price2 + (list[index].supplyUnitPrice * list[index].quantity)
                    } else if (list[index].productTp == 3) {
                        price3 = price3 + (list[index].supplyUnitPrice * list[index].quantity)
                    }
                }
                if (type == 1) {
                    return price1
                } else if (type == 2) {
                    return price2
                } else if (type == 3) {
                    return price3
                }
            }
        },
    },
}
