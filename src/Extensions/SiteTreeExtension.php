<?php

namespace AndrewHaine\SiteTreeInheritanceHelpers\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Config\Config;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\CMS\Controllers\RootURLController;

/**
 * Adds functionality to search 'up' the sitetree for
 * a desired value or relation
 */
class SiteTreeExtension extends Extension
{
    /**
     * Method to inherit a DB field from
     * the parents
     *
     * @param string $fieldName The desired field name
     *
     * @return mixed|false The value
     */
    public function getInheritedDBValue($fieldName)
    {

        if ( ! is_string($fieldName) ) return false;

        $page = $this->owner;
        $result = null;

        // First query the current page
        $result = $page->hasField($fieldName) ? $page->{$fieldName} : false;

        // Search the parent pages for a value
        if( !$result ) {
            while($page = $page->Parent) {
                $result = $page->hasField($fieldName) ? $page->{$fieldName} : false;
                if( $result ) break;
            }
        }

        // .. if all else fails search the homepage
        if( !$result ) {
            $home = $this->get_homepage();
            $result = $home->hasField($fieldName) ? $home->{$fieldName} : false;
        }

        return $result;
    }

    /**
     * Method to inherit a relation from
     * the parent - use this for has_one, has_many or many_many
     *
     * @param string $relationName
     * @param boolean $isList Set to true to check a list rather than a single object
     *
     * @return mixed|false The returned relation - will probably be a list of some kind
     */
    public function getInheritedRelationValue($relationName, $isList = false)
    {

        if ( ! is_string($relationName) ) return false;

        $page = $this->owner;
        $result = null;

        // First query the current page
		$result = $isList ? self::check_list_method($page, $relationName) : self::check_relation_method($page, $relationName);

        // Search the parent pages for a value
        if( !$result ) {
            while($page = $page->Parent) {
                $result = $isList ? self::check_list_method($page, $relationName) : self::check_relation_method($page, $relationName);
                if( $result ) break;
            }
        }

        // .. if all else fails search the homepage
        if( !$result ) {
            $home = $this->get_homepage();
            $result = $isList ? self::check_list_method($home, $relationName) : self::check_relation_method($home, $relationName);
        }

        return $result;
    }

    /**
     * Check that a related object is present on the
     * current page and that said object exists - use this for single object relations
     *
     * @param SiteTree $page Page object to check the method on
     * @param string $relationName The name of the method which returns a list
     *
     * @return mixed The found object
     */
    private static function check_relation_method($page, $relationName)
    {
        if( $page->hasMethod($relationName) && is_callable([$page, $relationName])   ) {
            $object = $page->{$relationName}();
            if( $object && $object->exists() ) {
                return $list;
            }
        }

        return $false;
    }

    /**
     * Check that a method which should return a list exists
     * and that the result is valid
     *
     * @param SiteTree $page Page object to check the method on
     * @param string $relationName The name of the method which returns a list
     *
     * @return mixed The list - will return false if anything is wrong
     */
    private static function check_list_method($page, $relationName)
    {
        if( $page->hasMethod($relationName) is_callable([$page, $relationName]) ) {
            $list = $page->{$relationName}();
            if( $list && $list->count() ) {
                return $list;
            }
        }

        return false;
    }

    /**
     * Returns the homepage - will usually
     * return the page with the URLSegment 'home'
     *
     * @return SiteTree
     */
    private function get_homepage()
    {
        $homeUrl = RootURLController::get_homepage_link();
        $home = SiteTree::get()->filter('URLSegment', $homeUrl)->first();
        return ( $home && $home->exists() ) ? $home : false;
    }
}
