<?php
if (isset($this->help) && $this->help === true) {
    $help = [];
    $help['info']         = 'Das Fragment ezeugt einen Container: https://getuikit.com/assets/uikit/tests/container.html';
    $help['body']         = 'Body (String)';
    $help['attributes']   = 'Hier können Attribute zur ergänzt werden (array), bei class werden diese an .uk-container angehägnt ';
    dump($help);
}

// default class uk-container
$attributes = [];
$attributes['class'] = 'uk-section';
if (isset($this->attributes) && is_array($this->attributes)) {
    $attributes = $this->attributes;
    if (array_key_exists('class', $this->attributes)) {
        $class = $this->attributes['class'];
        $attributes['class'] = 'uk-container ' . $class;
    }
}
$attributes_out = rex_string::buildAttributes($attributes);
?>
<container<?= $attributes_out?>>
<?php if (isset($this->body) && $this->body != '') : ?>
<?= $this->body ?>
<?php endif; ?>
</container>
