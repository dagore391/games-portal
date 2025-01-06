<?php
namespace app\data;

use app\config\Urls;

class Pagination {
    public static function htmlPagination(array $dataList, int $currentPage, int $totalResults, UrlsEnum $urlName, int $elementsPerPage, array $extraParam = []): string {
        // Se calcula el número de páginas que hay sobre el máximo de resultados.
        $totalPages = ceil($totalResults / $elementsPerPage);
        $totalPages = $totalPages > 0 ? $totalPages : 1;
        $pagination = Pagination::_getPagination($dataList, $currentPage, $totalResults, $totalPages);
        $result = "";
        // Se prepara la paginación.
        $result .= "<div class=\"pagination\">";
        if ($pagination['PREVIOUS']) {
            $result .= self::getPaginationLink($urlName, $extraParam !== null ? $extraParam : [], $pagination['CURRENT'] - 1, '&#11207;');
        }

        foreach ($pagination['PAGES'] as $page) {
            if ($page === $pagination['CURRENT']) {
                $result .= "<label class=\"pagination-current-link\">" . $pagination['CURRENT'] . " de " . $totalPages . "</label>";
            } else {
                $result .= self::getPaginationLink($urlName, $extraParam !== null ? $extraParam : [], $page, $page);
            }
        }

        if ($pagination['NEXT']) {
            $result .= self::getPaginationLink($urlName, $extraParam !== null ? $extraParam : [], $pagination['CURRENT'] + 1, '&#11208;');
        }
        return "{$result}</div>";
    }

    private static function getPaginationLink(UrlsEnum $url, array $extraParam = [], int $page, string $label) : string {
        return "<a class=\"pagination-link\" href=\"" . Urls::getUrl($url, array_merge($extraParam, [$page])) . "\">{$label}</a>";
    }

    private static function _getPagination(array $dataList, int $currentPage, int $totalResults, int $totalPages): array {
        $pagination = [
            "NEXT" => false,
            "PREVIOUS" => false,
            "CURRENT" => $currentPage,
            "PAGES" => []
        ];
        // Se guardan sólo el número de las páginas que se desea guardar.
        if ($currentPage > 1) {
            $pagination['PREVIOUS'] = true;
        }
        if ($currentPage < $totalPages) {
            $pagination['NEXT'] = true;
        }
        $pages = [];
        for ($idx = $currentPage; $idx > $currentPage - 3 && $idx > 0; $idx --) {
            array_push($pages, $idx);
        }
        for ($idx = $currentPage + 1; $idx < $currentPage + 3 && $idx <= $totalPages; $idx ++) {
            array_push($pages, $idx);
        }
        asort($pages);
        $pagination['PAGES'] = $pages;
        return $pagination;
    }
}
