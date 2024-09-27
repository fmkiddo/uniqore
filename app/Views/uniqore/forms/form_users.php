					<div id="modal-form" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modal-message" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content">
								<form method="post" enctype="application/x-www-form-urlencoded" autocomplete="off" data-doajax="true" data-validator="{validate_url}">
									<input type="hidden" name="{csrf_name}" value="{csrf_data}" />
									<input type="hidden" name="target" value="{dts_fetch}" />
									<input type="hidden" id="uuid" name="input-uuid" value="none" />
									<div class="modal-header">
										<h5 class="modal-title">Adminisrator User Form</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label for="input-newuser">Username:</label>
											<input type="text" class="form-control" id="username" name="input-newuser" placeholder="e.g. johndoe" required />
										</div>
										<div class="row">
											<div class="col-md-6">
        										<div class="form-group">
        											<label for="input-newmail">Email:</label>
        											<input type="email" class="form-control" id="email" name="input-newmail" placeholder="e.g. johndoe@domain.com" required />
        										</div>
											</div>
											<div class="col-md-6">
        										<div class="form-group">
        											<label for="input-cnfmail">Confirm Email:</label>
        											<input type="email" class="form-control" id="cnfmail" name="input-cnfmail" placeholder="Retype your email" required />
        										</div>
											</div>
										</div>
										<div class="form-group">
											<label for="input-newphone">Phone:</label>
											<input type="tel" class="form-control" id="phone" name="input-newphone" placeholder="e.g. 0812-3456-7890" pattern="0[0-9]{3}-[0-9]{4}-[0-9]{2, 5}" required />
										</div>
										<div class="form-group">
											<label for="input-newpswd">Password:</label>
											<input type="password" class="form-control" name="input-newpswd" placeholder="Your password" required />
										</div>
										<div class="d-none text-danger">
											<p id="caps-lock">Caps Lock is on</p>
										</div>
										<div class="form-group">
											<label for="input-cnfpswd">Confirm Password:</label>
											<input type="password" class="form-control" name="input-cnfpswd" placeholder="Retype your password" required />
										</div>
										<div class="form-check">
											<input type="checkbox" class="form-check-input" id="active" name="input-active" checked />
											<label for="input-active" class="form-check-label">Active</label>
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
					<div id="modal-changepassword" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modal-message" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content">
								<form method="post" enctype="application/x-www-form-urlencoded" autocomplete="off" data-doajax="true" data-validator="{validate_url}">
									<input type="hidden" name="{csrf_name}" value="{csrf_data}" />
									<input type="hidden" name="target" value="password-change" />
									<input type="hidden" id="userdata" name="user-data" value="empty" />
									<input type="hidden" id="uuid" name="input-uuid" value="none" />
									<div class="modal-header">
										<h5 class="modal-title">Change User Password</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label for="input-oldpswd">Type old password:</label>
											<input type="password" class="form-control" name="input-oldpswd" required />
										</div>
										<div class="form-group">
											<label for="input-newpswd">Type new password:</label>
											<input type="password" class="form-control" name="input-newpswd" required />
										</div>
										<div class="form-group">
											<label for="input-cnfpswd">Re-type new password:</label>
											<input type="password" class="form-control" name="input-cnfpswd" required />
										</div>
									</div>
									<div class="modal-footer">
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
					<div id="modal-deactivate" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modal-message" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content">
								<form method="post" enctype="application/x-www-form-urlencoded" autocomplete="off">
									<input type="hidden" name="{csrf_name}" value="{csrf_data}" />
									<div class="modal-header">
										<h5 class="modal-title">Deactivate User <span id="user-name"></span></h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
									</div>
									<div class="modal-body">
										<p>Are you sure you want to deactivate user <span id="user-name"></span>?</p>
										<p>Don't worry, you can reactivate this user anytime.</p>
									</div>
									<div class="modal-footer">
									</div>
								</form>
							</div>
						</div>
					</div>
