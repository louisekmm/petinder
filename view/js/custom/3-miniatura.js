function preview(img, selection) { 
	var scaleX = $('#thumbnail').data('width') / selection.width; 
	var scaleY = $('#thumbnail').data('height') / selection.height; 
	
	$('#thumb').css({ 
		width: Math.round(scaleX * $('#thumbnail').data('width_original')) + 'px', 
		height: Math.round(scaleY * $('#thumbnail').data('height_original')) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
} 

$(document).ready(function () {

	$('.thumbnail').each(function() {
		var thumb = $(this);
		console.log(parseFloat(parseInt(thumb.data('width'))/parseInt(thumb.data('height'))));
		//console.log(parseFloat(parseInt(thumb.data('height'))/parseInt(thumb.data('width'))));
		thumb.imgAreaSelect({
		
			x1: 0,
			y1: 0,
			x2: thumb.data('x2'),
			y2: thumb.data('y2'),
			aspectRatio: parseFloat(parseInt(thumb.data('width'))/parseInt(thumb.data('height')))+':1',
			imageHeight: thumb.data('height_original'),
			imageWidth:thumb.data('width_original'),
			onSelectChange: function(img, selection) { 
				var scaleX = thumb.data('width') / selection.width; 
				var scaleY = thumb.data('height') / selection.height; 
				//console.log(selection.height);
				/* // preview 
				
				$('#thumb').css({ 
					width: Math.round(scaleX * thumb.data('width_original')) + 'px', 
					height: Math.round(scaleY * thumb.data('height_original')) + 'px',
					marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
					marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
				});*/
				var form = thumb.closest('.mae');
				form.find('.x1').val(selection.x1);
				form.find('.y1').val(selection.y1);
				form.find('.x2').val(selection.x2);
				form.find('.y2').val(selection.y2);
				form.find('.w').val(selection.width);
				form.find('.h').val(selection.height);
			
		
			},
			show:true,
			handles:true
		}); 
	});

	
}); 