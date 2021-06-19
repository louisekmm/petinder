/* $(function() {
$("form").validate({
        tooltip_options: {
           '_all': { placement: 'top' }
        }
     });
	 
 });
 */
 
 $.validator.addMethod("requiredOutro", function(value, element) {
	 if (!$(element).parent().find('input[type=radio]').is(':checked')) {
		 
		 return true;
	 }
	 if (!$(element).val()) {
		 return false;
	 }
	return true;
    
}, "Preencha este campo.");

 $.validator.addMethod("data", function(value, element) {
	var dateOfBirth = value;
    var arr_dateText = dateOfBirth.split("/");
    day = arr_dateText[0];
    month = arr_dateText[1];
    year = arr_dateText[2];

 var mydate = new Date();
    mydate.setFullYear(year, month - 1, day);

    var maxDate = new Date();
    maxDate.setYear(maxDate.getYear() - 21);

    if (maxDate < mydate) {
        return false;
    }
    return true;
}, "Você deve ser maior de 21 anos.");

 $.validator.addMethod("repeteSenha", function(value, element) {
	 if ($(element).val() != $('#senha').val()) {
		 
		 return false;
	 } else {
		 return true;
	 }

}, "Senha não confere.");

 $.validator.addMethod("repeteEmail", function(value, element) {
	 if ($(element).val() != $('#email').val()) {
		 
		 return false;
	 } else {
		 return true;
	 }

}, "E-mail não confere.");
 
 
  $.validator.addMethod("repeteCelular", function(value, element) {
	 if ($(element).val() != $('#celular').val()) {
		 
		 return false;
	 } else {
		 return true;
	 }

}, "Celular não confere.");
 
 
 function validaForm(este) {

		if (!$(este).validate({
			ignore:"",
			tooltip_options: {
			   '_all': { placement: 'top' }
			},
			 highlight: function(element, errorClass, validClass) {
				 var pane = $(element).closest('.tab-pane');
				
				if (pane && !pane.data('erro')) {
					pane.data('erro', '1');
					var id = pane.attr('id');
					//console.log($('li[role=presentation] a[href="#'+id+'"]'));
					$('li[role=presentation] a[href="#'+id+'"]').addClass('alertpane');
				}
				
			  },
			  unhighlight: function(element, errorClass, validClass) {
				var p = $(element).closest('.tab-pane');
				if (p && !p.find('.form-control.error').size()) {
					p.data('erro', 0);
					var id = p.attr('id');
					if (id) $('li[role=presentation] a[href="#'+id+'"]').removeClass('alertpane');
				}
			  },
			  
			success: $.noop
		 
		}).form()) return false;
		return true;
	}
	
function updateForeign(current) {
	var atual = $('.foreign-form.active-foreign');
	var sel = atual.parent().siblings('select');
	//var tabela = atual.data("table");
	
	$.get(atual.data("table")+'/foreign_select/'+current+'/', function(data) {
		sel.html(data);
	});
	atual.removeClass('active-foreign');
}
 
 
 $(document).on('click', '.foreign-form', function(e) {
	$('.foreign-form').removeClass('active-foreign');
	$(this).addClass('active-foreign');
 });
 
 $(document).on('click', '.save', function(e) {
	e.preventDefault();
	var f = $(this).closest('form');
	if (validaForm(f)) {
		if ($(this).hasClass('save-ajax')) {
			$.post(f.attr('action')+'?ajax=1', f.serialize(), function(data) {
				var data = jQuery.parseJSON(data);
				if (!data.status) {
					alert(data.message);
				} else {
					$('#foreign_form').modal('toggle');
					updateForeign(data.id);
				}
			});
		} else {
			f.submit();
		}
	}

});


