

<!--
	AUTOR		Cardoso Virginia
	AUTOR		Marzullo Matias
	COPYRIGHT	Noviembre, 2015 - Departamento de Ciencias e Ingeniería de la Computación - UNIVERSIDAD NACIONAL DEL SUR 


-->

<link type="text/css" href="<?php echo base_url('assets/css/examen/generar.css'); ?>" rel="stylesheet" media="screen"/>

<link type="text/css" href="<?php echo base_url('assets/css/select2.css'); ?>" rel="stylesheet" media="screen"/>
<link type="text/css" href="<?php echo base_url('assets/css/select2-bootstrap.css'); ?>" rel="stylesheet" media="screen"/>	
<link type="text/css" href="<?php echo base_url('assets/css/bootstrap-select.css'); ?>" rel="stylesheet" media="screen"/>			

<script type="text/javascript"  src="<?php echo base_url('assets/js/bootstrap-select.js'); ?>"></script> 
<script type="text/javascript"  src="<?php echo base_url('assets/js/bootstrap-select-ES.js'); ?>"></script> 
<script type="text/javascript"  src="<?php echo base_url('assets/js/select2.js'); ?>"></script>
<script type="text/javascript"  src="<?php echo base_url('assets/js/select2_locale_es.js'); ?>"></script>
<script type="text/javascript"  src="<?php echo base_url('assets/js/estadisticas/estadisticas.js'); ?>"></script>

<link type="text/css" href="<?php echo base_url('assets/css/home/index.css'); ?>" rel="stylesheet" media="screen"/>


<div class="div-titulo">
	
		<label>Guías rendidas por el estudiante <?php echo $lu; ?></label>
			
</div>

<div id="div-principal">
	<div class="form-group-generar">	
		
		<div id="div-mostrar" >

		</div>
	</div>
	
	
	
	
	<?php 
		if(isset($info))
			echo '<br/><label id="error-server" class="label-error">'.$info .'</label> ';
	?>
</div>