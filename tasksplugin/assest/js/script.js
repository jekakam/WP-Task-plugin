jQuery(document).ready(function() {
   
	localStorage.reload = 0;
		
	// добавим задачу
	jQuery("body").on('click', '.addtask', function(){
		$.magnificPopup.open({
			items: {
				src: '#addform',
				type: 'inline',	
			},
			callbacks: {
				beforeClose: function() {
					jQuery('.msg').empty();
					ajaxupatetask();
				}
			}
		});
    });
	jQuery("body").on("click","#add", function(e){
		e.preventDefault()
		let data =	jQuery('#adds').serialize();
		let url = ajax.url;
		jQuery.ajax({
			url	: url,
			type: 'POST',
			data: data,
			beforeSend: function( xhr ) {},
			success: function( data ) {
				if(data) {
					jQuery('.msg').empty().html('<div class="notice notice-info"><p>'+data+'</p></div>');
					if (data != 'Заполните все поля') {
						localStorage.reload = 1;
						setTimeout(function(){
							$.magnificPopup.close();
						}, 1500);
					}
				} 
			},
			error: function (jqXHR, text, error) {
                jQuery('.msg').html('<div class="notice notice-error"><p>'+error+'</p></div>');
            }
		});
	});
	
	// редактируем задачу 
	jQuery('body').on('click','.edittask',function(){
		let id  = jQuery(this).attr('data-id');
		let row = jQuery('[data-id="id'+id+'"]').closest('tr')
		let done = row.find('.done').text();
		jQuery('[name="done"] option:contains("'+done+'")').prop('selected', true);
		jQuery('[name="ids"]').val(id);  
		jQuery('#edit-popup [name="name"]').val(row.find('.name').text());
		jQuery('#edit-popup [name="description"]').val(row.find('.desc').text());
		jQuery('#edit-popup [name="datetime"]').val(row.find('.date').text());	
		
		$.magnificPopup.open({
			items: {
				src: '#edit-popup',
				type: 'inline',	
			},
			callbacks: {
				beforeClose: function() {
					ajaxupatetask();
				}
			}
		});
	});
	jQuery('body').on('click', "#edit", function(e){
		e.preventDefault()
		let data =	jQuery('#editform').serialize();
		let url = ajax.url;
		jQuery.ajax({
			url	: url,
			type: 'POST',
			data: data,
			beforeSend: function( xhr ) {},
			success: function( data ) {
				if(data) {
					jQuery('.msg').empty().html('<div class="notice notice-info"><p>'+data+'</p></div>');
					if (data != 'Заполните все поля') {
						localStorage.reload = 1;
						setTimeout(function(){
							$.magnificPopup.close();
						}, 1500);
					}	
				} 
			},
			error: function (jqXHR, text, error) {
                jQuery('.msg').html('<div class="notice notice-error"><p>'+error+'</p></div>');
            }
		});
	
	
	});
	
	// удалим задачу
	jQuery('body').on('click','.deltask',function(){
		let id  = jQuery(this).attr('data-id');
		let url = ajax.url;
		jQuery.ajax({
			url	: url,
			type: 'POST',
			data: {
				'action': 'detetetask',
				'ids': id,
				},
			beforeSend: function( xhr ) {},
			success: function( data ) {
				if(data) {
					localStorage.reload = 1;
					ajaxupatetask();
				} 
			}
		});
	});

});

function ajaxupatetask(){
	let q = localStorage.reload;
	if (q == 0) return;
	let url = ajax.url;
	jQuery.ajax({
		url	: url,
		type: 'POST',
		data: {'action': 'reload'},
		beforeSend: function( xhr ) {},
		success: function( data ) {
			if(data) {
				jQuery('#t_list').replaceWith(data);
				localStorage.reload = 0;
			} 
		},
		error: function (jqXHR, text, error) {}
	});
	
	
	
}















