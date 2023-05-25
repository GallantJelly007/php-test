<?php
    $isAdmin = isset($_SESSION['auth_user']) && $_SESSION['auth_user']['role']=='admin';
    $status = $isAdmin ? Renderer::render(ROOT.'/views/patterns/obj_status.php') : '';
    $adminButtons = $isAdmin ? Renderer::render(ROOT.'/views/patterns/admin_buttons.php',['root'=>false]) : '';
    $attribDesc = !$isAdmin ? 'data-desc="'.$obj->description.'"':''
?>

<li class="obj-item <?php echo $isContainsChild ? 'deploy' : ''?> pos-rel" data-id="<?php echo $obj->id?>" <?php echo $attribDesc?>>
    <div class="obj-content p-10 maxw-70 maxw-m-100 <?php echo !$isAdmin?'obj-content-user':'' ?>">
        <div class="d-flex jc-bspace ai-center <?php echo $isAdmin?'fd-m-col-rs mb-05':'' ?>">
            <div class="d-flex w-100 obj-title">
                <?php if($isAdmin){ ?>
                    <input type="checkbox" class="select-obj mr-05">
                <?php } ?>
                <p class="fw-bold obj-name c-main"><?php echo $obj->title ?></p>
            </div>
            <?php if($isContainsChild||$isAdmin){?>
                    <div class="d-flex ml-10 manage-panel jc-m-bspace ml-m-0 <?php echo $isAdmin?'mb-m-10 w-m-100':'' ?>">
                        <?php echo $adminButtons ?>
                        <?php if($isContainsChild){ ?>
                            <button class="deploy-btn <?php echo $adminButtons!=''?'ml-10':''?>"><span>&#10095;</span></button>
                        <?php } ?>
                    </div>
            <?php } ?> 
        </div>
        <?php if($isAdmin){?>
            <p class="bt-1 pt-05 fsz-07 obj-desc c-dark"><?php echo $obj->description ?></p>
        <?php } ?>
        
        <?php echo $status?>
    </div>
    <?php echo $children?>
</li>