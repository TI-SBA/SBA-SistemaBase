<?php
global $f;
$baseURL = $f->request->root;
//$templateURL = $baseURL . 'themes/' . $f->config('template', 'name') . '/';
$templateURL = $baseURL . 'themes/' . 'devops' . '/';
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Sistema SBPA</title>
		<meta name="description" content="description">
		<meta name="author" content="DevOOPS">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="<?=$templateURL?>plugins/bootstrap/bootstrap.css" rel="stylesheet">
		<link href="<?=$templateURL?>plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet">
		<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>
		<link href="<?=$templateURL?>plugins/fancybox/jquery.fancybox.css" rel="stylesheet">
		<link href="<?=$templateURL?>plugins/fullcalendar/fullcalendar.css" rel="stylesheet">
		<link href="<?=$templateURL?>plugins/xcharts/xcharts.min.css" rel="stylesheet">
		<link href="<?=$templateURL?>plugins/select2/select2.css" rel="stylesheet">
		<link href="<?=$templateURL?>plugins/bootstrap-notify/css/bootstrap-notify.css" rel="stylesheet">
		<link href="<?=$templateURL?>css/pnotify.custom.min.css" rel="stylesheet">
    	<link href="<?=$baseURL.'themes/inspinia/'?>css/fileinput.css" rel="stylesheet">
		<link href="<?=$baseURL.'themes/inspinia/'?>css/datepicker.css" rel="stylesheet">
    	<link href="<?=$baseURL.'themes/inspinia/'?>css/toastr.css" rel="stylesheet">
		<link href="<?=$templateURL?>css/style.css" rel="stylesheet">
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
				<script src="http://getbootstrap.com/docs-assets/js/html5shiv.js"></script>
				<script src="http://getbootstrap.com/docs-assets/js/respond.min.js"></script>
		<![endif]-->
	</head>
<body>
<!--Start Header-->
<div id="screensaver">
	<canvas id="canvas"></canvas>
	<i class="fa fa-lock" id="screen_unlock"></i>
</div>
<div id="modalbox">
	<div class="devoops-modal">
		<div class="devoops-modal-header">
			<div class="modal-header-name">
				<span>Basic table</span>
			</div>
			<div class="box-icons">
				<a class="close-link">
					<i class="fa fa-times"></i>
				</a>
			</div>
		</div>
		<div class="devoops-modal-inner">
		</div>
		<div class="devoops-modal-bottom">
		</div>
	</div>
</div>
<header class="navbar" style="margin: 0; border: 0; position: fixed; top:0; left: 0; width:100%; -webkit-border-radius: 0; -moz-border-radius: 0; border-radius: 0; box-shadow: 0 1px 2px #272727; z-index: 2000;">
	<div class="container-fluid expanded-panel">
		<div class="row">
			<div id="logo" class="col-xs-12 col-sm-2">
				<a href="http://www.sbparequipa.gob.pe">Beneficencia</a>
			</div>
			<div id="top-panel" class="col-xs-12 col-sm-10">
				<div class="row">
					<div class="col-xs-8 col-sm-4">
						<a href="#" class="show-sidebar">
						  <i class="fa fa-bars"></i>
						</a>
						<div id="search">
							<input type="text" placeholder="buscar"/>
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="col-xs-4 col-sm-8 top-panel-right">
						<ul class="nav navbar-nav pull-right panel-menu">
							<li class="hidden-xs">
								<a href="index.html" class="modal-link">
									<i class="fa fa-bell"></i>
									<span class="badge">7</span>
								</a>
							</li>
							<li class="hidden-xs">
								<a class="ajax-link" href="ajax/calendar.html">
									<i class="fa fa-calendar"></i>
									<span class="badge">7</span>
								</a>
							</li>
							<li class="hidden-xs">
								<a href="ajax/page_messages.html" class="ajax-link">
									<i class="fa fa-envelope"></i>
									<span class="badge">7</span>
								</a>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle account" data-toggle="dropdown">
									<div class="avatar">
										<img src="<?=$templateURL?>css/avatar.png" class="img-rounded" alt="avatar" />
									</div>
									<i class="fa fa-angle-down pull-right"></i>
									<div class="user-mini pull-right">
										<span class="welcome">Bienvenido,</span>
										<span><?php echo $f->session->user['userid']; ?></span>
									</div>
								</a>
								<ul class="dropdown-menu">
									<li>
										<a href="#">
											<i class="fa fa-user"></i>
											<span>Perfil</span>
										</a>
									</li>
									<li>
										<a href="ajax/page_messages.html" class="ajax-link">
											<i class="fa fa-envelope"></i>
											<span>Mensajes</span>
										</a>
									</li>
									<li>
										<a href="ajax/gallery_simple.html" class="ajax-link">
											<i class="fa fa-picture-o"></i>
											<span>Albums</span>
										</a>
									</li>
									<li>
										<a href="ajax/calendar.html" class="ajax-link">
											<i class="fa fa-tasks"></i>
											<span>Tasks</span>
										</a>
									</li>
									<li>
										<a href="#">
											<i class="fa fa-cog"></i>
											<span>Settings</span>
										</a>
									</li>
									<li>
										<a href="ci/index/logout">
											<i class="fa fa-power-off"></i>
											<span>Logout</span>
										</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<!--End Header-->
