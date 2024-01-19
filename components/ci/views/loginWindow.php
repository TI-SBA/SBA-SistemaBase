<?php
global $f;
$baseURL = $f->request->root;
$templateURL = $baseURL . 'themes/inspinia/';
?><!DOCTYPE html>
<html>
	<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	    <meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <!-- <title>Sistema SBPA</title> -->
	    <title>Sistema SBA</title>
	    <link href="<?=$templateURL?>css/bootstrap.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/font-awesome.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/animate.css" rel="stylesheet">
	    <link href="<?=$templateURL?>css/style.css" rel="stylesheet">
			<link rel="shortcut icon" href="images/favicon.ico">
	</head>
	<body class="gray-bg">

	    <div class="loginColumns animated fadeInDown">
	        <div class="row">
	            <div class="col-sm-5 col-sm-pull-1">
	            	<!-- <h2 class="font-bold">Sistema SBPA</h2> 
								<h2 class="font-bold text-primary" style="margin-left: 120px; color: #038FD2;">Sistema SBA</h2>
								<p>
						<img src="images/logo.jpg" class="img img-rounded"/> -->
		            	<img src="images/sistema_.png" style="height: auto; width: auto; max-width: 600px; margin-top:150px;"/>
		            </p>
	              <p name="quote"></p>

	              <p>
	                   <small name="author"></small>
	              </p>

	            </div>
	            <div class="col-sm-6 col-sm-push-4" style="margin-top: 120px;">
	                <div class="ibox-content">
	                    <form id="form_login" name="form_login" class="m-t" role="form" action="<?=$baseURL?>ci/index/login">
	                        <div class="form-group">
	                            <input class="form-control" placeholder="Usuario" required="" type="text" id="l_user" name="l_user">
	                        </div>
	                        <div class="form-group">
	                            <input class="form-control" placeholder="Password" required="" type="password" id="l_pass" name="l_pass">
	                        </div>
	                        <button type="submit" class="btn btn-primary block full-width m-b">Iniciar Sesi&oacute;n</button>

	                        <!--<a href="#">
	                            <small>Forgot password?</small>
	                        </a>

	                        <p class="text-muted text-center">
	                            <small>Do not have an account?</small>
	                        </p>
	                        <a class="btn btn-sm btn-white btn-block" href="http://webapplayers.com/inspinia_admin-v1.9.2/register.html">Create an account</a>-->
	                    </form>
	                    <p class="m-t">
	                        <!-- <small>Administrado por la Oficina de Estad&iacute;stica e Inform&aacute;tica © 2014</small> -->
	                        <small>© Técnolgias de la Información SBA 2014 - 2023</small>
	                    </p>
	                </div>
	            </div>
	        </div>
	        <hr>
	        <div class="row">
	            <div class="col-md-6">
	                <!-- Copyright Sociedad de Beneficencia P&uacute;blica de Arequipa -->
	                Copyright Sociedad de Beneficencia Arequipa
	            </div>
	            <div class="col-md-6 text-right">
	               <!-- <small>© 2012-2016</small> -->
	               
	            </div>
	        </div>
	    </div>
		<script type="text/javascript">
		$("#l_user").focus();
		$("#form_login").submit(function(){

			if($("#form_login #l_user").val() == "" || $("#form_login #l_pass").val() == ""){
				$("#msj").show();
				$("#text_content").html("Debe especificar un nombre de Usuario y Contrase&ntilde;a");
				$("#l_user").focus();
				setTimeout(function(){$("#msj").fadeOut();}, 4000);
				return false;
			} else{
				$("#msj").show();
				$("#text_content").html("Verificando datos...");
				setTimeout(function(){$("#msj").fadeOut();}, 2000);
			}

		});
		</script>
	</body>
</html>
