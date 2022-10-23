# fe_fragments 
Fragmentsammlung für das Frontend in REDAXO

Einfache Fragmente zum sofortigen Einsatz. 

Geplant: 

- Fragmente für UiKit
- Fragmente für Bootstrap
- Fragmente für Tailwind

## Beispiel Uikit-Cards in Section und Container aus YForm

```php
<?php
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
    if (count($media) >= 1) {
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
if (count($cards >= 1)) {
    // Tag hinzufügen mittels FORHtml
    $output_cards = FORHtml::createElement('div')
        ->text(implode($cards))
        ->addClass('uk-child-width-1-3@m uk-grid-match')
        ->set('uk-grid', '');

    // Übergabe an Container
    $container = '';
    $fragment = new rex_fragment();
    $attributes = [];
    $attributes = ['class' => 'uk-container-large '];
    $fragment->setVar('attributes', $attributes, true);
    $fragment->setVar('body', $output_cards, false);
    $container = $fragment->parse('/uk3/container.php');

    // Sektion erstellen
    $section = '';
    $fragment = new rex_fragment();
    $attributes = [];
    $attributes = ['class' => 'uk-preserve-color uk-padding-large'];
    $fragment->setVar('attributes', $attributes, true);
    $fragment->setVar('body', $container, false);
    $section = $fragment->parse('/uk3/section.php');
    echo $section;
}

```
