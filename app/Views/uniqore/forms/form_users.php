					<div id="modal-form-user" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modal-message" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content">
								<form method="post" enctype="application/x-www-form-urlencoded">
									<input type="hidden" name="{csrf_name}" value="{csrf_value}" />
									<div class="modal-header">
										<h5 class="modal-title">Adminisrator User Form</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label for="input-newuser">Username:</label>
											<input type="text" class="form-control" name="input-newuser" required />
										</div>
										<div class="row">
											<div class="col-md-6">
        										<div class="form-group">
        											<label for="input-newmail">Email:</label>
        											<input type="email" class="form-control" name="input-newmail" required />
        										</div>
											</div>
											<div class="col-md-6">
        										<div class="form-group">
        											<label for="input-cnfmail">Confirm Email:</label>
        											<input type="email" class="form-control" name="input-cnfmail" required />
        										</div>
											</div>
										</div>
										<div class="form-group">
											<label for="input-newphone">Phone:</label>
											<input type="tel" class="form-control" name="input-newphone" required />
										</div>
										<div class="form-group">
											<label for="input-newpswd">Password:</label>
											<input type="password" class="form-control" name="input-newpswd" required />
										</div>
										<div class="form-group">
											<label for="input-cnfpswd">Confirm Password:</label>
											<input type="password" class="form-control" name="input-cnfpswd" required />
										</div>
									</div>
									<div class="modal-footer text-end">
										<button type="submit" class="btn btn-primary">
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
