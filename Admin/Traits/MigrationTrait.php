<?php

namespace Nz\MigrationBundle\Admin\Traits;

trait MigrationTrait
{

    protected function processContent($content)
    {
        $allowed_tags = '<iframe><img><a>';
        return strip_tags($content, $allowed_tags);
    }

    /**
     * Filter wp metas and remove system only metas
     */
    protected function filterMetas(array $array_metas)
    {
        $metas = [];
        foreach ($array_metas as $meta) {
            if (!isset($metas[$meta->getKey()])) {
                $metas[$meta->getKey()] = $meta->getValue();
            } else {
                $metas[$meta->getKey() . uniqid('_duplicate_meta')] = $meta->getValue();
            }
        }

        return $this->filterArray($metas, $this->metaKeysToFilterRegex());
    }

    /**
     *  Filter array of content against strings and regexes
     * 
     *  @return array New content filtered 
     */
    private function filterArray(array $array, array $filters)
    {

        $new_content = [];
        foreach ($array as $key => $value) {

            if ($this->match($key, $filters)) {
                continue;
            }

            $new_content[$key] = $value;
        }

        return $new_content;
    }

    /**
     *  Match string against array of regexes
     * 
     *  @return boolean Return true if string matches any of array regexes false otherwise
     */
    private function match($subject, array $arr)
    {
        foreach ($arr as $pattern) {
            if (preg_match($pattern, $subject)) {
                return true;
            }
        }

        return false;
    }

    private function metaKeysToFilterRegex()
    {
        return [
            '/^_yoast/',
            '/^_edit_lock/',
            '/^_edit_last/',
            '/^_gform-entry-id/',
            '/^_gform-form-id/',
            '/^_wp_attached_file/',
        ];
    }
}
