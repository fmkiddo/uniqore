					<div id="modal-form" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modal-message" aria-hidden="true">
						<div class="modal-dialog modal-lg modal-dialog-centered">
							<div class="modal-content">
								<form method="post" enctype="application/x-www-form-urlencoded" autocomplete="off" data-doajax="true" data-validator="{validate_url}" data-generator="{generate_url}">
									<input type="hidden" name="{csrf_name}" value="{csrf_data}" />
									<input type="hidden" name="target" value="{dts_fetch}" />
									<input type="hidden" id="uuid" name="input-uuid" value="none" />
									<div class="modal-header">
										<h5 class="modal-title">API User Form</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label for="input-newcname">Client Name:</label>
											<input type="text" class="form-control" id="cname" name="input-newcname" placeholder="Type new API client name" required />
										</div>
										<div id="client-init" data-type="form-section">
											<div class="form-group">
												<label for="input-newccode">Client Code:</label>
												<div class="input-group">
													<input type="text" class="form-control" id="ccode" name="input-newccode" data-readonly="true" placeholder="Type new API client code or press button to let system generate client code" required />
													<button type="button" id="generate-ccode" class="btn btn-outline-primary" title="Generate data">
														<i class="mdi mdi-pencil-circle-outline"></i>
													</button>
												</div>
											</div>
											<div class="form-group">
												<label for="input-capi">API:</label>
												<select class="form-control" id="capi" name="input-newcapi">
													<option disabled="disabled" selected="selected"> --- select API --- </option>
												</select>
											</div>
        									<div class="d-flex align-items-center justify-content-end mt-3">
        										<button type="button" class="btn btn-primary" data-action="next" title="Next">
        											<i class="mdi mdi-arrow-right-thick"></i>
        										</button>
        									</div>
										</div>
										<div id="client-info" data-type="form-section" class="d-hidden">
											<div class="form-group">
												<label for="input-newclname">Legal Name:</label>
												<input type="text" id="clname" class="form-control" name="input-newclname" required />
											</div>
											<div class="form-group">
												<label for="input-newcaddr1">Address 1:</label>
												<textarea id="addr1" class="form-control" name="input-newcaddr1" placeholder=""></textarea>
											</div>
											<div class="form-group">
												<label for="input-newcaddr2">Address 2:</label>
												<textarea id="addr2" class="form-control" name="input-newcaddr2" placeholder=""></textarea>
											</div>
											<div class="form-group">
												<label for="input-newcphone">Phone Number:</label>
												<input type="tel" id="cphone" class="form-control" name="input-newcphone" required />
											</div>
											<div class="form-group">
												<label for="input-newctax">Tax Number:</label>
												<input type="text" id="ctax" class="form-control" name="input-newctax" placeholder="" />
											</div>
											<div class="form-group">
												<label for="input-newcpic">Client PIC:</label>
												<input type="text" id="cpic" class="form-control" name="input-newcpic" placeholder="" required />
											</div>
											<div class="form-group">
												<label for="input-newcpicmail">Client PIC Email:</label>
												<input type="email" id="cpicmail" class="form-control" name="input-newcpicmail" placeholder="" required />
											</div>
											<div class="form-group">
												<label for="input-newcpicphone">Client PIC Phone:</label>
												<input type="tel" id="cpicphone" class="form-control" name="input-newcpicphone" placeholder="" required />
											</div>
        									<div class="d-flex align-items-center justify-content-between mt-3">
        										<button type="button" class="btn btn-primary" data-action="prev" title="Previous">
        											<i class="mdi mdi-arrow-left-thick"></i>
        										</button>
        										&nbsp;
        										<button type="button" class="btn btn-primary" data-action="next" title="Next">
        											<i class="mdi mdi-arrow-right-thick"></i>
        										</button>
        									</div>
										</div>
										<div id="client-config" data-type="form-section" class="d-hidden">
											<div class="form-group">
												<label for="input-newcdbname">DB Name:</label>
												<div class="input-group">
													<input type="text" id="cdbname" class="form-control" name="input-newcdbname" data-readonly="true" placeholder="Type client db Name or click button to generate random db name" required>
													<button type="button" id="generate-dbname" class="btn btn-outline-primary" title="Generate data">
														<i class="mdi mdi-pencil-circle-outline"></i>
													</button>
												</div>
											</div>
											<div class="form-group">
												<label for="input-newcdbuser">DB Username:</label>
												<div class="input-group">
													<input type="text" id="cdbuser" class="form-control" name="input-newcdbuser" data-readonly="true" placeholder="Type client db user or click button to generate random db user" required>
													<button type="button" id="generate-dbuser" class="btn btn-outline-primary" title="Generate data">
														<i class="mdi mdi-pencil-circle-outline"></i>
													</button>
												</div>
											</div>
											<div class="form-group">
												<label for="input-newcdbpswd">DB Password:</label>
												<div class="input-group">
													<input type="password" id="cdbpswd" class="form-control" name="input-newcdbpswd" data-readonly="true" placeholder="Type client db password or click button to generate random db password" required>
													<button type="button" id="generate-dbpswd" class="btn btn-outline-primary" title="Generate data">
														<i class="mdi mdi-lock-reset"></i>
													</button>
												</div>
											</div>
											<div class="form-group">
												<label for="input-newcdbprefix">DB Table Prefix:</label>
												<div class="input-group">
													<input type="text" id="cdbprefix" class="form-control" name="input-newcdbprefix" maxlength="4" size="4" data-readonly="true" placeholder="Type client table custom prefix or click button to reset default table prefix" required>
													<button type="button" id="generate-dbprefix" class="btn btn-outline-primary" title="Return to default">
														<i class="mdi mdi-refresh"></i>
													</button>
												</div>
											</div>
        									<div class="d-flex align-items-center justify-content-start mt-3">
        										<button type="button" class="btn btn-primary" data-action="prev" title="Previous">
        											<i class="mdi mdi-arrow-left-thick"></i>
        										</button>
        									</div>
										</div>
										<div class="form-check">
											<input type="checkbox" class="form-check-input" name="input-newcstatus" value="true" checked />
											<label class="form-check-labeel" for="input-cstatus">Active</label>
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
					<div id="modal-client-info" class="modal fade" aria-hidden="true" data-show="{alertShow}">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Registration Success!</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
								</div>
								<div class="modal-body">
									<div class="form-group">
										<label>Client ID:</label>
										<input type="text" class="form-control" value="{user_code}" readonly />
									</div>
									<div class="form-group">
										<label>Client Serial</label>
										<input type="text" class="form-control" value="{user_sn}" readonly />
									</div>
									<p>Please do not lose and store this information in a secure location for API usage purposes</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-primary" data-bs-dismiss="modal">
										<span class="mdi mdi-close-thick"></span>
									</button>
								</div>
							</div>
						</div>
					</div>
