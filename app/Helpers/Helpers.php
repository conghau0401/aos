<?php

if (!function_exists('buildTree')) {
    /**
     * build tree category function
     *
     * @param array $data
     * @param integer $parent
     * @return void
     */
    function buildTree($data, $parent = 1)
    {
        $tree = array();
        foreach ($data as $d) {
            if ($d->parent_category_no == $parent) {
                $children = buildTree($data, $d->category_no);
                // set a trivial key
                $d->_children = [];
                if (!empty($children)) {
                    $d->_children = $children;
                }
                $tree[] = $d;
            }
        }
        return $tree;
    }
}

if (!function_exists('buildTreeMenu')) {
    /**
     * build tree category function
     *
     * @param array $data
     * @param string $parent
     * @return void
     */
    function buildTreeMenu($data, $parent = null)
    {
        $tree = array();
        try {
            foreach ($data as $d) {
                if ((!empty($d->parent_category) && !empty($d->category)) && $d->parent_category == $parent) {
                    $children = buildTreeMenu($data, $d->category);
                    // set a trivial key
                    $d->sub_menu = [];
                    if (!empty($children)) {
                        $d->sub_menu = $children;
                    }
                    $tree[] = $d;
                }
            }
        } catch (\Throwable $th) {
            return $th;
        }

        return $tree;
    }
}

if (!function_exists('buildOptions')) {
    /**
     * build options category function
     *
     * @param array $tree
     * @param string $split
     * @param array $parentId
     * @return void
     */
    function buildOptions($tree, $split = '=', $parentId = [])
    {
        $options = '';
        foreach ($tree as $t) {
            $selected = '';
            if ($t->parent_category_no == 1) {
                $split = '';
            } else {
                $split .= '=';
            }
            if (in_array($t->category_no, $parentId)) {
                $selected = 'selected';
            }
            $options .= '<option data-depth="' . $t->category_depth . '" value="' . $t->category_no . '" ' . $selected . '>' . $split . $t->category_name . '</option>';
            if (!empty($t->_children)) {
                $options .= buildOptions($t->_children, $split, $parentId);
            }
        }
        return $options;
    }
}

if (!function_exists('paginateAPI')) {
    /**
     * Custom pagination from API function
     *
     * @param int $totalRows
     * @param int $perPage
     * @param int $currentPage
     * @param string $pageUrl
     * @param array $appends
     * @return void
     */
    function paginateAPI($totalRows, $perPage, $currentPage, $pageUrl, $appends = [])
    {
        $params = '';
        if (!empty($appends) && is_array($appends)) {
            foreach ($appends as $key => $item) {
                $params .= '&' . $key . '=' . $item;
            }
        }
        $pagination = '';
        $totalPages = ceil($totalRows / $perPage);
        if ($totalPages > 0 && $totalPages != 1 && $currentPage <= $totalPages) { //verify total pages and current page number
            $pagination .= '<ul class="pagination justify-content-center">';

            $rightLinks = $currentPage + 3;
            $previous = $currentPage - 3; //previous link
            $firstLink = true; //boolean var to decide our first link

            if ($currentPage > 1) {
                $previousLink = ($previous <= 0) ? 1 : $previous;
                $pagination .= '<li class="first"><a class="page-link" href="' . $pageUrl . '?page=1' . $params . '" title="First">«</a></li>'; //first link
                $pagination .= '<li><a class="page-link" href="' . $pageUrl . '?page=' . $previousLink . $params . '" title="Previous"><</a></li>'; //previous link
                for ($i = ($currentPage - 2); $i < $currentPage; $i++) { //Create left-hand side links
                    if ($i > 0) {
                        $pagination .= '<li><a class="page-link" href="' . $pageUrl . '?page=' . $i . $params . '">' . $i . '</a></li>';
                    }
                }
                $firstLink = false; //set first link to false
            }

            if ($firstLink) { //if current active page is first link
                $pagination .= '<li class="page-item first active"><a class="page-link">' . $currentPage . '</a></li>';
            } elseif ($currentPage == $totalPages) { //if it's the last active link
                $pagination .= '<li class="page-item last active"><a class="page-link">' . $currentPage . '</a></li>';
            } else { //regular current link
                $pagination .= '<li class="page-item active"><a class="page-link">' . $currentPage . '</a></li>';
            }

            for ($i = $currentPage + 1; $i < $rightLinks; $i++) { //create right-hand side links
                if ($i <= $totalPages) {
                    $pagination .= '<li><a class="page-link" href="' . $pageUrl . '?page=' . $i . $params . '">' . $i . '</a></li>';
                }
            }
            if ($currentPage < $totalPages) {
                $next_link = ($i > $totalPages) ? $totalPages : $i;
                $pagination .= '<li><a class="page-link" href="' . $pageUrl . '?page=' . $next_link . $params . '" >></a></li>'; //next link
                $pagination .= '<li class="last"><a class="page-link" href="' . $pageUrl . '?page=' . $totalPages . $params . '" title="Last">»</a></li>'; //last link
            }

            $pagination .= '</ul>';
        }
        return $pagination; //return pagination links
    }
}

if (!function_exists('jEncode')) {
    /**
     * json encode unicode
     *
     * @param array $array
     * @return json
     */
    function jEncode($array)
    {
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('formatPhoneNumber')) {
    function formatPhoneNumber($phoneNumber)
    {
        $phone = str_replace(['+', '(', ')'], '', trim($phoneNumber));
        return $phone;
    }
}

if (!function_exists('uniqueItemCart')) {
    function uniqueItemCart($item)
    {
        static $idList = [];
        $strId = $item['ds_product_id'] . '_' . $item['ds_option_id'] . '_' . $item['shipping_method'];
        if (in_array($strId, $idList)) {
            return false;
        }
        $idList[] = $strId;
        return true;
    }
}
