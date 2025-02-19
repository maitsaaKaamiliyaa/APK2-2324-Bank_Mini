<?php
		@session_start();
		require_once "../inc/functions.php";

		/*kita cek apakah sudah log in apa belum, jika sudah dia level apa? mka harus kita arahkan sesuai levelnya
jika dia level admin maka akan diarahkan ke folder admin/index.php
jika dia level petugas maka akan diarahkan ke folder petugas/index.php
jika dia level penyewa maka akan diarahkan ke folder penyewa/index.php
*/

		if (@$_SESSION['email']) {
			if (@!$_SESSION['level'] == "Admin") {
				header("location:../admin/index.php");
			} else {
				if (@$_SESSION['level'] == "Petugas") {
					header("location:../petugas/index.php");
				}
			}
		} else {
			header("location:../inc/login.php");
		}

		// // ambil data user yang login
		$email = $_SESSION['email'];
		// // echo $email;
		// // die;
		$sql_login = tampil("SELECT `tbl_admin`.nama_admin, tbl_users.email, tbl_tipe_user.tipe_user FROM tbl_admin LEFT JOIN tbl_users ON tbl_admin.id_user = tbl_users.id_user LEFT JOIN tbl_tipe_user ON tbl_users.role = tbl_tipe_user.id_tipe_user WHERE tbl_users.email='$email'");
		// // var_dump($sql_login);

		foreach ($sql_login as $user_login) {
			$nama_user = $user_login['nama_admin'];
			$tipe_user = $user_login['tipe_user'];
		}
		// echo $nama_user, " || " .$tipe_user;
		
		?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Bank Mini</title>

	<!-- Meta -->
	<meta name="description" content="Marketplace for Bootstrap Admin Dashboards" />
	<meta name="author" content="Bootstrap Gallery" />
	<link rel="canonical" href="https://www.bootstrap.gallery/">
	<meta property="og:url" content="https://www.bootstrap.gallery">
	<meta property="og:title" content="Admin Templates - Dashboard Templates | Bootstrap Gallery">
	<meta property="og:description" content="Marketplace for Bootstrap Admin Dashboards">
	<meta property="og:type" content="Website">
	<meta property="og:site_name" content="Bootstrap Gallery">
	<link rel="shortcut icon" href="../assets/images/favicon.svg" />

	<!-- *************
			************ CSS Files *************
		************* -->
	<link rel="stylesheet" href="../assets/fonts/bootstrap/bootstrap-icons.css" />
	<link rel="stylesheet" href="../assets/css/main.min.css" />

	<!-- *************
			************ Vendor Css Files *************
		************ -->

	<!-- Scrollbar CSS -->
	<link rel="stylesheet" href="../assets/vendor/overlay-scroll/OverlayScrollbars.min.css" />
</head>

