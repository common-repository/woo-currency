<?php
	$countBreadcrumbs = count($this->breadcrumbsList);
?>
<nav id="woobewoo-breadcrumbs" class="woobewoo-breadcrumbs <?php dispatcherWcu::doAction('adminBreadcrumbsClassAdd')?>">
	<?php dispatcherWcu::doAction('beforeAdminBreadcrumbs')?>
	<?php foreach($this->breadcrumbsList as $i => $crumb) { ?>
		<a class="woobewoo-breadcrumb-el" href="<?php echo $crumb['url']?>"><?php echo $crumb['label']?></a>
		<?php if($i < ($countBreadcrumbs - 1)) { ?>
			<span class="breadcrumbs-separator"></span>
		<?php }?>
	<?php }?>
	<?php dispatcherWcu::doAction('afterAdminBreadcrumbs')?>
</nav>