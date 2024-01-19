<?php
global $f;
$baseURL = $f->request->root;
$templateBoo = $baseURL . 'themes/bootstrap';
$templateURL = $baseURL . 'themes/kunanui';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Kunan Bootstrap Theme</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?=$templateBoo?>/css/bootstrap.css" >
<link rel="stylesheet" href="<?=$templateBoo?>/css/custom.css" >
<script type="text/javascript" src="<?=$baseURL?>scripts/jquery-1.7.2.min.js"></script>
<script src="<?=$baseURL?>scripts/plugins/bootstrap.js" type="text/javascript"></script>
<script src="<?=$baseURL?>scripts/plugins/typeahead.js" type="text/javascript"></script>
	<link type="text/css" rel="stylesheet" href="<?=$templateURL?>/css/jquery-ui.css">
	<!--<link type="text/css" rel="stylesheet" href="<?=$templateURL?>/css/kunan.css">-->
	<link type="text/css" rel="stylesheet" href="<?=$templateURL?>/css/dashboardui.css">
	<link type="text/css" rel="stylesheet" href="<?=$templateURL?>/css/fileuploader.css">
	<link type="text/css" rel="stylesheet" href="<?=$templateURL?>/css/jquery.noty.css">
	<link rel="stylesheet" type="text/css" href="<?=$templateURL?>/css/tree_component.css" />
	<link rel="stylesheet" type="text/css" href="<?=$templateURL?>/css/jquery.pnotify.default.css" />
	<link rel="stylesheet" type="text/css" href="<?=$templateURL?>/css/jquery.svg.css" />
	<link rel="stylesheet" type="text/css" href="<?=$templateURL?>/css/fullcalendar.css" />
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery-ui-1.9.1.custom.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/fileuploader.js"></script>
    <script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.ba-resize.min.js"></script>
    <script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.scrollTo.js"></script>
    <script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.editable-select.js"></script>
    <script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.custom.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.json-2.2.min.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jstorage.min.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.cookie.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.numeric.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.jkey-1.2.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.dashboard.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.contextmenu.r2.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.blockUI.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.pnotify.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/_lib/_all.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/tree_component.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jqueryui.themeswitchertool.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.ui.datepicker-es.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.svg.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/sylvester.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/purecssmatrix.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.animtrans.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.zoomooz.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jquery.maphilight.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/fullcalendar.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/globalize.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/globalize.culture.es-ES.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jspdf.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/FileSaver.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/BlobBuilder.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jspdf.plugin.standard_fonts_metrics.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jspdf.plugin.split_text_to_size.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/jspdf.plugin.from_html.js"></script>
	<script type="text/javascript" src="<?=$baseURL?>scripts/plugins/bytescoutpdf1.04.80.js"></script>
