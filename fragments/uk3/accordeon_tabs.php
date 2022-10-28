<?php
/**
 * @var rex_fragment $this
 * @psalm-scope-this rex_fragment
 */


if (isset($this->help) && $this->help === true) {
    $help = [];
    $help['info']  = 'Nimmt ein Array an und erstellt eine Tab oder Akkordeon Liste';
    $help['type']  = 'Bei 1 > Akkordeon, bei 2 Tabs';
    $help['items'] = 'Array mit den Keys title und body';
    dump($help);
}

$values = [];
if (isset($this->items) && is_array($this->items)){
    $values = array_filter($this->items);
}
$type = 1;
if (isset($this->type)){
    $type = $this->type;
}
?>
<div class="uk-container uk-container-small">
<?php if ($type === 1) : ?>
        <div uk-accordion>
            <?php foreach ($values as $i=>$value) : ?>
                <div>
                    <?php if (isset($this->title) && is_string($this->title) && $this->title !== '') : ?>
                    <a href="#" tabindex="0" class="uk-accordion-title uk-background-muted uk-padding-small"><?= $value['title'] ?></a>
                     <?php endif; ?>
                    <div class="uk-accordion-content">
                        <?php if (isset($this->body) && is_string($this->body) && $this->body !== '') : ?>
                        <p><?= $value['body'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
<?php elseif ($type === 2) : ?>
        <div class="uk-margin-medium-top">
            <div class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-fade">
            <?php foreach ($values as $value) : ?>
              <?php if (isset($this->title) && is_string($this->title) && $this->title !== '') : ?>
               <div><a tabindex="0" href="#"><?= $value['title'] ?></a></div>
              <?php endif; ?>  
            <?php endforeach ?>
            </div>
            <div class="uk-switcher uk-margin">
            <?php foreach ($values as $value) : ?>
                <?php if (isset($this->body) && is_string($this->body) && $this->body !== '') : ?>
                <div><?= $value['body'] ?></div>
                 <?php endif; ?>
            <?php endforeach ?>
            </div>
        </div>
<?php endif ?>
</div>
