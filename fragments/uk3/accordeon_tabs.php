<?php
if (isset($this->help) && $this->help === true) {
    $help = [];
    $help['info']  = 'Nimmt ein Array an und erstellt eine Tab oder Akkordeon Liste';
    $help['type']  = 'Bei 1 > Akkordeon, bei 2 Tabs';
    $help['items'] = 'Array mit den Keys title und body';
    dump($help);
}
if (isset($this->items) && is_array($this->items)){
    $values = [];
    $values = $this->items;
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
                    <a href="#" tabindex="0" class="uk-accordion-title uk-background-muted uk-padding-small"><?= $value['titel'] ?></a>
                    <div class="uk-accordion-content">
                        <p><?= $value['text'] ?></p>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
<?php elseif ($type == 2) : ?>
        <div class="uk-margin-medium-top">
            <div class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-fade">
            <?php foreach ($values as $value) : ?>
               <div><a tabindex="0" href="#"><?= $value['titel'] ?></a></div>
            <?php endforeach ?>
            </div>
            <div class="uk-switcher uk-margin">
            <?php foreach ($values as $value) : ?>
                <div><?= $value['text'] ?></div>
            <?php endforeach ?>
            </div>
        </div>
<?php endif ?>
</div>
