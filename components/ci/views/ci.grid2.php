<?php foreach ($cols as $item){
  $tw += $item['w'];
} ?>
<div class="grid" style="min-width:<?php echo $tw; ?>px;">
	<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
		<ul>
			<?php foreach ($cols as $item){ ?>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:<?=($item['w'])?>px;max-width:<?=($item['w'])?>px"><?=$item['nomb']?></li>
			<?php } ?>
		</ul>
	</div>
	<div class="gridBody"></div>
	<div class="gridReference">
		<ul>
			<?php foreach ($cols as $item){ ?>
			<li style="min-width:<?=$item['w']?>px;max-width:<?=$item['w']?>px"></li>
			<?php } ?>
		</ul>
	</div>
</div>