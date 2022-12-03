export const addToWishlist = {
    methods: {
        async addToWishlist(product, status) {
            let params = {};
            if (status == true) {
                let confirm = await this.$refs.confirmDialogue.show({
                    title: this.$t('modal.titleConfirm'),
                    message: this.$t('modal.confirmDelete'),
                    okButton: this.$t('modal.btnYes'),
                    cancelButton: this.$t('modal.btnCancel'),
                })
                if (confirm == true) {
                    this.isWishlist = false
                    params = {
                        "productDeleteIds": [
                            product.productId,
                        ]
                    }
                    this.$refs.confirmDialogue.show({
                        title: this.$t('modal.titleDialog'),
                        message: this.$t('modal.deleteSuccessWishlist'),
                        cancelButton: this.$t('modal.btnOk'),
                        isDialog: true,
                    })
                } else {
                    return
                }
            } else {
                this.isWishlist = true
                params = {
                    "productIds": [
                        product.productId,
                    ]
                }
                this.$refs.confirmDialogue.show({
                    title: this.$t('modal.titleDialog'),
                    message: this.$t('modal.addSuccessWishlist'),
                    cancelButton: this.$t('modal.btnOk'),
                    isDialog: true,
                })
            }
            this.loading = true
            axios
                .put(apiURL + "/product/add-wishlist", params)
                .then(res => {
                    this.loading = false
                    console.log(res)
                })
        }
    }
}
