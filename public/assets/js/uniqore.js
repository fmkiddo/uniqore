$(function () {
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
	
	$(document).ready (function () {
		$.base_url = function ($relativePath) {
			return $base_url + '/' + $relativePath;
		};

		$(function () {
			$dts = $('.dataTable');
			if ($dts.length > 0) {
				$.each ($dts, function () {
					$fetch = $(this).attr ('data-fetch');
					$.fn.dataTable.ext.errMode = 'throw';
					$(this).addClass ('table-100').delay (10000).DataTable ({
						ajax: {
							url: $.base_url ('uniqore/fetch-data'),
							type: 'POST',
							data: function ($d) {
								$d.fetch		= $fetch;
							}
						},
						responsive: true,
						processing: true,
						serverSide: true
					});
				});
			}
		});

		$('a.info-box').mouseenter (function ($event) {
			console.log ('Hovering');
		}).mouseleave (function ($event) {
			console.log ('Leaving');
		});
		
		$('button#submitter').click (function ($event) {
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
		});
	});
});