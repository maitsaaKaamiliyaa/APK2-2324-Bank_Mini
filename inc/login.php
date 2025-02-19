<?php
//cek session
@session_start();
require_once 'functions.php';

if (@$_SESSION['email']) {
    if (@$_SESSION['level'] == "Admin") { header("location:../admin/index.php");
    } elseif (@$_SESSION['level'] == "Petugas") {
        header("location:../petugas/index.php");
    }
}


//cek login
//jika tombol log in ditekan, maka akan mengirim variabel yang ada di form log in yaitu username berupa email dan password
if (isset($_POST['login'])) {
    $email = strtolower(stripslashes($_POST['email'])); //email di input oleh user
    $userpass = mysqli_real_escape_string($KONEKSI, $_POST['password']); //password di input oleh user

    // lalu kita query ke database
    $sql = mysqli_query($KONEKSI, "SELECT password, role FROM tbl_users WHERE email='$email'");

    list($paswd, $role) = mysqli_fetch_array($sql);

    // ambil level/role user yang sedang login
    $tipe_user = "SELECT  * FROM tbl_tipe_user WHERE id_tipe_user='$role'";
    $hasil = mysqli_query($KONEKSI, $tipe_user);
    $row = mysqli_fetch_assoc($hasil);
    $level = $row['tipe_user'];
    // echo $level;

    //jika data ditemukan dalam database, maka akan melakukan proses validasi dengan menggunakan password_verify
    if (mysqli_num_rows($sql) > 0) {
        /*jika ada data >0 maka kita lakukan validasi
        $userpass adalah diambil dari form input yang dilakukan oleh user
        $paswd adalah password yang ada di database dalam betuk HASH
        */
        if (password_verify($userpass, $paswd)) {
            //akan kita buatb session baru
            $_SESSION['email'] = $email;
            $_SESSION['level'] = $level;

            /*jika berhasil login maka user akan kita arahkan ke halaman admin sesuai dengan level user
            jika dia level admin maka akan diarahkan ke folder admin/index.php
            jika dia level petugas maka akan diarahkan ke folder petugas/index.php
            jika dia level penyewa maka akan diarahkan ke folder penyewa/index.php
            */
            if ($_SESSION['level'] == "Admin") {
                header("location:../admin/index.php");
            } elseif ($_SESSION['level'] == "Petugas") {
                header("location:../petugas/index.php");
            }
            die();
        } else {
            echo '<script language="javascript">
                window.alert("LOGIN KM GAGAL! email/passwordnya cek lg kocak");
                window.document.location.href="login.php";
                </script>';
        }
    } else {
        echo '<script language="javascript">
            window.alert("LOGIN KM GAGAL! email ga ketemu");
            window.document.location.href="login.php";
            </script>';
    }
}
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
									<label class="form-label">Email</label>
									<input type="email" name="email" class="form-control" placeholder="Enter your email" />
								</div>
								<div class="mb-3">
									<label class="form-label">Password</label>
									<input type="password" name="password" class="form-control" placeholder="Enter password" />
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div class="form-check m-0">
										<input class="form-check-input" type="checkbox" value="" id="rememberPassword" />
										<label class="form-check-label" for="rememberPassword">Remember</label>
									</div>
									<a href="forgot-password.html" class="text-blue text-decoration-underline">Lost password?</a>
								</div>
								<div class="d-grid py-3 mt-4">
									<button type="submit" class="btn btn-lg btn-primary" name="login">
										Login
									</button>
								</div>
								<div class="text-center pt-4">
									<span>Not registered?</span>
									<a href="register.php" class="text-blue text-decoration-underline ms-2">
										SignUp</a>
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