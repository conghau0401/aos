export const countNumber = {
    data() {
        return {
            quantity: 0,
            biggestNumber: Number
        };
    },
    methods: {
        decreaseNumber(e) {
            if (this.quantity === 0) {
                this.quantity = this.quantity
            } else {
                this.quantity -= 1
            }
            let routeName = this.$route.name
            if (routeName == 'ShoppingCart') {
                this.commitShoppingCart(routeName)
            } else if (routeName == 'PlanProduct' || routeName == 'DiscountProduct' || routeName == 'WishlistProduct') {
                this.commitPlan(routeName)
            } else if (routeName == 'RegularDelivery') {
                this.commitRegular(routeName)
            }
        },
        increaseNumber(e, biggestNumber) {
            if (this.quantity >= biggestNumber) {
                this.quantity = biggestNumber
            } else {
                this.quantity += 1
            }
            let routeName = this.$route.name
            if (routeName == 'ShoppingCart') {
                this.commitShoppingCart(routeName)
            } else if (routeName == 'PlanProduct' || routeName == 'DiscountProduct' || routeName == 'WishlistProduct') {
                this.commitPlan(routeName)
            } else if (routeName == 'RegularDelivery') {
                this.commitRegular(routeName)
            }
        },
        updateInputQuantity(e, biggestNumber) {
            if (this.quantity >= biggestNumber) {
                this.quantity = biggestNumber
            }
            let routeName = this.$route.name
            if (routeName == 'ShoppingCart') {
                this.commitShoppingCart(routeName)
            } else if (routeName == 'PlanProduct' || routeName == 'DiscountProduct' || routeName == 'WishlistProduct') {
                this.commitPlan(routeName)
            } else if (routeName == 'RegularDelivery') {
                this.commitRegular(routeName)
            }
        },
        commitShoppingCart(routeName) {
            this.item.quantityUpdate = this.quantity
            let obj = {
                item: this.item,
                routeName: routeName
            }
            this.$store.commit('updateCheckedItem', obj)
            if (!this.checkboxIds.includes(this.item.id)) {
                this.checkboxIds.push(this.item.id)
            }
            // check all items
            if (this.checkboxIds.length == this.countTotal) {
                this.$emit("checkAllStatus", true)
            } else {
                this.$emit("checkAllStatus", false)
            }
        },
        commitPlan(routeName) {
            this.item.quantityUpdate = this.quantity
            this.item.compareOptionId = this.compareOptionId
            let obj = {
                item: this.item,
                routeName: routeName
            }
            this.$store.commit('updateCheckedItem', obj)
            this.$store.commit('addIdsChecked', this.item.productId)
            if (!this.checkboxIds.includes(this.item.productId)) {
                this.checkboxIds.push(this.item.productId)
            }
            // check all items
            if (this.checkboxIds.length == this.countTotal) {
                this.$emit("checkAllStatus", true)
            } else {
                this.$emit("checkAllStatus", false)
            }
        },
        commitRegular(routeName) {
            // merge productId and optionId
            let idMerge = this.item.productId + "_" + this.item.optionId
            if (!this.checkboxIds.includes(idMerge)) {
                this.checkboxIds.push(idMerge)
            }
            this.item.quantityUpdate = this.quantity
            this.item.weekdayChecked = this.weekdayChecked
            this.item.idMerge = idMerge
            let obj = {
                item: this.item,
                routeName: routeName
            }
            this.$store.commit('updateCheckedItem', obj)
            this.$store.commit('addIdsChecked', idMerge)
            // check all items
            if (this.checkboxIds.length == this.countTotal) {
                this.$emit("checkAllStatus", true)
            } else {
                this.$emit("checkAllStatus", false)
            }
        }
    },
};
