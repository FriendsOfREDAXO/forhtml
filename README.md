# FORHtml

PHP HTML-Generator für REDAXO CMS

## Installation

Über das REDAXO-Backend installieren oder:

```bash
composer require friends-of-redaxo/for-html
```

## Grundlegende Verwendung

### HTML-Element erstellen

```php
$div = FORHtml::createElement('div');
echo $div; // Ausgabe: <div></div>

$paragraph = FORHtml::createElement('p')->text('Inhalt');
echo $paragraph; // Ausgabe: <p>Inhalt</p>
```

### Verschachtelte Elemente

```php
$container = FORHtml::createElement('div')
    ->addClass('container')
    ->addElement('a')
        ->set('href', './seite.php')
        ->text('Mein Link')
        ->getParent() // Zurück zum Container
    ->addElement('p')
        ->text('Mein Text');

// Ausgabe:
// <div class="container">
//     <a href="./seite.php">Mein Link</a>
//     <p>Mein Text</p>
// </div>
```

## Attribute verwalten

### Attribute setzen

```php
$link = FORHtml::createElement('a')
    ->set('href', './beispiel.php')
    ->set('id', 'meinLink')
    ->text('Mein Link');

// Alternativ mehrere Attribute gleichzeitig:
$link = FORHtml::createElement('a')
    ->set([
        'href' => './beispiel.php',
        'id' => 'meinLink',
        'title' => 'Mehr Information'
    ])
    ->text('Mein Link');
```

### ID setzen (Kurzform)

```php
$div = FORHtml::createElement('div')
    ->id('meinContainer');
```

### CSS-Klassen verwalten

```php
$div = FORHtml::createElement('div')
    ->addClass('primär')
    ->addClass('hervorgehoben');
// Ausgabe: <div class="primär hervorgehoben"></div>

$div->removeClass('hervorgehoben');
// Ausgabe: <div class="primär"></div>
```

## REDAXO-spezifische Funktionen

### Media Manager Integration

```php
$bild = FORHtml::createElement('img')
    ->addClass('uk-width-1-1')
    ->set('alt', 'Produktbild')
    ->mmfile('thumbnail', 'produkt.jpg');

// Ausgabe: 
// <img class="uk-width-1-1" alt="Produktbild" 
//      src="index.php?rex_media_type=thumbnail&rex_media_file=produkt.jpg">
```

### Fragment-System

```php
$card = FORHtml::createElement('div')
    ->addClass('card')
    ->parseFragment('/fragments/card.php', [
        'title' => 'Überschrift',
        'text' => 'Beschreibung',
        'image' => 'bild.jpg'
    ]);
```

## Sicherheit und Konfiguration

### XSS-Schutz aktivieren

```php
FORHtml::$avoidXSS = true;

$div = FORHtml::createElement('div')
    ->text('<script>alert("XSS")</script>');
// Text wird automatisch escaped
```

### Ausgabeformat konfigurieren

```php
// HTML5 (Standard)
FORHtml::$outputLanguage = ENT_HTML5;

// XML/XHTML
FORHtml::$outputLanguage = ENT_XML1;
```

## Element-Navigation

```php
$container = FORHtml::createElement('div');
$first = $container->addElement('p')->text('Erster');
$second = $container->addElement('p')->text('Zweiter');
$third = $container->addElement('p')->text('Dritter');

// Navigation
$element = $second
    ->getFirst();     // Ersten Absatz holen
    ->getNext();      // Nächsten Absatz
    ->getPrevious();  // Vorherigen Absatz
    ->getLast();      // Letzten Absatz
    ->getParent();    // Container
    ->getTop();       // Wurzelelement

// Element entfernen
$second->remove();
```

## Praktische Beispiele

### UIkit-Cards aus YForm-Daten

```php
$tableUrlaubsziele = rex_yform_manager_table::get('rex_urlaubsziele');
$urlaubsziele = $tableUrlaubsziele->query()->find();

foreach ($urlaubsziele as $ziel) {
    $mediaList = isset($ziel->media) ? array_filter(explode(",", $ziel->media)) : [];
    
    if (!empty($mediaList)) {
        $media = FORHtml::createElement('img')
            ->addClass('uk-width-1-1')
            ->set('alt', 'Bild: ' . $ziel->title)
            ->set('uk-tooltip', '')
            ->mmfile('card_image', $mediaList[0]);
    }

    $card = FORHtml::createElement('div')
        ->parseFragment('/uk3/card.php', [
            'media' => $media ?? '',
            'title' => $ziel->title,
            'body' => $ziel->infotext,
        ])
        ->addClass('uk-card-default');

    $cards[] = $card;
}

// Container für alle Cards
if ($cards) {
    echo FORHtml::createElement('div')
        ->addClass('uk-section uk-padding-large')
        ->addElement('div')
            ->addClass('uk-container')
            ->addElement('div')
                ->addClass('uk-child-width-1-3@m uk-grid-match')
                ->set('uk-grid', '')
                ->text(implode('', $cards));
}
```

### Navigation mit NavigationArray

```php
function generateUikit3Navigation(int $startCategoryId = 0, int $depth = 4): string
{
    $navArray = BuildArray::create()
        ->setStart($startCategoryId)
        ->setDepth($depth)
        ->generate();

    $nav = FORHtml::createElement('ul')
        ->addClass('uk-nav uk-nav-default');

    foreach ($navArray as $item) {
        $li = $nav->addElement('li');
        
        if ($item['active']) {
            $li->addClass('uk-active');
        }

        $li->addElement('a')
            ->set('href', $item['url'])
            ->text($item['catName']);

        if (!empty($item['children'])) {
            $li->addElement(generateSubMenu($item['children']));
        }
    }

    return $nav->toString();
}

function generateSubMenu(array $children): FORHtml
{
    $subNav = FORHtml::createElement('ul')
        ->addClass('uk-nav-sub');

    foreach ($children as $child) {
        $li = $subNav->addElement('li');
        
        if ($child['active']) {
            $li->addClass('uk-active');
        }

        $li->addElement('a')
            ->set('href', $child['url'])
            ->text($child['catName']);

        if (!empty($child['children'])) {
            $li->addElement(generateSubMenu($child['children']));
        }
    }

    return $subNav;
}
```

## Technische Details

### Selbstschließende Tags

Folgende Tags werden automatisch als selbstschließend behandelt:
- img, br, hr, input, area, link, meta
- param, base, col, command, keygen
- source, track, wbr

```php
$img = FORHtml::createElement('img')
    ->set('src', 'bild.jpg');
// Ausgabe: <img src="bild.jpg"/>
```

---

## Credits

Basierend auf [PHP HTML Generator](https://github.com/Airmanbzh/php-html-generator) von Airmanbzh.

## Support

Bei Fragen oder Problemen bitte ein [GitHub Issue](https://github.com/FriendsOfREDAXO/for-html/issues) erstellen.
