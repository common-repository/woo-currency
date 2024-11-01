<style type="text/css">
	.wcuAdminMainLeftSide {
		width: 56%;
		float: left;
	}
	.wcuAdminMainRightSide {
		width: <?php echo (empty($this->optsDisplayOnMainPage) ? 100 : 40)?>%;
		float: left;
		text-align: center;
	}
	#wcuMainOccupancy {
		box-shadow: none !important;
	}
</style>
<section>
	<div class="woobewoo-item woobewoo-panel">
		<div id="containerWrapper">
			<?php _e('Main page Go here!!!!', WCU_LANG_CODE)?>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>