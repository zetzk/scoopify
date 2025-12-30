<?php if (isset($js)) { ?>
    <?php foreach ($js as $index => $js_) { ?>
        <script src="<?= $js_ . '?t=' . time() ?>"></script>
    <?php } ?>
<?php } ?>