$(document).on('change', 'select[name=idTipo_Pergunta]', function() {
	hideshow();
});

function hideshow() {
	var a = $('select[name=idTipo_Pergunta]');
	if (a.val() == '2') {
		$('[name=numerico], [name=telefone], [name=email]').parent().parent().hide();
		$('[name=opcao1], [name=opcao2], [name=opcao3], [name=opcao4], [name=opcao5], [name=opcao6], [name=opcao7], [name=opcao8], [name=opcao9], [name=opcao10], [name=outro]').parent().parent().show();
	} else {
		$('[name=numerico], [name=telefone], [name=email]').parent().parent().show();
		$('[name=opcao1], [name=opcao2], [name=opcao3], [name=opcao4], [name=opcao5], [name=opcao6], [name=opcao7], [name=opcao8], [name=opcao9], [name=opcao10], [name=outro]').parent().parent().hide();
	}
}

hideshow();


$(document).on('change', '[data-name=idAdmin_Tipo]', function() {
	updateTipo(this);
});

function updateTipo(t) {
	var v = $(t).val();
	
	var p = $(t).closest('fieldset').find('.nav-tabs');
	
	if (v == 5) {
		p.find('li').eq(1).show();
	} else {
		p.find('li').eq(1).hide();
	}
}
updateTipo($('[data-name=idAdmin_Tipo]'));