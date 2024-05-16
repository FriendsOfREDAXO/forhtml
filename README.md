# ForHtml

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
                 ->set('title', 'Bild zum' . $ziel->title)
                 ->set('uk-tooltip ', '')
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