$(document).on('click', '.adicionar-escola', function(e) {
	e.preventDefault();
	var f = $(this).closest('form');
	var t = f.find("table tbody");
	var t2 = f.find('input[type=text]');
	if (validaForm(f)) {
		$.post(f.data('add')+'?ajax=1', f.serialize(), function(data) {
			if (data == '0') {
				alert('Escola inexistente');
			} else {
				t2.val('');
				if (!data) {
					alert('Escola já adicionada');
				} else {
					t.append(data);
				}
			}
		});
	}
});
$(document).on('click', '.limpar-tabela', function(e) {
	e.preventDefault();
	var f = $(this).closest('form').find('table tbody');
	f.html('');
});

 $(document).on('click', '.btn-submit', function(e) {
	e.preventDefault();
	var f = $(this).closest('form');
	if (validaForm(f)) {

		$.post('cadastro/basico/'+'?ajax=1', f.serialize(), function(data) {
			if (data == 1) {
				$('#modal1').modal('hide')
				//alert('Cadastro realizado com sucesso!');
				//window.location = '';
				$('#modal2').modal('show')
			} else {
				//alert(f.serialize());
				alert(data);
			}
		});
	}
});


 $(document).on('click', '.cadastro-proximo1', function(e) {
	e.preventDefault();
	var f = $(this).closest('form');
	if (validaForm(f)) {
		$.post(f.attr('action')+'?ajax=1', f.serialize(), function(data) {
			if (data == 1) {
				$('.form1, .form3').hide();
				$('.step').removeClass("active-step");
				$('.form2').show();
				$('.step').eq(1).addClass("active-step");
			} else {
				alert(data);
			}
		});
	}
});

 $(document).on('click', '.cadastro-proximo2', function(e) {
	e.preventDefault();
	var f = $(this).closest('form');
	var f2 = $('form');
	var table = f.find("table tbody tr");
	if (!table.length) {
		alert('Adicione alguma escola');
	} else {
		$.post('cadastro/turmas/'+'?ajax=1', f2.serialize(), function(data) {
			if (data) {
				$('.form1, .form2').hide();
				$('.step').removeClass("active-step");
				$('.step').eq(2).addClass("active-step");
				$('.form3').show();
				
				$('.turmas-table').html(data);
			} else {
				alert('Erro');
			}
		});
		
	}
	
});

$(document).on('click', '.cadastro-anterior2', function(e) {
	e.preventDefault();
	$('.form1, .form2, .form3').hide();
	$('.step').removeClass("active-step");
	$('.step').eq(0).addClass("active-step");
	$('.form1').show();
	
});

$(document).on('click', '.cadastro-anterior3', function(e) {
	e.preventDefault();
	$('.form1, .form2, .form3').hide();
	$('.step').removeClass("active-step");
	$('.step').eq(1).addClass("active-step");
	$('.form2').show();
	
});
 $(document).on('click', '.cadastro-finalizar', function(e) {
	e.preventDefault();
	var f = $('form');
	
		$.post('cadastro/finalizar/'+'?ajax=1', f.serialize(), function(data) {
			if (data == 1) {
				$('.form1, .form2, .form3').hide();
				//alert('Cadastro realizado com sucesso!');
				//window.location = '';
				$('.form4').show();
			} else {
				alert(data);
			}
		});
	
});
 
 $(document).on('click', '.remove-item', function(e) {
	e.preventDefault();
	var link = $(this).attr('href');
	if (confirm(unescape('Tem certeza que deseja excluir?'))) {
		$.get(link, function(data) {
			window.location.reload();
		});
	}
 });
 


$(document).on('click', '.btn-login', function(e) {
	e.preventDefault();
	var $btn = $(this);
	var form = $(this).closest('form');

	if (form.valid()) {
		$btn.button('loading');
		$.post(form.attr('action'), form.serialize(), function(data) {
			if (data == 1) {
				window.location.href = form.data('redirect');
			} else {
				$('.warnings').html(data);
				$btn.button('reset');
			}
			
		});
	}
	
});

$(document).on('click', '.btn-esqueci', function(e) {
	e.preventDefault();
	var $btn = $(this);
	var form = $(this).closest('form');

	if (form.valid()) {
		$btn.button('loading');
		$.post(form.attr('action'), form.serialize(), function(data) {
			$('.warnings').html(data);
			$btn.button('reset');
			
		});
	}
	
});
$(document).on('click', '.btn-recuperar', function(e) {
	e.preventDefault();
	var $btn = $(this);
	var form = $(this).closest('form');

	if (form.valid()) {
		$btn.button('loading');
		$.post(form.attr('action'), form.serialize(), function(data) {
			if (data == 1) {
				window.location.href = form.data('redirect');
			} else {
				$('.warnings').html(data);
				$btn.button('reset');
			}
			
		});
	}
	
});

