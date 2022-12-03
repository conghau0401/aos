export const groupByKeyMixin = {
    methods: {
        groupByKey(list, key) {
            return list.reduce(function(storage, item) {
                if (item[key] != null) {
                    (storage[item[key]] = storage[item[key]] || []).push(item);
                    return storage;
                } else {
                    return storage
                }
            }, {});
        },
    },
}
