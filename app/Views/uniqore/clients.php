					<div class="row">
						<div class="col-md-12 grid-margin stretch-card">
							<div class="card">
								<div class="card-body">
									<div class="d-block">
										<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-form">
											<i class="mdi mdi-plus-box"></i>
										</button>
									</div>
									<div class="d-block">
										<input type="hidden" name="{csrf_name}" value="{csrf_value}" data-csrf="true" />
										<table class="dataTable table table-striped table-hover table-centered center-first-column last-col-textend" data-fetch="{dts_fetch}" data-page-length="25">
											<thead>
												<tr>
													<th>Client #</th>
													<th>Code</th>
													<th>Name</th>
													<th>API</th>
													<th>Status</th>
													<th>
														<i class="mdi mdi-information-box"></i>
													</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
