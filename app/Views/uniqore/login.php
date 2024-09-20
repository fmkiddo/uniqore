		<div class="row uniqore-login">
			<div class="col-md-6 offset-md-3">
				<form method="post" enctype="application/x-www-form-urlencoded" {if !$validity}class="was-validated"{endif}>
					<input type="hidden" name="{csrf_name}" value="{csrf_value}" />
    				<div class="card">
    					<div class="card-body m-3">
    						<div class="row">
    							<div class="col-md-8">
    								<h3>Uniqore Login</h3>
    							</div>
    							<div class="col-md-4">
									<div class="logo-landscape-white"></div>
								</div>
    						</div>
    						<hr />
    						<div class="row">
    							<div class="col">
    								<div class="form-group my-3">
    									<label for="login-uname">Username:</label>
    									<input type="text" name="login-uname" class="form-control" placeholder="Input your username" required />
    								</div>
    								<div class="form-group my-3">
    									<label for="login-pword">Password:</label>
    									<input type="password" name="login-pword" class="form-control" placeholder="Input your password" required />
    								</div>
    								<div class="form-check my-3">
    									<input class="form-check-input" type="checkbox" name="login-stays" />
    									<label class="form-check-label" for="login-stays">Stay signed in</label>
    								</div>{if !$validity}
    								<div class="my-3 text-danger">
    									<p>{error}</p>
    								</div>{endif}
    							</div>
    						</div>
    						<div class="mt-3 text-end">
    							<button class="btn btn-primary"><i class="fas fa-paper-plane fa-fw"></i></button>
    						</div>
    					</div>
    				</div>
    			</form>
			</div>
		</div>
		<footer class="footer-login">
			Copyright &copy; 2024 - fmkiddo
		</footer>
