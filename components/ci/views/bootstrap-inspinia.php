<?php
global $f;
$baseURL = $f->request->root;
$templateURL = $baseURL . 'themes/inspinia/';
$templateURL_old = $baseURL . 'themes/kunanui/';
$cump = array();
if(isset($trabs)){
	foreach ($trabs as $key => $value) {
		//if(isset($value['roles.trabajador.fecnac'])){
			if(date('m-d') == substr(date('m-d',$value['roles']['trabajador']['fecnac']->sec),5,5)){
				$cump[] = $value;
			}
		//}
	}
}
?><!DOCTYPE html>
<html>
	<head>
		<link type="image/x-icon" rel="shortcut icon" href="<?=$baseURL?>images/favicon.ico">
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	    <meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <!-- <title>Sistema SBPA</title> -->
	    <title>Sistema SBA</title>
	    <link href="<?=$templateURL?>css/bootstrap.css" rel="stylesheet">
		<!--<link href="http://netdna.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.css" rel="stylesheet">-->
	    <link href="<?=$templateURL?>font-awesome-4.7.0/css/font-awesome.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/datepicker.css" rel="stylesheet">
	    <!-- Toastr style -->
	    <link href="<?=$templateURL?>css/toastr.css" rel="stylesheet">
	    <!-- Gritter -->
	    <link href="<?=$templateURL?>css/jquery.css" rel="stylesheet">
	    <link href="<?=$templateURL?>jqueryui/jquery-ui.min.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/animate.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/style.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/fileinput.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/icheck.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/bootstrap-datetimepicker.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/typeaheadmap.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/jstree.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/bootstrap-chosen.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/codemirror.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/ambiance.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/select2.min.css" rel="stylesheet">
	    <link rel="stylesheet" href="<?=$templateURL?>css/fullcalendar.min.css">
	    <!--<link href="<?=$templateURL?>css/daterangepicker.css" rel="stylesheet">-->
		<style>
			.fuelux{
				padding: 8px;
			}
			.datepicker{z-index:1151 !important;}
		</style>
	</head>
	<body class="pace-done">
	<style type="text/css" media="print">
    * { display: none; }
</style>
		<div class="pace  pace-inactive">
			<div data-progress="99" data-progress-text="100%" style="width: 100%;" class="pace-progress">
				<div class="pace-progress-inner"></div>
			</div>
			<div class="pace-activity"></div>
		</div>
		<div id="wrapper">
			<nav class="navbar-default navbar-static-side" role="navigation">
		    	<div class="sidebar-collapse" id="sidebar-left">
					<?=$f->response->view('ci/navg_inspinia',array('cump'=>$cump));?>
				</div>
			</nav>
			<div id="page-wrapper" class="gray-bg dashbard-1">
		        <div class="row border-bottom">
		        	<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
						<div class="navbar-header">
							<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i></a>
							<span name="titleBar" class="navbar-minimalize minimalize-styl-2">INTERFAZ DE TRABAJO</span>
						</div>
						<ul class="nav navbar-top-links navbar-right">
							<li>
								<span class="m-r-sm text-muted welcome-message">Bienvenido al Sistema de SBPA.</span>
							</li>
							<li class="dropdown">
								<a id="ChatList" class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
									<i class="fa fa-comments-o"></i><span class="label label-warning">0</span>
								</a>
								<ul class="dropdown-menu chat-user-list" name="userList"></ul>  	
							</li>
							<li>
								<a href="ci/index/logout">
									<i class="fa fa-sign-out"></i> Cerrar Sesi&oacute;n
								</a>
							</li>
						</ul>
		        	</nav>
		        </div>
				<!--<div class="row border-bottom white-bg dashboard-header" id="mainPanel"></div>-->
				<div class="wrapper wrapper-content">
					<div class="row ibox-content float-e-margins" id="mainPanel"></div>
				</div>
			</div>
		</div>
		<div id="ajax-modal" class="modal" tabindex="-1"></div>
		<div id="user-modal" class="modal user-modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="row-fluid">
				<div class="span12"><img name="imagen" src="images/admin.png"></div>			
			</div>
			<div class="row-fluid">
				<div class="span12"><strong name="user-use">Nombre</strong> esta usando este formulario</div>		
			</div>
			<div class="row-fluid">
				<div class="span12">
					<a name="chat" href="javascript:void(0);" class="btn btn-info">Iniciar Chat</a>
					<a name="close" data-dismiss="modal" aria-hidden="true" href="javascript:void(0);" class="btn btn-warning">Cerrar Cuadro</a>
				</div>
			</div>
		</div>
	    <!-- Mainly scripts -->
	    <script type="text/javascript" src="<?=$baseURL?>scripts/plugins/Chart.min.js"></script>
	    <script type="text/javascript" src="<?=$baseURL?>scripts/plugins/math.min.js"></script>
	    <script type="text/javascript" src="<?=$templateURL?>js/jquery.min.js"></script>
	    <script type="text/javascript" src="<?=$templateURL?>jqueryui/jquery-ui.min.js"></script>
	    <script type="text/javascript" src="<?=$templateURL?>js/bootstrap.min.js"></script>
	    <script type="text/javascript" src="<?=$templateURL?>js/bootstrap-datepicker.js"></script>
	    <script type="text/javascript" src="<?=$templateURL?>js/metisMenu.min.js"></script>
	    <script type="text/javascript" src="<?=$templateURL?>js/jquery.slimscroll.min.js"></script>
	    <!-- Custom and plugin javascript -->
	    <script type="text/javascript" src="<?=$templateURL?>js/inspinia.js"></script>
	    <script type="text/javascript" src="<?=$templateURL?>js/pace.js"></script>
	    <!-- Toastr -->
	    <script type="text/javascript" src="<?=$templateURL?>js/toastr.js"></script>
	    <script type="text/javascript" src="<?=$templateURL?>js/socket.io-1.2.0.js"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.cookie.js"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jstorage.min.js"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.numeric.js"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/fileinput.js"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/fileinput_locale_es.js"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.countdown.js" type="text/javascript"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/select2.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.validate.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/icheck.min.js"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/moment.js"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/bootstrap-datetimepicker.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/typeahead.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/typeaheadmap.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/jstree.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/chosen.jquery.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/jquery.highlight.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/codemirror.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/codemirror-mode/javascript.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/codemirror-mode/php.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/codemirror-mode/htmlmixed.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/codemirror-mode/css.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/codemirror-mode/xml.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/codemirror-mode/clike.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/jquery.filtertable.js"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/ubigeo.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/fullcalendar.min.js"></script>
		<script type="text/javascript" src="<?=$templateURL?>js/fullcalendar.es.js"></script>
		<!--<script type="text/javascript" src="<?=$templateURL?>js/daterangepicker.js"></script>-->
		<!-- Real start -->
		<script type="text/javascript" data-main="<?=$baseURL?>scripts/app_ins.js" src="<?=$baseURL?>scripts/require.js"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/kunan_ins.js"></script>
		<script type="text/javascript" src="<?=$baseURL?>scripts/ac/chat.js"></script>
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
		<?php if(sizeof($cump)>0): ?>
		K.session.cump = <?php echo json_encode($cump); ?>;
		require(['ci/helper_ins'],function(ciHelper){
			ciHelper.windowCumple();
		});
		<?php endif; ?>
		</script>
		<?=$f->response->view('ci/skin_config')?>
		<script type="text/javascript" src="<?=$baseURL?>scripts/main.js"></script>
	</body>
</html>