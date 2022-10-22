<?php

/**
 * @var rex_fragment $this
 * @psalm-scope-this rex_fragment
 */

// Deklaration der Variablen
$media = $style = $helpout = '';
$media_bottom  = false; 

if (isset($this->help) && $this->help === true) {
    $help = [];
    $help['media']        = 'Nimmt ein Medium an (String)';
    $help['media_bottom'] = 'Definiert ob das Medium am Ende dargestellt werden soll (bool)';
    $help['title']        = 'Titel (String)';
    $help['body']         = 'Body (String)';
    $help['body_prepend'] = 'vor Body (String)';
    $help['body_append']  = 'nach Body (String)';
    $help['footer']       = 'Footer (String)';
    $help['style']        = 'Hier können css-classes zur uk-card ergänzt werden(string) ';
    $helpout = dump($help);
}

// check if media and position are set
if (isset($this->media) && $this->media != '') {  
    $media  = '<div class="uk-card-media-top">' . $this->media . '</div>';
    if (isset($this->media_bottom) && $this->media_bottom === true)
    {
    $media = '';    
    $media_bottom  = '<div class="uk-card-media-bottom">' . $this->media . '</div>';   
    }
    
}
?>

<div>
    <div class="uk-card <?= (isset($this->style) ? $this->style : '')?>">
        <?= $media ?>
        <?php if (isset($this->title) && $this->title != '') : ?>
            <div class="uk-card-header">
                <h3 class="uk-card-title">
                    <?= $this->title ?></h3>
            </div>
        <?php endif; ?>
        <div class="uk-card-body">
            <?php if (isset($this->body_prepend) && $this->body_prepend != '') : ?>
                <?= $this->body_prepend ?>
            <?php endif; ?>
            <?php if (isset($this->body) && $this->body != '') : ?>
                <?= $this->body ?>
            <?php endif; ?>
            <?php if (isset($this->body_append) && $this->body_append != '') : ?>
                <?= $this->body_append ?>
            <?php endif; ?>
        </div>
        <?php if (isset($this->footer) && $this->footer != '') : ?>
            <div class="uk-card-footer">
                <a href="#"><?= $this->footer ?></a>
            </div>
        <?php endif; ?>
        <?= $media_bottom ?>
    </div>
</div>
