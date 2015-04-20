$(document).ready(function(){

	//get Therapeutic Area list
	getTherapeuticAreaList();

	//get msl list
	getMslList();

	// therapeutic area add/edit action
	$('#therapeutic_area_add').click(function() {
		var s_therapeutic_area_name = $('#txt_therapeutic_area').val();
		if(s_therapeutic_area_name == '') {

			$('.alert-message').html('Please enter the therapeutic area name');
			$('#popupCommonAlert').modal('show');

		} else {

			var frm_serialize_data = $( "#frm_therapeutic_area" ).serialize();
			// console.log(frm_serialize_data);
			$.ajax({
				type:"POST",
				url: "/therapeutic_area_add_edit",
				data:frm_serialize_data,
				success:function (callback) {
					try {
						if (callback.s_status != 'success') {
							$('.alert-message').html(callback.data);
							$('#popupCommonAlert').modal('show');
						} else {
							$('.success-message').html(callback.data);
							$('#popupCommonMessage').modal('show');
							$('#frm_therapeutic_area').trigger("reset");
							getTherapeuticAreaList();
						}

					} catch (s_error) {
						$('.alert-message').html(callback.data);
						$('#popupCommonAlert').modal('show');
					}
				}
			});
		}
		return false;
	});

	//open edit therapeutic area popup
	$(document).on('click','.therapeutic_area_edit',function(e){
		$('#hid_therapeutic_area_id').val($(this).attr('therapeutic_area_id'));
		$('#txt_edit_therapeutic_area').val($(this).attr('therapeutic_area_name'));
		$('#hid_edit_therapeutic_area').val($(this).attr('therapeutic_area_name'));
		$('.edit-therapeutic-area-alert-msg').addClass('hide');
		$('.edit-therapeutic-area-response-msg').html('');
		$('#popupEditTherapeuticArea').modal('show');
		return false;
	});

	//edit therapeutic area
	$('.btn_edit_therapeutic_area').click(function() {
		var frm_serialize_data = $( "#frm_edit_therapeutic_area" ).serialize();
		// console.log(frm_serialize_data);
		$.ajax({
			type:"POST",
			url: "/therapeutic_area_add_edit",
			data:frm_serialize_data,
			success:function (callback) {
				try {
					if (callback.s_status != 'success') {
						$('.edit-therapeutic-area-response-msg').html(callback.data);
						$('.edit-therapeutic-area-alert-msg').removeClass('hide');
					} else {
						$('#popupEditTherapeuticArea').modal('hide');	
						getTherapeuticAreaList();
					}

				} catch (s_error) {
					$('.edit-therapeutic-area-response-msg').html(callback.data);
					$('.edit-therapeutic-area-alert-msg').removeClass('hide');
				}
			}
		});
		return false;
	});	

	// msl add action
	$('#msl_add').click(function() {

		//blank the edit id
		$('#hid_msl_id').val(0);

		var s_msl_first_name 	= $.trim($('#txt_first_name').val());
		var s_msl_last_name 	= $.trim($('#txt_last_name').val());
		var s_msl_email 		= $.trim($('#txt_email').val());
		if(s_msl_first_name == '') {
			$('.alert-message').html('Please enter the first name');
			$('#popupCommonAlert').modal('show');
		} else if(s_msl_last_name == '') {
			$('.alert-message').html('Please enter the last name');
			$('#popupCommonAlert').modal('show');
		} else if(s_msl_email == '') {
			$('.alert-message').html('Please enter the email');
			$('#popupCommonAlert').modal('show');
		} else {
			var frm_serialize_data = $( "#frm_msl" ).serialize();
			// console.log(frm_serialize_data);
			$.ajax({
				type:"POST",
				url: "/msl_add_edit",
				data:frm_serialize_data,
				success:function (callback) {
					try {
						if (callback.s_status != 'success') {
							$('.alert-message').html(callback.data);
							$('#popupCommonAlert').modal('show');
						} else {
							$('.success-message').html(callback.data);
							$('#popupCommonMessage').modal('show');
							$('#frm_msl').trigger("reset");
							getMslList();
						}

					} catch (s_error) {
						$('.alert-message').html(callback.data);
						$('#popupCommonAlert').modal('show');
					}
				}
			});
		}
		return false;
	});

	//open edit msl popup
	$(document).on('click','.msl_edit',function(e){

		$('.edit-msl-alert-msg').addClass('hide');
		$('.edit-msl-response-msg').html('');

		var form_data 	= new Object;
		form_data.msl_id = $(this).attr('msl_id');

		$.ajax({
			type:"POST",
			url: "/msl_get",
			data:form_data,
			success:function (callback) {
				try {
					if (callback.s_status != 'success') {
						$('.alert-message').html(callback.data);
						$('#popupCommonAlert').modal('show');
					} else {
						$('#msl_form_edit_div').html(callback.data);
						$('#popupEditMsl').modal('show');
					}

				} catch (s_error) {
					$('.alert-message').html(callback.data);
					$('#popupCommonAlert').modal('show');
				}
			}
		});
		return false;
	});

	// open delete msl popup
	$('.btn_delete_msl').click(function() {

		$('.delete-message').html("Are you sure, do you want to delete this user?");
		$('#popupCommonDelete').modal('show');

	});

	//delete msl user
	$('.delete_entity').click(function() {
		//alert($('#hid_msl_id').val());

		var form_data 	= new Object;
		form_data.msl_id = $('#hid_msl_id').val();

		$.ajax({
			type:"POST",
			url: "/msl_delete",
			data:form_data,
			success:function (callback) {
				try {
					if (callback.s_status != 'success') {
						$('.alert-message').html(callback.data);
						$('#popupCommonAlert').modal('show');
					} else {
						$('#popupCommonDelete').modal('hide');
						$('#popupEditMsl').modal('hide');
						getMslList();
					}

				} catch (s_error) {
					$('.alert-message').html(callback.data);
					$('#popupCommonAlert').modal('show');
				}
			}
		});
	});

	// msl edit action
	$('.btn_save_msl').click(function() {
		$('.edit-msl-alert-msg').addClass('hide');
		$('.edit-msl-response-msg').html('');
		var s_msl_edit_first_name 	= $.trim($('#txt_edit_first_name').val());
		var s_msl_edit_last_name 	= $.trim($('#txt_edit_last_name').val());
		var s_msl_edit_email 		= $.trim($('#txt_edit_email').val());
		if(s_msl_edit_first_name == '') {
			$('.edit-msl-alert-msg').removeClass('hide');
			$('.edit-msl-response-msg').html('Please enter the first name');
		} else if(s_msl_edit_last_name == '') {
			$('.edit-msl-alert-msg').removeClass('hide');
			$('.edit-msl-response-msg').html('Please enter the last name');	
		} else if(s_msl_edit_email == '') {
			$('.edit-msl-alert-msg').removeClass('hide');
			$('.edit-msl-response-msg').html('Please enter the email');
		} else {
			var frm_serialize_data = $( "#frm_edit_msl" ).serialize();
			$.ajax({
				type:"POST",
				url: "/msl_add_edit",
				data:frm_serialize_data,
				success:function (callback) {
					try {
						if (callback.s_status != 'success') {
							$('.edit-msl-alert-msg').removeClass('hide');
							$('.edit-msl-response-msg').html(callback.data);
						} else {
							$('#msl_form_edit_div').html('');
							$('#popupEditMsl').modal('hide');
							getMslList();
						}

					} catch (s_error) {
						$('.edit-msl-alert-msg').removeClass('hide');
						$('.edit-msl-response-msg').html(callback.data);
					}
				}
			});
		}
		return false;
	});	
});

