<?php $tw = 0; ?>
<div class="grid">
	<div class="gridHeader ui-state-default ui-jqgrid-hdiv">
		<ul>
			<?php foreach ($cols as $item){ ?>
			<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:<?=($item['w'])?>px;max-width:<?=($item['w'])?>px"><?=$item['nomb']?></li>
			<?php $tw += $item['w'];
			} ?>
		</ul>
	</div>
</div>
<!--<div class="grid" style="min-width:<?php echo $tw; ?>px">-->
<div class="grid">
	<div class="gridBody"></div>
	<div class="gridReference">
		<ul>
			<?php foreach ($cols as $item){
				$align = '';
				if(isset($item['align'])){?>
					<?php $align = "text-align:".$item['align'];
				}?>
			<li style="min-width:<?=$item['w']?>px;max-width:<?=$item['w']?>px;<?=$align?>"></li>
			<?php } ?>
		</ul>
	</div>
</div>