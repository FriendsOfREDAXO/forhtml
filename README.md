# FORHtml

PHP-HTML-Generator und Fragmentsammlung für REDAXO cms

Einfache Fragmente zum sofortigen Einsatz. 

Geplant: 

- Fragmente für UiKit
- Fragmente für Bootstrap
- Fragmente für Tailwind


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


## Beispiel Uikit-Cards in Section und Container aus YForm

```php
$table_urlaubsziele = rex_yform_manager_table::get('rex_urlaubsziele');
$query = $table_urlaubsziele->query();
$urlaubsziele = $query->find();
$cards = [];
// Card erzeugen
foreach ($urlaubsziele as $ziel) {
    $media = [];
    if (isset($ziel->media)) {
        $media = array_filter(explode(",", $ziel->media));
    }
    $fragment = new rex_fragment();
    $fragment->setVar('help', false);
    $medium = '';
    if ($media) {
        // Erstelle Medium mit FORHtml mmfile holt ein mediamanager bild
        $medium = FORHtml::createElement('img')
             ->addClass('uk-width-1-1')
             ->set('alt', 'Bild zum' . $ziel->title)
             ->set('title', 'Bild zum' . $ziel->title)
             ->set('uk-tooltip ', '')
             ->mmfile('card_image', $media[0]); 
        $fragment->setVar('media', $medium, false);
        $fragment->setVar('media_bottom', true, false);
        $medium = '';
    }
    $attributes_main = [];
    $attributes_main = ['class' => 'uk-card-default'];
    $fragment->setVar('main_attributes', $attributes_main, false);
    $fragment->setVar('title', $ziel->title, false);
    $fragment->setVar('body', $ziel->infotext, false);
    $cards[] = FORHtml::createElement('div')
        ->text($fragment->parse('/uk3/card.php'))
        ->addClass('wrapper uk-background-secondary');
}
// Sind Cards da?
if ($cards) {
    // Tag hinzufügen mittels FORHtml

$output_cards = FORHtml::createElement('div')
    ->text(implode($cards))
    ->addClass('uk-child-width-1-3@m uk-grid-match')
    ->set('uk-grid', '');
   
    
$output = FORHtml::createElement('div')
    ->addClass('uk-section uk-preserve-color uk-padding-large')
        ->addElement('div')
        ->addClass('uk-container uk-container-large')
        ->body($output_cards);
echo $output;    
}

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
