		<div class="row">
			<div class="col-md-6 offset-md-3">
				<hr />
				<form id="newsuperuserform" action="{base_url}uniqore/forge/1" 
						method="post" enctype="application/x-www-form-urlencoded">
					<input type="hidden" name="{csrf_name}" value="{csrf_data}" />
					<input type="hidden" name="newsukey" value="{newsukey}" />
					<input type="hidden" name="newsudb" value="{newsudb}" />
					<div class="card card-primary">
						<div class="card-body m-3">
							<p>Please create your new super user account here by filling in your new user 
							name, email and password in the form below.</p>
							<hr />	
							<div class="form-group">
								<label for="newsuname">Username:</label>
								<input type="text" class="form-control" name="newsuname" placeholder="e.g. Kirin" required />
							</div>	
							<div class="form-group">
								<label for="newsumail">Email:</label>
								<input type="email" class="form-control" name="newsumail" placeholder="e.g. kirintor@yourdomain.com" required />
							</div>	
							<div class="form-group">
								<label for="newsuphone">Phone:</label>
								<input type="tel" pattern="0[0-9]{3}-[0-9]{4}-[0-9]{2, 5}" class="form-control" name="newsuphone" 
										placeholder="e.g. 0811-2345-6789" required />
							</div>
							<div class="form-group">
								<label for="newsupswd">Password:</label>
								<div class="input-group">
									<input type="password" class="form-control" name="newsupswd" placeholder="Your password" required />
									<span class="input-group-text">
										<i class="fas fa-eye fa-fw"></i>
									</span>
								</div>
							</div>
							<div class="form-group">
								<label for="cnfmpswd">Confirm your password:</label>
								<input type="password" class="form-control" name="cnfmpswd"  placeholder="Re-type your password" required />
							</div>{if $validated}
							<div class="text-danger mt-3">
								<p>You have error in your input data, please re-entry your data for revalidation.</p>
							</div>{endif}
							<hr />
							<div class="d-flex justify-content-between">
								<div>
									<a href="{base_url}admin" class="btn btn-primary" title="Go Home...">
										<i class="fas fa-home fa-fw"></i>
									</a>
								</div>
								<div>
    								<button type="submit" class="btn btn-primary" title="">
    									<i class="fas fa-save fa-fw"></i>
    								</button>
    								<button type="reset" class="btn btn-primary">
    									<i class="fas fa-undo fa-fw"></i>
    								</button>
    							</div>
							</div>
						</div>
					</div>
				</form>
    		</div>
    	</div>