/*
* Get Therapeutic Area list
*/
function getTherapeuticAreaList() {
	$.ajax({
		type:"POST",
		url: "/therapeutic_area_list",
		data:'',
		success:function (callback) {
			try {
				
				if (callback.s_status != 'success') {
					$('.alert-message').html(callback.data);
					$('#popupCommonAlert').modal('show');
				} else {
					if(callback.i_record >= 8) {
						$('#therapeutic_area_list_div').addClass('scroll-table');
					} else {
						$('#therapeutic_area_list_div').addClass('no-scroll-table');
					}
					$('#table_therapeutic_area_list').html(callback.data);
				}

			} catch (s_error) {
				$('.alert-message').html(callback.data);
				$('#popupCommonAlert').modal('show');
			}
		}
	});
	return false;
}

/*
* Get MSL list
*/
function getMslList() {
	$.ajax({
		type:"POST",
		url: "/msl_list",
		data:'',
		success:function (callback) {
			try {
				
				if (callback.s_status != 'success') {
					$('.alert-message').html(callback.data);
					$('#popupCommonAlert').modal('show');
				} else {
					if(callback.i_record >= 8) {
						$('#msl_list_div').addClass('scroll-table');
					} else {
						$('#msl_list_div').addClass('no-scroll-table');
					}
					$('#table_msl_list').html(callback.data);
				}

			} catch (s_error) {
				$('.alert-message').html(callback.data);
				$('#popupCommonAlert').modal('show');
			}
		}
	});
	return false;
}