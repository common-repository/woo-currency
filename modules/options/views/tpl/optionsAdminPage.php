<div class="wrap">
    <div class="woobewoo-plugin">
        <?php /*?><header class="woobewoo-plugin">
            <h1><?php echo WCU_WP_PLUGIN_NAME?></h1>
        </header><?php */?>
		<?php echo $this->breadcrumbs?>
        <section class="woobewoo-content">
            <nav class="woobewoo-navigation woobewoo-sticky <?php dispatcherWcu::doAction('adminMainNavClassAdd')?>">
                <ul>
					<?php foreach($this->tabs as $tabKey => $tab) { ?>
						<?php if(isset($tab['hidden']) && $tab['hidden']) continue;?>
						<li class="woobewoo-tab-<?php echo $tabKey;?> <?php echo (($this->activeTab == $tabKey || in_array($tabKey, $this->activeParentTabs)) ? 'active' : '')?>">
							<a href="<?php echo $tab['url']?>" title="<?php echo $tab['label']?>">
								<?php if(isset($tab['fa_icon'])) { ?>
									<i class="fa <?php echo $tab['fa_icon']?>"></i>	
								<?php } elseif(isset($tab['wp_icon'])) { ?>
									<i class="dashicons-before <?php echo $tab['wp_icon']?>"></i>	
								<?php } elseif(isset($tab['icon'])) { ?>
									<i class="<?php echo $tab['icon']?>"></i>	
								<?php }?>
								<span class="sup-tab-label"><?php echo $tab['label']?></span>
							</a>
						</li>
					<?php }?>
                </ul>
            </nav>
            <div class="woobewoo-container woobewoo-<?php echo $this->activeTab?>">
				<?php echo $this->content?>
                <div class="clear"></div>
            </div>
        </section>
    </div>
</div>
<!--Option available in PRO version Wnd-->
<div id="wcuOptInProWnd" style="display: none;" title="<?php _e('Improve Free version', WCU_LANG_CODE)?>">
	<p id="wcuOptWndTemplateTxt" style="display: none;">
		<?php printf(__('Please be advised that this template with all other options and PRO templates is available only in <a target="_blank" href="%s">PRO version</a>. You can <a target="_blank" href="%s" class="button">Get PRO</a> today and get this and other PRO features!', WCU_LANG_CODE), $this->mainLink, $this->mainLink)?>
	</p>
	<p id="wcuOptWndOptionTxt">
		<?php printf(__('Please be advised that this option is available only in <a target="_blank" href="%s">PRO version</a>. You can <a target="_blank" href="%s" class="button">Get PRO</a> today and get this and other PRO option!', WCU_LANG_CODE), $this->mainLink, $this->mainLink)?>
	</p>
</div>
