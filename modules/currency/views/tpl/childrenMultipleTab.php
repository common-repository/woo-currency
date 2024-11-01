<?php
    $moduleName = $this->moduleName;
    $moduleTab = $this->moduleTab;
    $moduleIsPro = $this->moduleIsPro;
    $showPreviewAjax = $this->showPreviewAjax;
    $optionsDb = $this->getModule()->optionsDbOpt;
    if (!empty($moduleIsPro) && $moduleIsPro) {
        $optionsDb = $this->getModule()->optionsDbOptPro;
    }
 ?>

 <?php if ($showPreviewAjax) { ?>
     <div class="container-fluid" style="display:block;position:relative;float:left;width:100%;">
     <div class="row" style="display:block;position:relative;float:left;width:100%;">
     <div class="col-md-8 wcuTabContentChildOptions">
 <?php } ?>

<table class="form-table moduleName<?php echo $moduleName?>">
    <tbody>
        <?php foreach($this->optionsParams as $indexTab => $optTab) {?>
        <?php if($indexTab != $moduleName) continue;?>
        <?php foreach($optTab as $indexTabSubOpt => $optBlock) {?>
        <?php if($indexTabSubOpt != $moduleTab) continue;?>
        <?php foreach($optBlock as $key => $opt) {?>
        <?php
            $display = !empty($opt['row_parent']) ? $this->isDisplayElem($optBlock[$opt['row_parent']]['params']['value'], $opt) : '';
            $inrow = !empty($opt['inrow']) ? $opt['inrow'] : false;
            ?>
        <?php if ( ($inrow === 'open') || (!$inrow) ) {?>
        <?php $opt['row_hide'] = !empty($opt['row_hide']) ? $opt['row_hide'] : '' ?>
        <?php $opt['row_show'] = !empty($opt['row_show']) ? $opt['row_show'] : '' ?>
        <?php $opt['tooltip'] = !empty($opt['tooltip']) ? $opt['tooltip'] : '' ?>
        <?php $opt['row_hide_with_all'] = !empty($opt['row_hide_with_all']) ? $opt['row_hide_with_all'] : '' ?>
        <tr valign="top" class="single_select_page <?php echo $opt['row_classes']?>" data-target-show="<?php echo $opt['row_show']?>" data-target-hide="<?php echo $opt['row_hide']?>" data-hide-with-all="<?php echo $opt['row_hide_with_all']?>" style="<?php echo $display?>">
            <th scope="row" class="col-md-3 titledesc">
                <div><?php echo $opt['label']?></div>
            </th>
            <td class="col-md-1 forminp">
                <?php if ($opt['tooltip']) {?>
                    <i class="fa fa-question woobewoo-tooltip tooltipstered" title="<?php echo htmlspecialchars ($opt['tooltip'])?>"></i>
                <?php }?>
            </td>
            <td class="col-md-8 forminp">
                <?php } ?>
				<?php if ($inrow) {?>
					<div class="wcuTabContentInrowElement">
				<?php }?>
                <?php $htmlField = $opt['html'];?>
                <?php echo htmlWcu::$htmlField("{$optionsDb}[{$indexTab}][{$indexTabSubOpt}][{$key}]", $opt['params']); ?>
				<?php if ($inrow) {?>
					</div>
				<?php }?>
				<?php if ( ($inrow === 'close') || (!$inrow) ) { ?>
            </td>
        </tr>
        <?php }?>
        <?php }?>
        <?php }?>
        <?php }?>
    </tbody>
</table>

<?php if ($showPreviewAjax) {?>
    </div>
    <div class="col-md-4 supsystic-sticky-wrapper">
        <div class="wcuTabContentPreviewInner supsystic-sticky wcuTabContentPreviewInner<?php echo $moduleName ?> "></div>
    </div>
    </div>
    </div>
    <script type="text/javascript">
    jQuery(document).ready(function () {
        wcuLookPreviewAjax('<?php echo $moduleName ?>');

        var wcuCanSendAjax = true;

        function wcuLookPreviewAjaxHandler() {
            if (wcuCanSendAjax == true) {
                wcuCanSendAjax = false;
                setTimeout(function () {
                    wcuLookPreviewAjax('<?php echo $moduleName ?>');
                    wcuCanSendAjax = true;
                }, 1500);
            }
        }

        jQuery(".moduleName<?php echo $moduleName?> .ui-sortable-handle").on("click", function (e) {
            wcuLookPreviewAjaxHandler();
        });

        jQuery(".moduleName<?php echo $moduleName?> .ui-sortable").on("sortupdate", function (event, ui) {
            wcuLookPreviewAjaxHandler();
        });

        jQuery('.moduleName<?php echo $moduleName?> .wp-color-picker').wpColorPicker({
            change: function (event, ui) {
                var element = event.target;
                var color = ui.color.toString();
                wcuLookPreviewAjaxHandler();
            },
        });

        jQuery(".moduleName<?php echo $moduleName?> input").on("change paste keyup", function (e) {
            wcuLookPreviewAjaxHandler();
        });

        jQuery(".moduleName<?php echo $moduleName?> select").on("change paste keyup", function () {
            wcuLookPreviewAjaxHandler();
        });

    });
    </script>
<?php } ?>
