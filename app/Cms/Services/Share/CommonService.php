<?php

declare(strict_types=1);

namespace Cms\Services\Share;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Сервис с общедоступными методами модуля CMS
 *
 * Class CommonService
 * @package Cms\Application\Services
 */
class CommonService
{
    public const CMS_PAGE_PREFIX_NEWS = 'news';
    public const CMS_PAGE_PREFIX_STATIC_PAGE = 's';

    /**
     * @param string $str
     * @param bool $includePostfix
     * @return string
     */
    public static function generateIriFromString(string $str, bool $includePostfix = true): string
    {
        $result = Str::slug($str);

        if ($includePostfix) {
            $result .= '-' . time();
        }

        return  $result;
    }

    /**
     * Возвращает абсолютный URL сущности по ее IRI
     *
     * @param string $type
     * @param string $iri
     * @return string
     */
    public static function generatePageUrlByIri(string $type, string $iri): string
    {
        switch ($type) {
            case 'news':
                $prefix = CommonService::CMS_PAGE_PREFIX_NEWS;
                break;
            default:
                $prefix = CommonService::CMS_PAGE_PREFIX_STATIC_PAGE; // static pages
                break;
        }

        return url('/' . $prefix . '/' . $iri);
    }

    /**
     * Удаляет null-значения из массива
     *
     * @param array $array
     * @return array
     */
    public function removeNullValuesFromArray(array $array)
    {
        return array_filter($array, [$this, 'removeNullItems']);
    }

    /**
     * @param mixed $item
     * @return array|bool
     */
    protected function removeNullItems($item)
    {
        if (is_array($item)) {
            return array_filter($item, [$this, 'removeNullItems']);
        }

        if (is_null($item)) {
            return false;
        }

        return true;
    }
}
