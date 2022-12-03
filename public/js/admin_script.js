$(document).ready(function () {
    $('.sl-parent-cate').change(function (e) {
        let depth = $(this).find(':selected').data('depth');
        $('input[name="category_depth"]').val(depth);
    });
});
