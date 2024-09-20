$(function () {
	$(document).ready (function () {
		$.base_url = function ($relativePath) {
			return $base_url + '/' + $relativePath;
		};

		$(function () {
			$dts = $('.dataTable');
			if ($dts.length > 0) {
				$.each ($dts, function () {
					$fetch = $(this).attr ('data-fetch');
					$(this).addClass ('table-100').DataTable ({
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
	});
});