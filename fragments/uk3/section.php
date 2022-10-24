<?php
if (isset($this->help) && $this->help === true) {
    $help = [];
    $help['info']         = 'Das Fragment ezeugt eine Section: https://getuikit.com/assets/uikit/tests/section.html';
    $help['body']         = 'Body (String)';
    $help['attributes']   = 'Hier können Attribute ergänzt werden (array), bei class werden diese an .uk-section angehägnt ';
    dump($help);
}

// default class uk-section
$attributes = [];
$attributes['class'] = 'uk-section';
if (isset($this->attributes) && is_array($this->attributes)) {
    $attributes = $this->attributes;
    if (array_key_exists('class', $this->attributes)) {
        $class = $this->attributes['class'];
        $attributes['class'] = 'uk-section ' . $class;
    }
}
$attributes_out = rex_string::buildAttributes($attributes);
?>
<section<?= $attributes_out?>>
<?php if (isset($this->body) && $this->body !== '') : ?>
<?= $this->body ?>
<?php endif; ?>
</section>
