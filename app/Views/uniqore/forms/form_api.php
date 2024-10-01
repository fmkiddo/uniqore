					<div id="modal-form" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modal-message" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content">
								<form method="post" enctype="application/x-www-form-urlencoded" autocomplete="off" data-doajax="true" data-validator="{validate_url}">
									<input type="hidden" name="{csrf_name}" value="{csrf_data}" />
									<input type="hidden" name="target" value="{dts_fetch}" />
									<input type="hidden" id="uuid" name="input-uuid" value="none" />
									<div class="modal-header">
										<h5 class="modal-title">API System Forms</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label for="input-newcode">Code:</label>
											<input type="text" class="form-control" id="code" name="input-newcode" placeholder="New API code" data-readonly="true"  required />
										</div>
										<div class="form-group">
											<label for="input-newname">Name:</label>
											<input type="text" class="form-control" id="name" name="input-newname" placeholder="New API name" required />
										</div>
										<div class="form-group">
											<label for="input-newdscript">Descriptions:</label>
											<textarea class="form-control" name="input-newdscript" id="dscript" placeholder="Describe your API here"></textarea>
										</div>
										<div class="form-group">
											<label for="input-newprefix">Default Table Prefix:</label>
											<input type="text" class="form-control" name="input-newprefix" id="prefix" placeholder="Specify default api table prefix" required />
										</div>
										<div class="form-check">
    										<input class="form-check-input" type="checkbox" id="status" name="input-newstatus" checked />
    										<label class="form-check-label" for="input-newstatus">Active</label>
										</div>
										<div class="d-none text-danger">
											<p id="validate-messages"></p>
										</div>
									</div>
									<div class="modal-footer text-end">
										<button type="submit" class="d-hidden"></button>
										<button type="button" class="btn btn-primary" data-action="submitter">
											<span class="mdi mdi-content-save-outline"></span>
										</button>
										<button type="button" class="btn btn-primary" data-bs-dismiss="modal">
											<span class="mdi mdi-close-thick"></span>
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
