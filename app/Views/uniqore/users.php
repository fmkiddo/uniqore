					<div class="row">
						<div class="col-md-12 grid-margin stretch-card">
							<div class="card">
								<div class="card-body">
									<div class="d-block text-end">
										<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-form">
											<i class="mdi mdi-plus-box"></i>
										</button>
										<button type="button" class="btn btn-primary" id="refresh-table">
											<i class="mdi mdi-refresh-circle"></i>
										</button>
									</div>
									<div class="d-block">
										<input type="hidden" name="{csrf_name}" value="{csrf_value}" data-csrf="true" />
										<table class="dataTable table table-striped table-hover table-centered center-first-column last-col-textend" data-fetch="{dts_fetch}" data-page-length="25">
											<thead>
												<tr>
													<th data-orderable="false">User #</th>
													<th>Username</th>
													<th>Email</th>
													<th>Phone</th>
													<th data-orderable="false">Status</th>
													<th data-orderable="false">
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
