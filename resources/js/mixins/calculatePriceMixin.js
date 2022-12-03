export const calculatePriceMixin = {
    methods: {
        calculatePrice(productType, option, marginRates, storeInfos, quantity = 1) {
            let price = option.supplyUnitPrice ? option.supplyUnitPrice : option.supply_unit_price
            let discount_price = option.discount_price
            let rateByShippingMethod = 0
            // default price
            let priceDisplay = {
                price: price,
                discount_price: discount_price,
            }
            if (productType == null ||
                Object.keys(marginRates).length == 0 ||
                Object.keys(marginRates).storeInfos == 0) {
                return priceDisplay
            }
            if (storeInfos.defaultShippingMethod == 1) {
                rateByShippingMethod = marginRates[productType].center_pickup.margin
            } else if (storeInfos.defaultShippingMethod == 3) {
                rateByShippingMethod = marginRates[productType].supplier.margin
            } else { // default set margin rate delivery
                rateByShippingMethod = marginRates[productType].delivery.margin
            }
            if (productType == 3) { // calculate alcohol products
                price = (quantity * option.supplyUnitPrice) + (option.quantityPerPack * parseInt(option.bottlePrice) || 0 * quantity) + (quantity * parseInt(option.containerUnitPrice) || 0) + (quantity * option.supplyUnitPrice * rateByShippingMethod / 100)
                discount_price = (quantity * option.discount_price) + (option.quantityPerPack * parseInt(option.bottlePrice) || 0 * quantity) + (quantity * parseInt(option.containerUnitPrice) || 0) + (quantity * option.discount_price * rateByShippingMethod / 100)
            } else {
                price = price + (price * rateByShippingMethod / 100)
                discount_price = discount_price + (discount_price * rateByShippingMethod / 100)
            }
            priceDisplay.price = price
            priceDisplay.discount_price = parseInt(discount_price)
            return priceDisplay
        },
    },
}
