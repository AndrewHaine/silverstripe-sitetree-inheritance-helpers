# SilverStripe SiteTree Inheritance Helpers

A module which adds some additional methods to SiteTree to make it easier to search "up" the page tree for values or relations to inherit. Please note this module is only compatible with SilverStripe 4.1+.

## The problem
Are you tired of writing methods or template loops to retrieve database values or relations from parent pages? Well this is just the module for you!

## Introducing the helper methods
Currently this module adds two methods to help with inherited values. These can be used within your Page's php class _or_ from within a template file.

### Getting database field values
If you just need a database value you can use ```getInheritedDBValue```

#### Examples
**CustomPage.php**
```php
public function getMyDataBaseValue()
{
    return $this->getInheritedDBValue('FieldName');
}
```

**CustomPage.ss**
```html
<span>{$getInheritedDBValue('FieldName')}</span>
```

### Getting relation values
If you need to get a relation from a parent page, for example a ```$has_one``` or a ```$many_many``` you can use ```getInheritedRelationValue```. This method has an additional parameter which will need to be set to true when the relation is a list.

#### Examples
**CustomPage.php**
```php
public function getMyInheritedList()
{
    return $this->getInheritedRelationValue('SomeList', true);
}
```

**CustomPage.ss**
```html
<% loop $getInheritedRelationValue('SomeList', true) %>
    \\\
<% end_loop %>
```

## Issues & Contributing
This was made quite quickly, any issue reports or enhancements are welcome :) - See you in the issues section
