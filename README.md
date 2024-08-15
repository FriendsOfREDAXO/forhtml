# FORHtml

PHP-HTML-Generator für REDAXO cms

## Beispiel HTML erstellen

```php
$medium = FORHtml::createElement('div')
          ->addClass('border big')
             ->addElement('img')
                ->addClass('uk-width-1-1')
                 ->set('alt', 'Bild zum' . $ziel->title)
                 ->set('uk-tooltip', '')
                 ->mmfile('mediamanager_type', 'image.jpg'); 
echo $medium; 
```


## Beispiel Uikit-Cards fragment in Section und Container aus YForm

```php
$tableUrlaubsziele = rex_yform_manager_table::get('rex_urlaubsziele');
$urlaubsziele = $tableUrlaubsziele->query()->find();
$cards = [];

foreach ($urlaubsziele as $ziel) {
    $mediaList = isset($ziel->media) ? array_filter(explode(",", $ziel->media)) : [];
    $media = '';

    if (!empty($mediaList)) {
        $media = FORHtml::createElement('img')
            ->addClass('uk-width-1-1')
            ->set('alt', 'Bild zum ' . $ziel->title)
            ->set('title', 'Bild zum ' . $ziel->title)
            ->set('uk-tooltip', '')
            ->mmfile('card_image', $mediaList[0])
            ->toString();
    }

    $fragmentVars = [
        'help' => false,
        'media' => $media,
        'media_bottom' => !empty($media),
        'main_attributes' => ['class' => 'uk-card-default'],
        'title' => $ziel->title,
        'body' => $ziel->infotext,
    ];

    $cards[] = FORHtml::createElement('div')
        ->parseFragment('/uk3/card.php', $fragmentVars)
        ->addClass('wrapper uk-background-secondary');
}

if ($cards) {
    $outputCards = FORHtml::createElement('div')
        ->text(implode('', $cards))
        ->addClass('uk-child-width-1-3@m uk-grid-match')
        ->set('uk-grid', '');
    
    $output = FORHtml::createElement('div')
        ->addClass('uk-section uk-preserve-color uk-padding-large')
        ->addElement('div')
        ->addClass('uk-container uk-container-large')
        ->body($outputCards);

    echo $output;
}
```

### Beispiel Navigation mit navigation_array

```php
use FriendsOfRedaxo\FORHtml\FORHtml;
use FriendsOfRedaxo\NavigationArray\BuildArray;

function generateUikit3Navigation(int $startCategoryId = -1, int $depth = 4, bool $ignoreOfflines = true): string
{
    // Erstelle das Navigationsarray
    $navigationBuilder = BuildArray::create()
        ->setStart($startCategoryId)
        ->setDepth($depth)
        ->setIgnore($ignoreOfflines);

    $navigationArray = $navigationBuilder->generate();

    // Erstelle das HTML für die Navigation
    $navHtml = FORHtml::createElement('ul')->addClass('uk-nav uk-nav-default');

    foreach ($navigationArray as $navItem) {
        $li = FORHtml::createElement('li');

        // Überprüfen, ob das Element aktiv oder das aktuelle Element ist
        if ($navItem['active']) {
            $li->addClass('uk-active');
        }

        // Link für das Navigationselement
        $a = FORHtml::createElement('a')
            ->attr('href', $navItem['url'])
            ->text($navItem['catName']);

        $li->addElement($a);

        // Prüfe, ob das Element Unterkategorien hat
        if (!empty($navItem['children'])) {
            $li->addElement(generateSubMenu($navItem['children']));
        }

        $navHtml->addElement($li);
    }

    return $navHtml->toString();
}

function generateSubMenu(array $children): FORHtml
{
    $subMenu = FORHtml::createElement('ul')->addClass('uk-nav-sub');

    foreach ($children as $child) {
        $li = FORHtml::createElement('li');

        if ($child['active']) {
            $li->addClass('uk-active');
        }

        $a = FORHtml::createElement('a')
            ->attr('href', $child['url'])
            ->text($child['catName']);

        $li->addElement($a);

        if (!empty($child['children'])) {
            $li->addElement(generateSubMenu($child['children']));
        }

        $subMenu->addElement($li);
    }

    return $subMenu;
}

// Beispielnutzung:
echo generateUikit3Navigation();

```



## based on: PHP HTML GENERATOR

Create HTML tags and render them efficiently.

by: [https://github.com/Airmanbzh/](https://github.com/Airmanbzh)

## Overview

```php
return FORHtml::createElement();
// returns an empty HtmlTag Container
```
```php
return FORHtml::createElement('a');
// returns an HtmlTag containing a 'a' tag
```

### Why you should use it

 - it always generates valid HTML and XHTML code
 - it makes templates cleaner
 - it's easy to use and fast to execute

## Render tags

```php
echo(FORHtml::createElement('a'));
```
or 
```php
$tag = FORHtml::createElement('a')
echo( $tag );
```

### Simple tags


```php
echo FORHtml::createElement('div');
```
```html
<div></div>
```

```php
echo(FORHtml::createElement('p')->text('some content'));
```
```html
<p>some content</p>
```

### Structured tags

```php
echo(FORHtml::createElement('div')->addElement('a')->text('a text'));
```
```html
<div><a>a text</a></div>
```

```php
$container = FORHtml::createElement('div');
$container->addElement('p')->text('a text');
$container->addElement('a')->text('a link');
```
```html
<div><p>a text</p><a>a link</a></div>
```
### Attributes

#### Classics attributes (method : 'set')

```php
$tag = FORHtml::createElement('a')
    ->set('href','./sample.php')
    ->set('id','myID')
    ->text('my link');
echo( $tag );
```
```html
<a href='./sample.php' id='myID'>my link</a>
```
	
#### Shortcut to set an ID attribute (method : 'id')

```php
$tag = FORHtml::createElement('div')
    ->id('myID');
echo( $tag );
```
```html
<div id='myID'>my link</div>
```

#### Class management (method : 'addClass'/'removeClass')

```php
$tag = FORHtml::createElement('div')
    ->addClass('oneClass')
    ->text('my content')
echo( $tag );
```
```html
<div class="oneClass">my content</div>
```

```php
$tag = FORHtml::createElement('div')
    ->addClass('aClass')
    ->addClass('anothereClass')
    ->text('my content')
echo( $tag );
```
```html
<div class="aClass anothereClass">my content</div>
```

```php
$tag = FORHtml::createElement('div')
    ->addClass('firstClass')
    ->addClass('secondClass')
    ->text('my content')
    ->removeClass('firstClass');
echo( $tag );
```
```html
<div class="secondClass">my content</div>
```
	
### More

Text and content are generated according to the order of addition
```php
$tag = FORHtml::createElement('p')
    ->text('a text')
    ->addElement('a')
    ->text('a link');
```
```html
<p>ma text<a>a link</a></p>
```
	
To generate content before text, 2 solutions :
```php
$tag = FORHtml::createElement('p')
    ->addElement('a')
    ->text('a link')
    ->getParent()
    ->text('a text');
```
or
```php
$tag = FORHtml::createElement('p');
$tag->addElement('a')->text('a link');
$tag->text('a text');
```

```html
<p><a>a link</a>a text</p>
```
