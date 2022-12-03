export const format = {
    price (value) {
        let result =  Math.round(value) || 0
        return result.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    },
    timeToStr(time, separate = '.') {
        if (time != null) {
            let date = new Date(time)
            let strDate = date.toISOString().substr(0, 10)
            return strDate.replace(/-/g, separate);
        }
    },
    imageUrl(imagePath) {
        if (imagePath == null || imagePath =='') {
            return '/img/product/no-thumbnail.png'
        } else {
            return imagePath
        }
    }
};
