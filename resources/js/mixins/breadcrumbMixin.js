export const breadcrumbMixin = {
    methods: {
        breadcrumb(product) {
            let breadcrumb = []
            breadcrumb.push({
                name: this.$t("category.home"),
                url: "/",
                code: 0,
                level: 0,
            })
            if (product.productTpName != null) {
                breadcrumb.push({
                    name: product.productTpName,
                    url: "/category/" + product.productTp,
                    code: product.productTp,
                    level: 1,
                })
            }
            if (product.largeCategoryName != null) {
                breadcrumb.push({
                    name: product.largeCategoryName,
                    url: "/category/" + product.largeCategory,
                    code: product.largeCategory,
                    level: 2,
                })
            }
            if (product.mediumCategoryName != null) {
                breadcrumb.push({
                    name: product.mediumCategoryName,
                    url: "/category/" + product.mediumCategory,
                    code: product.mediumCategory,
                    level: 3,
                })
            }
            if (product.smallCategoryName != null) {
                breadcrumb.push({
                    name: product.smallCategoryName,
                    url: "/category/" + product.smallCategory,
                    code: product.smallCategory,
                    level: 4,
                })
            }
            return breadcrumb
        },
    },
}
