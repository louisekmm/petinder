</div>

<?php
	include_once 'modal_add.tpl';
	global $di;
	$idioma = LoginController::getIdioma($di);

?>

	
	<script src="view/js/custom/0-jquery.min.js"></script>
	<script src="view/js/custom/jquery-1.11.1.min.js"></script>
	
	<script src="view/js/custom/bootstrap.min.js"></script>
	<script src="view/js/custom/1-bootstrap-colorpicker.min.js"></script>
	<script src="view/js/custom/1-jquery.imgareaselect.min.js"></script>
	<script src="view/js/custom/1-jquery-ui.min.js"></script>
	<script src="view/js/custom/1-moment.min.js"></script>
	<script src="view/js/custom/2-fullcalendar.min.js"></script>
	<script src="view/js/custom/2-jquery.input.mask.min.js"></script>
	<script src="view/js/custom/2-jquery.validate.min.js"></script>
	<script src="view/js/custom/2-menu.js"></script>
	<script src="view/js/custom/3-fullcalendar-pt-br.js"></script>
	<script src="view/js/custom/3-jquery-validate.bootstrap-tooltip.min.js"></script>
	<script src="view/js/custom/3-messages_pt_BR.min.js"></script>
	<script src="view/js/custom/3-miniatura.js"></script>
	<script src="view/js/custom/4-bootstrap-rating.min.js"></script>
	<script src="view/js/custom/5-default.js"></script>
	<script src="view/js/custom/5-especifico.js"></script>


	<script src="view/js/custom/owl.carousel.min.js"></script>
	<script src="view/js/custom/wow.min.js"></script>
	<script src="view/js/custom/typewriter.js"></script>
	<script src="view/js/custom/jquery.onepagenav.js"></script>
	<script src="view/js/custom/main.js"></script>
	
	<script src="view/js/ckeditor/ckeditor.js"></script>
	<script src="view/js/ckeditor/adapters/jquery.js"></script>
	<script>
	 $('textarea.rich').ckeditor({
		 filebrowserUploadUrl: $('base').attr('href')+'fotoadmin/upload/',
		 language: '<?=$idioma?>'
	 }, function() {
		 $('textarea.rich').css('height', '0').css('padding', '0').show();
		 
	 });
	 
	 $(document).ready(function() {
			if ($('#calendar').size()) {
				/*var unidade = encodeURIComponent($('#unidade').val());
				var sala = encodeURIComponent($('#sala').val());
				var professor = encodeURIComponent($('#professor').val());*/
				$('#calendar').fullCalendar({
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek,agendaDay'
					},
					defaultDate: '<?=date('Y-m-d')?>',
					editable: false,
					selectable: true,
					eventStartEditable: false,
					selectHelper: true,
					lang: '<?=$idioma?>',
					eventLimit: true, // allow "more" link when too many events
					events: {
						url: $('#page-wrapper').data('lista')+$('#page-wrapper').data('escola')+'/',
						error: function() {
							//$('#script-warning').show();
						}
					},
					
					select: function (start, end) {
						var modal = $('#modalevento');
						modal.find('.modal-title').text('<?=Meta::getLangFile('agendamento_footer', $di)?> ' + start.format('MM/DD/YYYY'));
						modal.find(".btn-excluir-evento").hide();
						modal.find('.modal-body').html('<div class="row">  ' +
							  '<div class="col-md-12"> ' +

							  '<form class="form-horizontal"> ' +
							  '<input type="hidden" name="data" id="data" value=""> ' +
							  '<input type="hidden" name="id" id="id" value="0"> ' +
													'<div class="form-group"> ' +
							  '<label class="col-md-4 control-label" for="name"><?=Meta::getLangFile('hora_footer', $di)?></label> ' +
							  '<div class="col-md-2"> ' +
							  '<select id="hora" class="form-control required">' +
							  '<option value=""></option>' +
							  '<option value="05">05</option>' +
							  '<option value="06">06</option>' +
							  '<option value="07">07</option>' +
							  '<option value="08">08</option>' +
							  '<option value="09">09</option>' +
							  '<option value="10">10</option>' +
							  '<option value="11">11</option>' +
							  '<option value="12">12</option>' +
							  '<option value="13">13</option>' +
							  '<option value="14">14</option>' +
							  '<option value="15">15</option>' +
							  '<option value="16">16</option>' +
							  '<option value="17">17</option>' +
							  '<option value="18">18</option>' +
							  '<option value="19">19</option>' +
							  '<option value="20">20</option>' +
							  '<option value="22">22</option>' +
							  '<option value="23">23</option>' +
							  '</select>' +
							  '</div> ' +
							  '<div class="col-md-2"> ' +
							  '<select id="minuto" class="form-control required">' +
							  '<option value=""></option>' +
							  '<option value="00">00</option>' +
							  '<option value="05">05</option>' +
							  '<option value="10">10</option>' +
							  '<option value="15">15</option>' +
							  '<option value="20">20</option>' +
							  '<option value="25">25</option>' +
							  '<option value="30">30</option>' +
							  '<option value="35">35</option>' +
							  '<option value="40">40</option>' +
							  '<option value="45">45</option>' +
							  '<option value="50">50</option>' +
							  '<option value="55">55</option>' +
							  '</select>' +
							  '</div> ' +
							  '</div> ' +

							  '<div class="form-group"> ' +
							  '<label class="col-md-4 control-label" for="name"><?=Meta::getLangFile('titulo_footer', $di)?></label> ' +
							  '<div class="col-md-6"> ' +
							  '<input id="event_title" name="evtitle" maxlength="20" required="" type="text" value="" placeholder="" class="form-control input-md"> ' +
							  '</div> ' +
							  '</div> ' +
							  '<div class="form-group"> ' +
							  '<label class="col-md-4 control-label" for="name"><?=Meta::getLangFile('descricao_footer', $di)?></label> ' +
							  '<div class="col-md-6"> ' +
							  '<input id="event_description" name="evdesc" maxlength="80" required="" value="" type="text" placeholder="" class="form-control input-md"> ' +
							  '</div>' +
							  '</div> ' +
							  '<div class="form-group">' +
							  '<label class="col-md-4 control-label" for "name"><?=Meta::getLangFile('NAMEPLURAL_entidade_agrupamento', $di)?></label>' +
							  '<div class="col-md-6">' +
							  '<select id="multiple" name="evturma" class="form-control select2-multiple ajax-select2"  style="width: 100%" multiple>' +
							  '</select>' +
							  '</div>' +
							  '</div> ' +
							  '<div class="form-group"> ' +
							  '<label class="col-md-4 control-label" for="name"><?=Meta::getLangFile('cor_footer', $di)?></label> ' +
							  '<div class="col-md-3"> ' +
							  '<select name="event_color" id="event_color" class="form-control"><option value="#000"><?=Meta::getLangFile('preto_footer', $di)?></option><option value="#ff0000"><?=Meta::getLangFile('vermelho_footer', $di)?></option><option value="#00ff00"><?=Meta::getLangFile('verde_footer', $di)?></option><option value="#0000ff"><?=Meta::getLangFile('azul_footer', $di)?></option></select>' +
							  '</div> ' +
							  '</div> ' +
							  '<div class="form-group"> ' +
							  '<label class="col-md-4 control-label" for="name"><span style="color:red;">*</span><?=Meta::getLangFile('enviar_footer', $di)?></label> ' +
							  '<div class="col-md-6"> ' +
							  '<select name="event_sms" id="event_sms" class="form-control"><option value="1"><?=Meta::getLangFile('um_footer', $di)?></option><option value="7"><?=Meta::getLangFile('uma_footer', $di)?></option><option value="17"><?=Meta::getLangFile('um1_footer', $di)?></option></select>' +
							  '</div> ' +
							  '</div> ' +
							  '<label class="col-md-12 control-label" style="text-align:center;font-weight:normal;color:red;"for="name"><?=Meta::getLangFile('ao_footer', $di)?></label> '+
							  '</form> </div>  </div>');
							  modal.find('#data').val(start.format('YYYY-MM-DD'));
							  modal.find('#multiple > option').remove();
							  var options = $('#agrupamentos > option').clone();
							  modal.find('#multiple').append(options);
							  
						$('#modalevento').modal('toggle');
						

						$.getJSON(window.eventosUrl.getTurmas, function(data) {
							$("#multiple").select2({
									placeholder: "<?=Meta::getLangFile('selecione_footer', $di)?>",
									data: data,
									cache: true
									//escapeMarkup: function (markup) {
									//    return markup;
									//}, // let our custom formatter work
									//minimumInputLength: 1,
									//templateResult: repo, // omitted for brevity, see the source of this page
									//templateSelection: repoSelect // omitted for brevity, see the source of this page
								});
							}
						);

						calMbox.find('.event-color-picker').colorpicker();


						$('#calendario-edit').fullCalendar('unselect');

					},
					eventClick: function (event, element) {
					
							var modal = $('#modalevento');
							modal.find('.modal-title').text('<?=Meta::getLangFile('agendamento_footer', $di)?> ' + event.dia);
							
							modal.find('.modal-body').html('<div class="row">  ' +
							  '<div class="col-md-12"> ' +

							  '<form class="form-horizontal"> ' +
							  '<input type="hidden" name="data" id="data" value=""> ' +
							  '<input type="hidden" name="id" id="id" value="'+event.id+'"> ' +
							  
													'<div class="form-group"> ' +
							  '<label class="col-md-4 control-label" for="name"><?=Meta::getLangFile('hora_footer', $di)?></label> ' +
							  '<div class="col-md-2"> ' +
							  '<select id="hora" class="form-control required">' +
							  '<option value=""></option>' +
							  '<option value="05">05</option>' +
							  '<option value="06">06</option>' +
							  '<option value="07">07</option>' +
							  '<option value="08">08</option>' +
							  '<option value="09">09</option>' +
							  '<option value="10">10</option>' +
							  '<option value="11">11</option>' +
							  '<option value="12">12</option>' +
							  '<option value="13">13</option>' +
							  '<option value="14">14</option>' +
							  '<option value="15">15</option>' +
							  '<option value="16">16</option>' +
							  '<option value="17">17</option>' +
							  '<option value="18">18</option>' +
							  '<option value="19">19</option>' +
							  '<option value="20">20</option>' +
							  '<option value="22">22</option>' +
							  '<option value="23">23</option>' +
							  '</select>' +
							  '</div> ' +
							  '<div class="col-md-2"> ' +
							  '<select id="minuto" class="form-control required">' +
							  '<option value=""></option>' +
							  '<option value="00">00</option>' +
							  '<option value="05">05</option>' +
							  '<option value="10">10</option>' +
							  '<option value="15">15</option>' +
							  '<option value="20">20</option>' +
							  '<option value="25">25</option>' +
							  '<option value="30">30</option>' +
							  '<option value="35">35</option>' +
							  '<option value="40">40</option>' +
							  '<option value="45">45</option>' +
							  '<option value="50">50</option>' +
							  '<option value="55">55</option>' +
							  '</select>' +
							  '</div> ' +
							  '</div> ' +

							  '<div class="form-group"> ' +
							  '<label class="col-md-4 control-label" for="name"><?=Meta::getLangFile('titulo_footer', $di)?></label> ' +
							  '<div class="col-md-6"> ' +
							  '<input id="event_title" name="evtitle" maxlength="20" required="" type="text" value="" placeholder="" class="form-control input-md"> ' +
							  '</div> ' +
							  '</div> ' +
							  '<div class="form-group"> ' +
							  '<label class="col-md-4 control-label" for="name"><?=Meta::getLangFile('descricao_footer', $di)?></label> ' +
							  '<div class="col-md-6"> ' +
							  '<input id="event_description" name="evdesc" maxlength="80" required="" value="" type="text" placeholder="" class="form-control input-md"> ' +
							  '</div>' +
							  '</div> ' +
							  '<div class="form-group">' +
							  '<label class="col-md-4 control-label" for "name"><?=Meta::getLangFile('NAMEPLURAL_entidade_agrupamento', $di)?></label>' +
							  '<div class="col-md-6">' +
							  '<select id="multiple" name="evturma" class="form-control select2-multiple ajax-select2"  style="width: 100%" multiple>' +
							  '</select>' +
							  '</div>' +
							  '</div> ' +
							  '<div class="form-group"> ' +
							  '<label class="col-md-4 control-label" for="name"><?=Meta::getLangFile('cor_footer', $di)?></label> ' +
							  '<div class="col-md-3"> ' +
							  '<select name="event_color" id="event_color" class="form-control"><option value="#000"><?=Meta::getLangFile('preto_footer', $di)?></option><option value="#ff0000"><?=Meta::getLangFile('vermelho_footer', $di)?></option><option value="#00ff00"><?=Meta::getLangFile('verde_footer', $di)?></option><option value="#0000ff"><?=Meta::getLangFile('azul_footer', $di)?></option></select>' +
							  '</div> ' +
							  '</div> ' +
							  '<div class="form-group"> ' +
							  '<label class="col-md-4 control-label" for="name"><span style="color:red;">*</span><?=Meta::getLangFile('enviar_footer', $di)?></label> ' +
							  '<div class="col-md-6"> ' +
							  '<select name="event_sms" id="event_sms" class="form-control"><option value="1"><?=Meta::getLangFile('um_footer', $di)?></option><option value="7"><?=Meta::getLangFile('uma_footer', $di)?></option><option value="17"><?=Meta::getLangFile('um1_footer', $di)?></option></select>' +
							  '</div> ' +
							  '</div> ' +
							  '<label class="col-md-12 control-label" style="text-align:center;font-weight:normal;color:red;"for="name"><?=Meta::getLangFile('ao_footer', $di)?></label> '+
							  '</form> </div>  </div>');
							  //modal.find('#data').val(event.dia2);
							  modal.find('#hora').val(event.horas);
							  modal.find('#minuto').val(event.minutos);
							  modal.find('#event_title').val(event.title);
							  modal.find('#event_description').val(event.descricao);
							  modal.find('#event_color').val(event.color);
							  modal.find('#event_sms').val(event.sms);
							  modal.find(".btn-excluir-evento").show();
							  modal.find('#multiple > option').remove();
							  var options = $('#agrupamentos > option').clone();
							  modal.find('#multiple').append(options);
							  //console.log(event.turmas);
							  var turmas = JSON.parse(event.turmas);
							  var arrayLength = turmas.length;
								for (var i = 0; i < arrayLength; i++) {
									$('#multiple option[value="'+turmas[i]+'"]').attr('selected', true);
									//Do something
								}
						$('#modalevento').modal('toggle');
					
						


					},
					editable: true,
					eventLimit: true, // allow "more" link when too many events
					


					eventDrop: function (event, delta, revertFunc) {
						bootbox.confirm({
							title: "<?=Meta::getLangFile('alteracao_footer', $di)?>",
							message: "<?=Meta::getLangFile('tem_footer', $di)?>",
							buttons: {
								cancel: {
									label: "<?=Meta::getLangFile('cancelar_eventos', $di)?>",
									className: "btn-default"
								},
								confirm: {
									label: "<?=Meta::getLangFile('mover_footer', $di)?>",
									className: "btn-danger"
								}
							},
							callback: function (result) {
								if (result) {
									window.atualizaEvento(event.id, event.start);
								}
								if (!result) {
									revertFunc();
								}
							}
						});
					}
					
					
					
					
					
					
					
				});
			}
			
		});
	</script>