<!--Start Container-->
<div id="main" class="container-fluid">
	<div class="row">
		<?=$f->response->view('ci/navg');?>
		<!--Start Content-->
		<div id="content" class="col-xs-12 col-sm-10" style="padding-top: 10px;">
		</div>
		<!--End Content-->
	</div>
</div>
<div id="ajax-modal" class="modal" tabindex="-1"></div>
<div class='notifications top-right'></div>
<style>
.highlights {
    background-color: #fbec88;
}
</style>
<!--End Container-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!--<script src="http://code.jquery.com/jquery.js"></script>-->
<script src="<?=$templateURL?>plugins/jquery/jquery-2.1.0.min.js"></script>
<!--<script src="<?=$templateURL?>plugins/jquery-ui/jquery-ui.min.js"></script>-->
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?=$templateURL?>plugins/bootstrap/bootstrap.min.js"></script>
<script src="<?=$baseURL.'themes/inspinia/'?>js/bootstrap-datepicker.js"></script>
<script src="<?=$baseURL.'themes/inspinia/'?>js/toastr.js"></script>
<script src="<?=$templateURL?>plugins/justified-gallery/jquery.justifiedgallery.min.js"></script>
<script src="<?=$templateURL?>plugins/tinymce/tinymce.min.js"></script>
<script src="<?=$templateURL?>plugins/tinymce/jquery.tinymce.min.js"></script>
<script src="<?=$baseURL?>scripts/plugins/jquery.cookie.js"></script>
<script src="<?=$baseURL?>scripts/plugins/jstorage.min.js"></script>
<script src="<?=$baseURL?>scripts/plugins/jquery.numeric.js"></script>
<script src="<?=$templateURL?>plugins/bootstrap-notify/js/bootstrap-notify.js"></script>
<script src="<?=$templateURL?>plugins/jquery.bootstrap.wizard/jquery.bootstrap.wizard.js"></script>
<script src="<?=$baseURL?>scripts/plugins/fileinput.js"></script>
<script src="<?=$baseURL?>scripts/plugins/fileinput_locale_es.js"></script>
<!--<script src="<?=$baseURL?>scripts/plugins/jquery.blockUI.js"></script>-->
<!-- All functions for this theme + document.ready processing -->
<script data-main="<?=$baseURL?>scripts/app_ins.js" src="<?=$baseURL?>scripts/require.js"></script>
<script src="<?=$baseURL?>scripts/kunan_ins.js"></script>
<script src="<?=$templateURL?>js/devoops.js"></script>
<script type="text/javascript">
K.session = {
	user: <?php echo json_encode($f->session->user); ?>,
	enti: <?php echo json_encode($f->session->enti); ?>,
	titular: <?php echo json_encode($f->session->titular); ?>,
	tasks: <?php echo json_encode($f->session->tasks); ?>
};
Object.freeze(K.session.tasks);
Object.freeze(K.session.user);
Object.freeze(K.session.enti);
Object.freeze(K.session.titular);
</script>
</body>
</html>