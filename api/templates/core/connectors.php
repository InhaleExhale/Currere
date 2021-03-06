<h2>Connectors...</h2>

<?php foreach($connectors as $connector): ?>
    <div class="connector" id="connector_<?=$this->e($connector);?>">
        <h2><?=$this->e($connector::name)?> (v<?=$this->e($connector::version)?>)</h2>
        <?php if($connector->isAuthorised()): ?>
            <p>Connected (<a href="<?=$this->e($this->queryToUri("/?controller=Authentication&connector={$connector->getClass(true)}&action=deauthorise"));?>">Disconnect</a>)</p>
            <p><a href="<?=$this->e($this->queryToUri("/?controller=Activity&connector={$connector->getClass(true)}&action=getAllActivities"));?>">Get All Activities</a></p>
            <p><a href="<?=$this->e($this->queryToUri("/?controller=Activity&connector={$connector->getClass(true)}&action=getAllActivities&newOnly=true"));?>">Get New Activities</a></p>
            <p><a href="<?=$this->e($this->queryToUri("/?controller=Activity&connector={$connector->getClass(true)}&action=getGearForActivities"));?>">Load Gear for Activities</a></p>
        <?php else: ?>
            <p><a href="<?=$connector->authoriseLink()?>">Connect</a></p>
        <?php endif; ?>
    </div>
<?php endforeach;?>