<?php
global $di;
if (LoginController::isSuper($di)) {

?>
	<script>
	CKEDITOR.disableAutoInline = true;

$(document).ready(function() {
	$('.edit').prepend('<span class="editbutton"></span>');
	
	//$('.editable2').wrap('<span class="imgeditable2"></span>');
	var x_img = 0;
	$('.editable').each(function(){
		x_img++;
		$(this).attr('id', 'img'+x_img);
		var width1 = $(this).attr('width');
		var height1 = $(this).attr('height');
		width = 'auto';
		height = 'auto';
		var position = $(this).offset();
		
		var nome = $(this).data('nome');
		
		var id = $(this).data('id');
		//console.log(position);
		$('body').append('<div class="imgeditable" data-for="img'+x_img+'" style="width:'+width+'px;height:'+height+'px;left:'+(position.left+5)+'px;top:'+position.top+'px"><span class="btn"><form enctype="multipart/form-data" method="post" target="'+nome+'" action="admin/fotoadmin/updatestatic"><input type="hidden" name="static" value="'+nome+'"> <input type="hidden" name="width" value="'+width1+'"> <input type="hidden" name="height" value="'+height1+'"> <input type="file" name="img" value="" onChange="envia(this)" /> <a href="#" class="openfile"><?=Meta::getLangFile('editar_cadastrador_completar_widget', $di)?></a><br><a href="<?=URL?>admin/fotoadmin/miniatura/'+id+'/'+width1+'/'+height1+'/" target="_blank" class="miniatura"><?=Meta::getLangFile('miniatura_footer', $di)?></a></form><iframe class="imgupload" id="'+nome+'" name="'+nome+'"></iframe></span></div>');
	});
	$('.editable').hover(function() {
		$('.imgeditable[data-for="'+$(this).attr('id')+'"]').show();
		
	},
	
	function(){
		$('.imgeditable[data-for="'+$(this).attr('id')+'"]').hide();
	});
	
	$('.imgeditable').hover(function(){$(this).show();});
	
	
	$('.imgupload').load(function() {
		var data = jQuery.parseJSON($(this).contents().text());
		var pai = $(this).closest('.imgeditable');
		var id = pai.data('for');
		$('img#'+id).attr('src', data.file);
	  pai.removeClass('loading');
		pai.find('.miniatura').attr('href', 'admin/fotoadmin/miniatura/'+data.idpai+'/'+data.width+'/'+data.height+'/')
    });
});


function envia(este) {
	var atual = $(este);
	
	atual.closest("form").submit();
	atual.closest('.imgeditable').addClass('loading');
}

 
$(document).on('click', '.openfile', function(e) {
	e.preventDefault();
	e.stopPropagation();
	$(this).closest('form').find('input').click();
});

$(document).on('click', 'span.editbutton', function(e) {
	e.preventDefault();
	e.stopPropagation();
	
	var pai = $(this).closest('.edit');
	var remove = '';
	var enter = CKEDITOR.ENTER_P;
	if (!pai.hasClass('rich')) {
		var remove = 'toolbar';
		var enter = CKEDITOR.ENTER_BR;
	}
	pai.attr('contenteditable', true);
	pai.ckeditor({
		removePlugins: remove,
		enterMode: enter,
		filebrowserUploadUrl: $('base').attr('href')+'admin/fotoadmin/upload/'
	}, function() {
		var instance = this;
		this.on('blur',function(e) {
			
			var id = pai.data('id');
			var valor = pai.html();
			var este = pai;
			
			este.addClass('loading');
			$.post('string/update', { id: id, valor: valor }, function(data) {
				este.removeClass('loading');
				
				pai.removeAttr('contenteditable');
				pai.prepend('<span class="editbutton"></span>');
				//instance.destroy();
				
				
			});
			pai.ckeditor(function(){
				this.destroy();
			pai.prepend('<span class="editbutton"></span>');
			pai.attr('contenteditable', '');
			pai.removeClass('cke_focus');
			});
			/*
			pai.ckeditor(function(){
				this.destroy();
				pai.prepend('<span class="editbutton"></span>');
				pai.attr('contenteditable', '');
				pai.removeClass('cke_focus');
			}).off("blur");
			*/
		});
	
	});
	
	
	pai.focus();
});

$(document).on('click', 'a .edit.cke_editable', function(e) {
	e.preventDefault();
});
	</script>
<?php

}
?>
	
	
</body>

</html>
