<?php
if (isset($this->help) && $this->help === true) {
    $help = [];
    $help['info']         = 'Das Fragment ezeugt eine Section: https://getuikit.com/assets/uikit/tests/section.html';
    $help['body']         = 'Body (String)';
    $help['body_prepend'] = 'vor Body (String)';
    $help['body_append']  = 'nach Body (String)';
    $help['attributes']   = 'Hier können Attribute zur ergänzt werden (array), bei class werden diese an .uk-section angehägnt ';
    dump($help);
}

// default class uk-section
$attributes = [];
$attributes['class'] = 'uk-section';
if (isset($this->attributes) && is_array($this->attributes)) {
    if (array_key_exists('class', $this->attributes)) {
        $class = $this->attributes['class'];
        $attributes['class'] = 'uk-card ' . $class;
    }
}
$attributes_out = rex_string::buildAttributes($attributes);
?>
<section<?= $attributes_code ?>>
<?php if (isset($this->body_prepend) && $this->body_prepend != '') : ?>
<?= $this->body_prepend ?>
<?php endif; ?>
<?php if (isset($this->body) && $this->body != '') : ?>
<?= $this->body ?>
<?php endif; ?>
<?php if (isset($this->body_append) && $this->body_append != '') : ?>
<?= $this->body_append ?>
<?php endif; ?>
</section>
