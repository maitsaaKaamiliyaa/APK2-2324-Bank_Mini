<?php
@session_start();
require_once 'functions.php';

//pengecekan apakah sudah login sebagai admin

// if (@$_SESSION['email']) {
//     if (@!$_SESSION['level'] == "Admin") {
//         header("location:../inc/register.php");
//     } else {
//         if (@$_SESSION['level'] == "Petugas") {
//             header("location:../petugas/index.php");
//         } elseif (@$_SESSION['level'] == "Penyewa") {
//             header("location:../penyewa/index.php");
//         } elseif (@$_SESSION['level'] == "Owner") { header("location:../owner/index.php");
// }elseif (@$_SESSION['level'] == "Karyawan") { header("location:../karyawan/index.php"); }
//     }
// } else {
//     header("location:../inc/login.php");
// }

// untuk cek registrasi
if (isset($_POST["signup"])) {
    if (signup($_POST) > 0) {
        echo "<script>
        alert('User baru berhasil ditambahkan!!');
        document.location.href='login.php';
        </script>";
    } else {
        echo mysqli_error($KONEKSI);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>Sign Up</title>

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
	</head>

	<body>
		<!-- Container start -->
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-4 col-lg-5 col-sm-6 col-12">
					<form method="post" class="my-5">
						<div class="border border-dark rounded-2 p-4 mt-5">
							<div class="login-form">
								<a href="index.html" class="mb-4 d-flex">
									<img src="../assets/images/logo.svg" class="img-fluid login-logo" alt="Nyke Admin" />
								</a>
								<div class="mb-3">
									<label class="form-label">Name</label>
									<input type="hidden" class="form-control" name="id_user" value="<?php echo autonumber("tbl_users", "id_user", 7, "ADM"); ?>" placeholder="Enter your Name">
									<input type="name" name="name" class="form-control" placeholder="Enter your Name" />
								</div>
								<div class="mb-3">
									<label class="form-label">Email</label>
									<input type="email" name="email" class="form-control" placeholder="Enter your email" />
								</div>
								<div class="mb-3">
									<label class="form-label">Password</label>
									<input type="password" name="password" class="form-control" placeholder="Enter password" />
								</div>
								<div class="mb-3">
									<label class="form-label">Confirm Password</label>
									<input type="password" name="password2" class="form-control" placeholder="Re-enter password" />
								</div>
								<div class="d-grid py-3 mt-4">
									<button type="submit" name="signup" class="btn btn-lg btn-primary">
										Signup
									</button>
								</div>
								<div class="text-center pt-4">
									<span>Already have an account?</span>
									<a href="login.php" class="text-blue text-decoration-underline ms-2">
										Login</a>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- Container end -->
	</body>

</html>