<h2>Connectors...</h2>

<?php foreach($connectors as $connector): ?>
    <div class="connector" id="connector_<?=$this->e($connector);?>">
        <h2><?=$this->e($connector::name)?> (v<?=$this->e($connector::version)?>)</h2>
        <?php if($connector->isauthorised()): ?>
            <p>Authorised</p>
        <?php else: ?>
            $connector->authorise();
        <?php endif; ?>
    </div>
<?php endforeach;?>