$(document).on('click', '.btn-order', function(e) {
		e.preventDefault();
		$.post($(this).attr('href'), $('.ordem').serialize(), function(data) {
			location.reload();
		});
		return false;
	});

 $(function () {
$("table tbody.sort").sortable({ handle: ".handle", cursor: "pointer" });
	// $( "table tbody.sort" ).disableSelection();
	$("table tbody.sort" ).bind( "sortupdate", function(event, ui) {
		$('.btn-order').removeClass("disabled");
		var lowest = 99999;
		$('.ordem').each(function() {
			if ($(this).val() < lowest) {
				lowest = $(this).val();
			}
		});
		
		$('.ordem').each(function() {
			$(this).val(lowest);
			lowest = parseInt(lowest) + 1;
		});

	});
	

 });
 

 
 var x_geral = 99;
 $(document).on('click', '.novo_multi', function(e) {
 e.preventDefault();
 var pai = $(this).closest('fieldset');
 
 
 var modelo = pai.find('.modelo').clone(false, false).removeClass('modelo');
 newElement(modelo);
 modelo.find(".noid").remove();
 modelo.find('input, select, textarea').each(function() {
	var id = (1+Math.random()).toString();
	id = id.replace('.', '');
	$(this).removeClass("error");
	$(this).attr('id', id).val('');
	var name = $(this).attr('name');
	
	name = name.replace('[0]', '['+x_geral+']');
	$(this).attr('name', name);
	
	$(this).parent().find('label').attr('for', id);
	$(this).parent().find('.tooltip').remove();
 });
 x_geral++;
 pai.closest('.repositorio').append(modelo);
 
 });
 
 $(document).on('click', '.remover', function(e) {
	e.preventDefault();
	$(this).closest('.pai').remove();
 });
 
 function applyColor(element) {
	if (element) {
		element.colorpicker({
		 format:'hex',
		 }).on('changeColor.colorpicker', function(event){
		  $(this).css('background-color', event.color.toHex());
		}).on('create.colorpicker', function(event){
		  $(this).css('background-color', $(this).val());
		});
	}
}
 
 $('.color').each(function() {
	applyColor($(this));
 });
 
 
 
 function newElement(element) {
	applyColor(element.find('.color'));
	applyMasks(element);
 }
 
 function applyMasks(element) {
	if (element) {
		element.find('.cpf').inputmask({ mask: function () { return ["999.999.999-99"]; }, placeholder:' ', clearIncomplete: true, showMaskOnHover:false});
		element.find('.hora').inputmask({ mask: function () { return ["99:99"]; }, placeholder:'', clearIncomplete: true, showMaskOnHover:false});
		element.find('.rg').inputmask({ mask: function () { return ["99.999.999-*"]; }, placeholder:' ', clearIncomplete: true, showMaskOnHover:false});
		element.find('.cnpj').inputmask({ mask: function () { return ["99.999.999/9999-99"]; }, placeholder:' ', clearIncomplete: true, showMaskOnHover:false});
		element.find('.cep').inputmask({ mask: function () { return ["99999-999"]; }, placeholder:' ', clearIncomplete: true, showMaskOnHover:false});
		element.find('.telefone').inputmask({ mask: function () { return ["(99) 9999-9999", "(99) 99999-9999"]; }, placeholder:' ', clearIncomplete: true, showMaskOnHover:false});
		element.find('.data').inputmask({ mask: function () { return ["99/99/9999"]; }, placeholder:' ', clearIncomplete: true, showMaskOnHover:false});
		element.find('.data').datepicker({dateFormat: 'dd/mm/yy'});
		element.find('.number').inputmask({ mask: function () { return ["9{0,10}"]; }, placeholder:' ', clearIncomplete: true, showMaskOnHover:false});
		//element.find('.perc').inputmask({ mask: function () { return ["9{1,2}"]; }, placeholder:' ', suffix:'%', autoUnmask:true, removeMaskOnSubmit:true, clearIncomplete: true, showMaskOnHover:false});
	}
 }
 
 $(function() {
	applyMasks($('body'));
 });
 
  $(function () {
  //$('[data-toggle="tooltip"]').tooltip();
  
  
  $('body').tooltip({  selector: '[data-toggle="tooltip"]'});
  
})
 
 
 $('textarea.rich').css('height', '0').css('padding', '0').show();
 
 
 $('.nav-tabs a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})

$.validator.addMethod("hora", function(value, element) {
    if (value && !/^\d{2}:\d{2}$/.test(value)) return false;
    var parts = value.split(':');
	
    if (parts[0] > 23 || parts[1] > 59) return false;
    return true;
}, "Hora inválida.");



$('#foreign_form').on('shown.bs.modal', function (e) {
	applyMasks($('#foreign_form'));
})

$('#foreign_form').on('hidden.bs.modal', function () {
  $(this).removeData('bs.modal');
});

$(document).on('click', '.inline-edit .pencil', function(e) {
	e.preventDefault();
	var pai = $(this).closest('.inline-edit');
	pai.find('.pencil, .value').hide();
	pai.find('.form').show();
});

$(document).on('click', '.inline-edit .form .btn-save-inline', function(e) {
	e.preventDefault();
	var pai = $(this).closest('.inline-edit');
	var formu = pai.find('.form');
	pai.css('opacity', '.5');
	$.post(pai.find('.controller').val()+'/inline_edit/', formu.find("form").serialize(), function(data) {
		pai.find('.pencil, .value').css('display', '');
		formu.hide();
		var el = pai.find('.form .input').children().first();
		var valor = '';
		
		if (el.is('select')) {
			valor = el.find('option:selected').text()
		}
		
		if (el.is('input[type=checkbox]')) {
			valor = (el.is(':checked') ? 'Sim' : 'Não');
		}
		
		if (el.is('input[type=text]')) {
			valor = el.val();
		}
		
		pai.find('.value').text(valor);
		pai.css('opacity', '1');
	});
	
});

$(document).on('click', '.gera_chave', function(e) {
	e.preventDefault();
	if (confirm('Tem certeza que deseja gerar as chaves do torneio?')) {
		$.get($(this).attr('href'), function(data) {
			if (data == 1) {
				alert('Chaves criadas com sucesso!');
			} else {
				alert('Possível erro ao criar chaves. Verifique.');
			}
		});
	}
});

$(document).on('click', '.confirm', function(e) {
	if (!confirm('Tem certeza?')) {
		e.preventDefault();
	}
});


$(document).on('click', '.print', function(e) {
	e.preventDefault();
	window.print();
});


$(document).on('change', '.onchange', function(e) {
	var table = $(this).data('change-table');
	var current_field = $(this).data('name');
	var foreign_field = $(this).data('change-field');
	var skip = $(this).data('skip');
	
	var value = $(this).val();
	if (skip) {
		value = '';
	}
	var atual = $(this);
	$.get(table+'/foreign_select/'+current_field+'/'+value+'/', function(data) {
		var f = atual.closest('fieldset').find('[data-name='+foreign_field+']');
		f.html(data);
		if (f.data('value')) {
			f.val(f.data('value'));
		}
		
		f.trigger('change');
	});
});

$(function() {
	$('.onchange').trigger('change');
});
function enviaMsg() {
	
	var f = $('#formchat');
	if (!validaForm(f)) {
		return;
	}
	$.post('servico/chat_post/', f.serialize(), function(data) {
		f.find('.texto').val('');
		updateChat();
	});
}


$(document).on('click', '.load-avaliadores', function(e) {
	e.preventDefault();
	var id = $(this).data("id");
	var re = $(this).parent().find('.results-avaliadores');
	if (re.html()) {
		re.html('');
	} else {
		$.post('acompanhamento/load_avaliadores/', {id:id}, function(data) {
			re.html(data);
		});
	}
});
$(document).on('click', '.envia-msg', function(e) {
	e.preventDefault();
	enviaMsg();
});


function updateChatTimer() {
	setTimeout(updateChatTimer, 5000);
	updateChat();
}
if ($('#chat').size()) {
	setTimeout(updateChatTimer, 5000);
}



$('.rating1').rating({
  extendSymbol: function (rate) {
    $(this).tooltip({
      container: 'body',
      placement: 'bottom',
      title: titulos[rate-1]
    });
	
	 $(this).on('rating.change', function (e, rate) {
      alert(rate);
    })
  }
});


$(document).on('click', '.btn-excluir-evento', function(e) {
	e.preventDefault();
	var f = $(this).closest('.modal').find('form');
	
	var id = f.find("#id").val();
	
	if (id && confirm('Tem certeza?')) {
		$.post($('#page-wrapper').data('del'), {id:id}, function(r) {
			if (r == 1) {
				window.location.reload();
			}
		});
	}
});

$(document).on('click', '.btn-salvar-evento', function(e) {
	e.preventDefault();
	var f = $(this).closest('.modal').find('form');
	
	if (validaForm(f)) {
		var evTitle = $('#event_title').val();
		var evDesc = $('#event_description').val();
		var evColor = $('#event_color').val();
		var evTurma = (JSON.stringify($('#multiple').val()));
		var evHora = $('#hora').val();
		var evId = $('#id').val();
		var evData = $('#data').val();
		var evMinuto = $('#minuto').val();
		var evEscola = $('#page-wrapper').data('escola');
		//var evTurma = 'a';
		if (evTitle) {
			eventData = {
				nome: evTitle,
				data: evData,
				id: evId,
				//end: end,
				descricao: evDesc,
				escola: evEscola,
				cor: evColor,
				turma: evTurma,
				hora: evHora+':'+evMinuto,
				
			};
			//console.log(eventData);
			$.ajax({
				type: 'POST',
				url: $('#page-wrapper').data('add'),
				data: eventData,
				success: function (result) {
					//console.log(result);
					if (result == 1) {
						window.location.reload();
					}
					 /*if (result.success) {
						eventData.id = result.guidEscEvt;
						eventData.allDay = true;
						//console.log(result.novoseventos);
						//$('#calendario-edit').fullCalendar('rerenderEvents', result.novoseventos, true);
						$('#calendario-edit').fullCalendar('addEventSource', result.novoseventos, true);
						//msgCallBack.show(result.message);
					} else {
						//msgCallBack.show(result.message);
					}*/
				}
			});
		}
	}
});




$('.clearmenu').closest('li').hide();