<body>
	<!-- Page wrapper start -->
	<div class="page-wrapper">

		<!-- Main container start -->
		<div class="main-container">

			<!-- Sidebar wrapper start -->
			<nav id="sidebar" class="sidebar-wrapper">

				<!-- App brand starts -->
				<div class="app-brand px-3 py-3 d-flex align-items-center">
					<a href="index.html">
						<img src="../assets/images/logo.svg" class="logo" alt="Bootstrap Gallery" />
					</a>
				</div>
				<!-- App brand ends -->

				<!-- Sidebar profile starts -->
				<div class="sidebar-user-profile">
					<img src="../assets/images/bankMini.png" class="profile-thumb rounded-2 p-2 d-lg-flex d-none"
						alt="Bootstrap Gallery" />
					<h5><?= $nama_user; ?></h5>
				</div>
				<!-- Sidebar profile ends -->

				<!-- Sidebar menu starts -->
				<div class="sidebarMenuScroll">
					<ul class="sidebar-menu">
						<li class="treeview">
							<a href="#!">
								<i class="bi bi-window-sidebar"></i>
								<span class="menu-text">Invoices</span>
							</a>
							<ul class="treeview-menu">
								<li>
									<a href="create-invoice.html">Create Invoice</a>
								</li>
								<li>
									<a href="view-invoice.html">View Invoice</a>
								</li>
								<li>
									<a href="invoice-list.html">Invoice List</a>
								</li>
							</ul>
						</li>
						<li class="treeview">
							<a href="#!">
								<i class="bi bi-window-sidebar"></i>
								<span class="menu-text">Data Tabungan</span>
							</a>
							<ul class="treeview-menu">
								<li>
									<a href="create-invoice.html">Rekayasa Perangkat Lunak</a>
								</li>
								<li>
									<a href="view-invoice.html">Teknik Komputer Jaringan</a>
								</li>
								<li>
									<a href="invoice-list.html">Farmasi</a>
								</li>
							</ul>
					</ul>
				</div>
				<!-- Sidebar menu ends -->

			</nav>
			<!-- Sidebar wrapper end -->

			<!-- App container starts -->
			<div class="app-container">

				<!-- App header starts -->
				<div class="app-header d-flex align-items-center">

					<!-- Toggle buttons start -->
					<div class="d-flex">
						<button class="btn btn-primary me-2 toggle-sidebar" id="toggle-sidebar">
							<i class="bi bi-chevron-left fs-5"></i>
						</button>
						<button class="btn btn-primary me-2 pin-sidebar" id="pin-sidebar">
							<i class="bi bi-chevron-left fs-5"></i>
						</button>
					</div>
					<!-- Toggle buttons end -->

					<!-- App brand sm start -->
					<div class="app-brand-sm d-md-none d-sm-block">
						<a href="index.html">
							<img src="../assets/images/logo-sm.svg" class="logo" alt="Bootstrap Gallery">
						</a>
					</div>
					<!-- App brand sm end -->

					<!-- App header actions start -->
					<div class="header-actions">
						<div class="dropdown ms-3">
							<a id="userSettings" class="dropdown-toggle d-flex py-2 align-items-center text-decoration-none"
								href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">
								<img src="../assets/images/user.png" class="rounded-circle img-3x" alt="Bootstrap Gallery" />
							</a>
							<div class="dropdown-menu dropdown-menu-end shadow">
								<a class="dropdown-item d-flex align-items-center" href="profile.html"><i
										class="bi bi-person fs-4 me-2"></i>Profile</a>
								<a class="dropdown-item d-flex align-items-center" href="settings.html"><i
										class="bi bi-gear fs-4 me-2"></i>Account Settings</a>
								<a class="dropdown-item d-flex align-items-center" href="../inc/logout.php"><i
										class="bi bi-escape fs-4 me-2"></i>Logout</a>
							</div>
						</div>
					</div>
					<!-- App header actions end -->

				</div>
				<!-- App header ends -->

				<?php
				include "../inc/menu.php"
				?>

				<!-- App footer start -->
				<div class="app-footer">
					<span>© Bank Mini SMK MVP ARS Internasional</span>
				</div>
				<!-- App footer end -->

			</div>
			<!-- App container ends -->

		</div>
		<!-- Main container end -->

	</div>
	<!-- Page wrapper end -->

	<!-- *************
			************ JavaScript Files *************
		************* -->
	<!-- Required jQuery first, then Bootstrap Bundle JS -->
	<script src="../assets/js/jquery.min.js"></script>
	<script src="../assets/js/bootstrap.bundle.min.js"></script>

	<!-- *************
			************ Vendor Js Files *************
		************* -->

	<!-- Overlay Scroll JS -->
	<script src="../assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js"></script>
	<script src="../assets/vendor/overlay-scroll/custom-scrollbar.js"></script>

	<!-- Apex Charts -->
	<script src="../assets/vendor/apex/apexcharts.min.js"></script>
	<script src="../assets/vendor/apex/custom/dash1/sparkline.js"></script>
	<script src="../assets/vendor/apex/custom/dash1/customers.js"></script>
	<script src="../assets/vendor/apex/custom/dash1/channel.js"></script>
	<script src="../assets/vendor/apex/custom/dash1/deals.js"></script>
	<script src="../assets/vendor/apex/custom/dash1/demography.js"></script>
	<script src="../assets/vendor/apex/custom/dash1/device.js"></script>
	<script src="../assets/vendor/apex/custom/dash1/weekly-sales.js"></script>

	<!-- Vector Maps -->
	<script src="../assets/vendor/jvectormap/jquery-jvectormap-2.0.5.min.js"></script>
	<script src="../assets/vendor/jvectormap/world-mill-en.js"></script>
	<script src="../assets/vendor/jvectormap/gdp-data.js"></script>
	<script src="../assets/vendor/jvectormap/continents-mill.js"></script>
	<script src="../assets/vendor/jvectormap/custom/world-map-markers2.js"></script>

	<!-- Custom JS files -->
	<script src="../assets/js/custom.js"></script>
</body>

</html>