<?php
    $moduleName = $this->moduleName;
    $moduleIsPro = $this->moduleIsPro;
    $optionsDb = $this->getModule()->optionsDbOpt;
    if (!empty($moduleIsPro) && $moduleIsPro) {
        $optionsDb = $this->getModule()->optionsDbOptPro;
    }
 ?>

<table class="form-table">
    <tbody>
        <?php foreach($this->optionsParams as $index => $optBlock) {?>
        <?php if($index != $moduleName) continue;?>
        <?php foreach($optBlock as $key => $opt) {?>
        <?php $display = !empty($opt['row_parent']) ? $this->isDisplayElem($optBlock[$opt['row_parent']]['params']['value'], $opt) : ''; ?>
        <?php $opt['row_hide'] = !empty($opt['row_hide']) ? $opt['row_hide'] : '' ?>
        <?php $opt['row_show'] = !empty($opt['row_show']) ? $opt['row_show'] : '' ?>
        <?php $opt['row_hide_with_all'] = !empty($opt['row_hide_with_all']) ? $opt['row_hide_with_all'] : '' ?>
        <?php $opt['tooltip'] = !empty($opt['tooltip']) ? $opt['tooltip'] : '' ?>
        <tr valign="top" class="single_select_page <?php echo $opt['row_classes']?>" data-target-show="<?php echo $opt['row_show']?>" style="<?php echo $display?>">
            <th scope="row" class="col-md-3 titledesc">
                <div><?php echo $opt['label']?></div>
            </th>
            <td class="col-md-1 forminp">
                <?php if ($opt['tooltip']) {?>
                    <i class="fa fa-question woobewoo-tooltip tooltipstered" title="<?php echo htmlspecialchars($opt['tooltip'])?>"></i>
                <?php }?>
            </td>
            <td class="col-md-7 forminp">
                <?php $htmlField = $opt['html'];?>
                <?php echo htmlWcu::$htmlField("{$optionsDb}[{$index}][{$key}]", $opt['params'])?>
            </td>
        </tr>
        <?php }?>
        <?php }?>
    </tbody>
</table>
