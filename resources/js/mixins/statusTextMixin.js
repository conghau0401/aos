export const statusTextMixin = {
    methods: {
        statusText(status) {
            if (status == 'Cancellation Completed') {
                return this.$t('status.cancellationCompleted')
            } else if (status == 'Payment Completed') {
                return this.$t('status.paymentCompleted')
            } else if (status == 'Product Being Prepared') {
                return this.$t('status.productBeingPrepared')
            } else if (status == 'Preparing for Delivery') {
                return this.$t('status.preparing')
            } else if (status == 'Shipping') {
                return this.$t('status.shipping')
            } else if (status == 'Completed' || status == 'Delivery Completed') {
                return this.$t('status.completed')
            } else {
                return status
            }
        },
        statusDelivery(status) {
            if (status == 'C40') {
                return this.$t('status.cancellationCompleted')
            } else if (status == 'N10') {
                return this.$t('status.productBeingPrepared')
            } else if (status == 'N20') {
                return this.$t('status.preparing')
            } else if (status == 'N30') {
                return this.$t('status.shipping')
            } else if (status == 'N40') {
                return this.$t('status.completed')
            } else {
                return status
            }
        },
    },
}
