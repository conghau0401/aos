export const checkboxMixin = {
    data() {
        return {
            isCheckAll: false,
            checkboxIds: [],
        }
    },
    methods: {
        checkAll(listData, key = '') {
            this.isCheckAll = !this.isCheckAll
            this.checkboxIds = []
            if(this.isCheckAll) {
                for (var index in listData) {
                    this.checkboxIds.push(listData[index][key])
                }
            }
        },
        checkAllRegular(listData, key = '', optionId = '') {
            this.isCheckAll = !this.isCheckAll
            this.checkboxIds = []
            if(this.isCheckAll) {
                for (var index in listData) {
                    this.checkboxIds.push(listData[index][key] + "_" + listData[index][optionId])
                }
            }
        },
        toggleCheck(id, countTotal) {
            if (this.checkboxIds.includes(id)) {
                for(let i = 0; i < this.checkboxIds.length; i++) {
                    if (id == this.checkboxIds[i]) {
                        this.checkboxIds.splice(i, 1)
                    }
                }
            } else {
                this.checkboxIds.push(id)
            }
            if (this.checkboxIds.length == countTotal) {
                this.isCheckAll = true
            } else {
                this.isCheckAll = false
            }
        },
    },
}
