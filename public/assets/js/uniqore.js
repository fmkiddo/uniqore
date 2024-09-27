$(function () {
	$(document).ready (function () {
		$.base_url = function ($relativePath) {
			return $base_url + '/' + $relativePath;
		};
		
		$.fn.isFormReady = function () {
			if ($(this).is ('form')) {
				$isReady = true;
				$(this).find (':input[required]').each (function () {
					if ($.trim ($(this).val ()).length === 0) {
						$isReady = false;
						return false;
					}
				});
				return $isReady;
			}
		};
		
		$.fn.openChangePasswordDialog = function () {
			if ($(this).prop ('id') === 'pswd-change') {
				$modalPswd	= $('body').find ('#modal-changepassword');
				$form		= $modalPswd.find ('form');
				$userData	= '';
				$divData	= $(this).parents ('.dropend').find ('#data');				
				$divData.find (':input').each (function () {
					$userData	+= $(this).val () + '#';
				});
				$userData	+= $divData.attr ('data-user');
				
				$form.find ('#userdata').val ($userData);
				$form.find ('#uuid').val ($divData.find ('#uuid').val ());
			}
		};
		
		$.fn.loadPropertiesToForm = function () {
			if ($(this).prop ('id') === 'edit-data') {
				$modalForm	= $('body').find ('#modal-form');
				$form		= $modalForm.find ('form');
				$target		= $form.find ('input[name="target"]');
				$divData	= $(this).parents ('.dropend').find ('#data');

				$divData.find ('input').each (function () {
					$dataId = $(this).prop ('id');
					$data	= $(this).val ();
					$formEl	= $form.find ('#' + $dataId);
					
					if (! $formEl.is (':checkbox')) $formEl.val ($data);
					else $formEl.prop ('checked', ($data === 'true'));
					
					if ($dataId === 'email') $('input#cnfmail').val ($data);
				});
				
				$form.find (':input').each (function () {
					if ($(this).not ('[type="hidden"]')) {
						$name = $(this).prop ('name');
						switch ($name) {
							default:
								break;
							case 'input-newuser':
							case 'input-newpswd':
							case 'input-cnfpswd':
								$(this).attr ('readonly', true);
						}
						
						if ($name === 'input-newpswd' || $name === 'input-cnfpswd')
							$(this).val ('');
					}
					
					if ($(this).is ('[type="password"]')) $(this).removeAttr ('required');
				});
			}
		};

		$(function () {
			$dts = $('.dataTable');
			if ($dts.length > 0) {
				$.each ($dts, function () {
					$fetch	= $(this).attr ('data-fetch');
					$.fn.dataTable.ext.errMode = 'throw';
					$dt		= $(this);
					setTimeout (function () {
						$dt.addClass ('table-100').DataTable ({
							ajax: {
								url: $.base_url ('uniqore/fetch-data'),
								type: 'POST',
								data: function ($d) {
									$d.fetch		= $fetch;
								},
								complete: function () {
									$dpToggle	= $('body').find ('.dropdown-toggle');
									$btnRefresh	= $('body').find ('#refresh-table');
									if ($dpToggle.length > 0) $dropDown	= new bootstrap.Dropdown ($dpToggle);
									if ($btnRefresh.length > 0 && $btnRefresh.prop ('disabled')) $btnRefresh.prop ('disabled', false);
								},
							},
							order: [[1, 'asc']],
							responsive: true,
							processing: true,
							serverSide: true
						});
					}, 400);
				});
			}
		});
		
		$('body').on ('keyup', function ($event) {
			$isCapsOn	= $event.originalEvent.getModifierState ('CapsLock');
			$target		= $('body').find ('#caps-lock').parent ();
			if ($target.length > 0) {
				if ($isCapsOn) $target.removeClass ('d-none');
				else $target.addClass ('d-none');
			}
		});

		$('body').on ('click', 'button,a', function ($event) {
			if ($(this).attr ('data-action') === 'submitter') {
				$event.preventDefault ();
				$form	= $(this).parents ('form');
				$url	= $form.attr ('data-validator');
				if (!$form.isFormReady ()) $(this).prev ().click ();
				else {
					$.ajax ({
						url: $url,
						method: 'post',
						data: $form.serialize (),
					}).done (function ($res) {
						if ($res.status != 200) $form.find ('#validate-messages').text ($res.messages.error);
						else $form.submit ();
					}).fail (function () {
						
					});
				}
			}
			if ($(this).is ('#refresh-table')) { 
				$(this).prop ('disabled', true);
				$('.dataTable').DataTable ().ajax.reload ();
			}
			if ($(this).prop ('id') === 'edit-data') $(this).loadPropertiesToForm ();
			if ($(this).prop ('id') === 'pswd-change') $(this).openChangePasswordDialog ();
		});
		
		$('.modal').on ('hidden.bs.modal', function ($event) {
			$modalId = $(this).prop ('id');
			if ($modalId === 'modal-form') {
				$(this).find ('form').find (':input').each (function () {
					if (! $(this).is ('[type="hidden"]')) $(this).val ('');
					if ($(this).is ('#uuid')) $(this).val ('none');
					if ($(this).is ('[type="password"]')) $(this).attr ('required', true);
					if ($(this).is ('[type="checkbox"]')) $(this).prop ('checked', true);
					if ($(this).is ('[readonly]')) $(this).removeAttr ('readonly');
				});
			}
				
			if ($modalId === 'modal-changepassword') {
				$(this).find ('form').find (':input').each (function () {
					if (! $(this).is ('[type="hidden"]')) $(this).val ('');
					if ($(this).is ('#userdata')) $(this).val ('empty');
					if ($(this).is ('#uuid')) $(this).val ('none');
				});
			}
		});
	});
});