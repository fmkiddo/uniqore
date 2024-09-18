<body id="uniqore-background">
	<div class="container-scroller dashboard">
		<div class="horizontal-menu">
			<nav class="navbar top-navbar col-lg-12 col-12 p-0">
				<div class="container">
					<div class="d-flex align-items-center justify-content-between w-100">
						<div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                            <a class="navbar-brand brand-logo" href=""><img src="{base_url}assets/imgs/fmkiddo-logo-landscape-white.png" alt="logo"/></a>
                            <a class="navbar-brand brand-logo-mini" href=""><img src="{base_url}assets/imgs/fmkiddo-logo-only-square-white.png" alt="logo"/></a>
                            <div class="welcome-message d-lg-flex d-none">Hi, {if $realname === ''}{username}{else}{realname}{endif}, welcome back!</div>
						</div>
						<div class="navbar-menu-wrapper d-flex align-items-center justify-content-center">
                            <ul class="navbar-nav mr-lg-2">
                            </ul>
						</div>
					</div>
				</div>
			</nav>
			<nav class="bottom-navbar">
				<div class="container">
					<ul class="nav page-navigation">
						<li class="nav-item">
							<a class="nav-link" href="{dashboard_url}?route=welcome">
								<i class="mdi mdi-view-dashboard-outline menu-icon"></i>
								<span class="menu-item">Dashboard</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{dashboard_url}?route=api">
								<i class="mdi mdi-api menu-icon"></i>
								<span class="menu-item">Supported APIs</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{dashboard_url}?route=clients">
								<i class="mdi mdi-account-group-outline menu-icon"></i>
								<span class="menu-item">API Clients</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{dashboard_url}?route=apiadmin">
								<i class="mdi mdi-security menu-icon"></i>
								<span class="menu-item">Administrators</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{dashboard_url}?route=sign-out">
								<i class="mdi mdi-logout-variant menu-icon"></i>
								<span class="menu-item">Logout</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{dashboard_url}?route=documentation">
								<i class="mdi mdi-file-document-outline menu-icon"></i>
								<span class="menu-item">Documentation</span>
							</a>
						</li>
					</ul>
				</div>
			</nav>
		</div>
		<div class="container-fluid page-body-wrapper">
			<div class="main-panel">
				<div class="content-wrapper">
