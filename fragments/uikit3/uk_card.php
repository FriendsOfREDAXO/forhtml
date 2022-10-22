<?php

/**
 * @var rex_fragment $this
 * @psalm-scope-this rex_fragment
 */

// Deklaration der Variablen
$media = $attributes_code = $style = '';
$media_bottom  = false;

if (isset($this->help) && $this->help === true) {
    $help = [];
    $help['info']         = 'Das Fragment ezeugt UiKit-Cards: https://getuikit.com/assets/uikit/tests/card.html';
    $help['media']        = 'Nimmt Markup für ein Medium / uk-media an (String)';
    $help['media_bottom'] = 'Definiert ob das Medium am Ende dargestellt werden soll (bool)';
    $help['title']        = 'Titel bzw. Header (String)';
    $help['body']         = 'Body (String)';
    $help['body_prepend'] = 'vor Body (String)';
    $help['body_append']  = 'nach Body (String)';
    $help['footer']       = 'Footer (String)';
    $help['attributes']   = 'Hier können Attribute zur uk-card ergänzt werden (array), bei class werden diese an .uk-card angehänt ';
    dump($help);
}

// check if media and position are set
if (isset($this->media) && $this->media != '') {
    $media  = '<div class="uk-card-media-top">' . $this->media . '</div>';
    if (isset($this->media_bottom) && $this->media_bottom === true) {
        $media = '';
        $media_bottom  = '<div class="uk-card-media-bottom">' . $this->media . '</div>';
    }
}
// default is allways uk-card
$attributes = [];
$attributes['class'] = 'uk-card';
if (isset($this->attributes) && is_array($this->attributes)) {
    if (array_key_exists('class', $this->attributes)) {
        $class = $this->attributes['class'];
        $attributes['class'] = 'uk-card ' . $class;
    }
}
$attributes_code = rex_string::buildAttributes($attributes);
?>
<hr>
<div>
    <div<?= $attributes_code ?>>
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