<script type="text/javascript" src="http://server/libraries/kunan.js/kunan.js"></script>
</head>
<body>
	<div id="content" class="row-fluid no-space" style="background-color:#3A3A40">
	    <nav id="primary" class="span1">
			<div class="row-fluid naveg">
				<a class="item" href="#"><span class="icon-home icon-48"></span> Inicio</a>
				<a class="item" href="#"><span class="icon-user icon-48"></span> Usuarios</a>
				<a class="item" href="#"><span class="icon-table icon-48"></span> Contenido</a>
				<a class="item" href="#"><span class="icon-question-sign icon-48"></span> &nbsp;Ayuda</a>
				<a class="item" href="#"><span class="icon-remove icon-48"></span>  &nbsp;Cerrar Sesi&oacute;n</a>
			</div>
	    </nav>
		<nav id="secondary" class="span2" style="border-right:solid 2px #3F3C3C;">
			<div class="well">
			<img src="https://dl.dropbox.com/u/832330/plastique/images/avatar.png" alt="avatar" />
	<div class="btn-group">
	                <button data-toggle="dropdown" class="btn btn-mini dropdown-toggle">Username <span class="caret"></span></button>
	                <ul class="dropdown-menu">
	                  <li><a href="#">Cambiar Contrase&ntilde;a</a></li>
	                  <li><a href="#">Ayuda</a></li>
	                  <li><a href="#">Cerrar Sesión</a></li>
	                </ul>
	              </div>
			<div class="hidden-desktop right">
				<a class="btn btn-inverse" data-toggle="collapse" data-target=".nav-collapse">
				<i class="icon-reorder"></i>
				</a>
			</div>
			</div>
			<div class="nav-collapse collapse">
			<ul class="nav nav-tabs nav-stacked">
				<li class="active"><a href="#"><i class="icon-home icon-white"></i> Elemento 1</a></li>
				<li><a href="#"><i class="icon-home icon-white"></i> Elemento 2</a></li>
				<li><a href="#"><i class="icon-share icon-white"></i> Elemento 3</a></li>
				<li><a href="#"><i class="icon-camera icon-white"></i> Elemento 4</a></li>
				<li><a href="#"><i class="icon-pencil icon-white"></i> Elemento 5</a></li>
			</ul>
			</div>
	    </nav>
		<section id="Main" class="span9 main" style="background-color:#818D9B;">
			<div class="navbar navbar-inverse">
	  			<div class="navbar-inner">
	    				<a class="brand" href="#">Titulo</a>
	  			</div>
			</div>
			<div class="container-fluid" id="mainPanel">
					<div class="row-fluid">
				<div class="span12">
				<div class="box">
	      			<div class="tab-header">
	        		<i class="icon-th-list"></i> Botones y Modal
	      			</div>
	      			<div class="padded">
			Contenido
			    <!-- Button to trigger modal -->
	    <a href="#myModal" role="button" class="btn" data-toggle="modal">Launch demo modal</a>
	     
	    <!-- Modal -->
	    <div id="myModal" class="modal black-box hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-header tab-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
	    <i class="icon-th-list"></i> Modal Titulo
	    </div>
	    <div class="modal-body separator">
	    <p>One fine body...</p>
	    </div>
	    <div class="modal-footer">
	    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	    <button class="btn btn-primary">Save changes</button>
	    </div>
	    </div>
			<span class="btn">Boton</span>
			<span class="btn btn-primary">Boton</span>
			<span class="btn btn-info">Boton</span>
			<span class="btn btn-success">Boton</span>
			<span class="btn btn-primary">Boton</span>
			<span class="btn btn-warning">Boton</span>
			<span class="btn btn-danger">Boton</span>
			<span class="btn btn-inverse">Boton</span>
			      			</div>
	    		</div>
	    		</div>
			</div>
			
			<div class="row-fluid">
				<div class="span12">
				<div class="box">
	      			<div class="tab-header">
	        		<i class="icon-th-list"></i> Horizontal Tabs
	      			</div>
	      			<div class="padded">
						<!-- Start Tabs Horizontal -->
				
	            <ul class="nav nav-tabs" id="myTab">
	             	<li class="active"><a data-toggle="tab" href="#home">Home</a></li>
	            	<li><a data-toggle="tab" href="#profile">Profile</a></li>
	            </ul>
	            <div class="tab-content" id="myTabContent">
	              <div id="home" class="tab-pane fade in active">
	                <p>Tab content home</p>
	              </div>
	              <div id="profile" class="tab-pane fade">
	                <p>Tab content profile</p>
	              </div>
	            </div>
	
						<!-- End Tabs Horizontal -->
	      			</div>
	    		</div>
	    		</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
				<div class="box">
	      			<div class="tab-header">
	        		<i class="icon-th-list"></i> Vertical Tabs
	      			</div>
	      			<div class="padded">
					
						<!-- Start Vertical Tabs -->
						<script>
					    $('#myTabV a').click(function (e) {
					        e.preventDefault();
					        $(this).tab('show');
					        })
						</script>
				<div class="tabbable tabs-left">
	            <ul class="nav nav-tabs" id="myTabV">
	             	<li class="active"><a data-toggle="tab" href="#inicio">Home</a></li>
	            	<li><a data-toggle="tab" href="#perfil">Profile</a></li>
	            </ul>
	            <div class="tab-content" id="myTabContent">
	              <div id="inicio" class="tab-pane fade in active">
	                <p>Tab content home</p>
	              </div>
	              <div id="perfil" class="tab-pane fade">
	                <p>Tab content profile</p>
	              </div>
	            </div>
	            </div>
						<!-- End Vertical Tabs -->
	     
			      	</div>
	    		</div>
	    		</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
				<div class="box">
	      			<div class="tab-header">
	        		<i class="icon-th-list"></i> Span6
	      			</div>
	      			<div class="padded">
	        		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	      			</div>
	    		</div>
	    		</div>
	    		<div class="span6">
				<div class="black-box">
	      			<div class="tab-header">
	        		<i class="icon-th-list"></i> Span6
	      			</div>
	      			<div class="padded">
	        		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	      			</div>
	    		</div>
	    		</div>
			</div>
			
			<div class="row-fluid">
				<div class="span4">
				<div class="box">
	      			<div class="tab-header">
	        		<i class="icon-th-list"></i> Span4
	      			</div>
	      			<div class="padded">
	        		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	      			</div>
	    		</div>
	    		</div>
	    		<div class="span4">
				<div class="box">
	      			<div class="tab-header">
	        		<i class="icon-th-list"></i> Span4
	      			</div>
	      			<div class="padded">
	        		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	      			</div>
	    		</div>
	    		</div>
	    		<div class="span4">
				<div class="box">
	      			<div class="tab-header">
	        		<i class="icon-th-list"></i> Span4
	      			</div>
	      			<div class="padded">
	        		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	      			</div>
	    		</div>
	    		</div>
			</div>
			
			</div>
			<br />
	    </section>
	</div>
	<script type="text/javascript" src="<?=$baseURL?>scripts/ci/helper.js"></script>
	<?php if($f->session->tasks['mg.titu']){ ?><script type="text/javascript" src="<?=$baseURL?>scripts/test/titu.js"></script><?php } ?>
	<script type="text/javascript">
	K.base = "<?=$baseURL?>";
	</script>
</body>
</html>
