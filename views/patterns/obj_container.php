<?php
    $rootId = $root ? 'id="root-container"':'';
    $visible = $root ? '':'d-none';
    $parent = $parentId!=null&&!$root?'data-parent-id="'.$parentId.'"':'';
?>

<ul <?php echo $rootId ?> class="obj-container pos-rel <?php echo $visible?>" <?php echo $parent?>>
    <?php echo $items?>
</ul>