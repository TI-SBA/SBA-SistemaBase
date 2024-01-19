<?php global $f; ?>
<div>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#detalle" aria-controls="detalle" role="tab" data-toggle="tab">General</a></li>
		<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Revisiones</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane fade in active" id="detalle">
			<?php if($tipo=="B"){ ?>
			<?=$f->response->view('lg/pedi.bien.edit')?>
			<?php }elseif($tipo=="S"){ ?>
			<?=$f->response->view('lg/pedi.serv.edit')?>
			<?php }elseif($tipo=="L"){ ?>
			<?=$f->response->view('lg/pedi.loca.edit')?>
			<?php } ?>
		</div>
		<div role="tabpanel" class="tab-pane fade" id="profile">
			<div class="ibox-content inspinia-timeline"></div>
		</div>
	</div>
</div>