<div id="ei">
<?php include_once $include_path .'_status_messages.php'; ?>

<?php if ($admin_scripts): ?>
<h1><?=lang('admin_scripts_module_name')?> <span>(v<?=$module_version; ?>)</span></h1>

<?php foreach ($admin_scripts AS $bundle): ?>
<div class="admin_script_bundle">
    <div class="hd">
        <h2><?php echo $bundle['name']; ?></h2>
        <p><?php echo $bundle['description']; ?></p>
    </div>

    <ul class="bd">
    <?php foreach ($bundle['scripts'] AS $script): ?>
        <li class="admin_script">
            <h3 class="hd"><?php echo $script['name']; ?></h3>
            <p class="bd"><?php echo $script['description']; ?></p>
            <a class="btn ft" href="<?php echo $run_script_url .'bundle=' .$bundle['bundle'] .AMP .'script=' .$script['script']; ?>">Run this script</a>
        </li>
    <?php endforeach; ?>
    </ul>
</div>

<?php endforeach; ?>
</ul><!-- /#admin_scripts_index -->

<?php else: ?>
<p><?=lang('no_admin_scripts'); ?></p>

<?php endif; ?>
</div><!-- /#ei -->
