0.<?php
//cek session
@session_start();
require_once 'functions.php';

if (@$_SESSION['email']) {
    if (@$_SESSION['level']=="Admin") {
        header("location:../admin/index.php");
    } elseif (@$_SESSION['level']=="Petugas") {
        header("location:../petugas/index.php");
    } elseif (@$_SESSION['level']=="Penyewa") {
        header("location:../penyewa/index.php");
    } elseif (@$_SESSION['level'] == "Owner") {
        header("location:../owner/index.php");
    } elseif (@$_SESSION['level'] == "Karyawan") {
        header("location:../karyawan/index.php");
    }
        
    } 


//cek login
//jika tombol log in ditekan, maka akan mengirim variabel yang ada di form log in yaitu username berupa email dan password
if (isset($_POST['login'])) {
    $email = $_POST['email']; //email di input oleh user
    $userpass = $_POST['password']; //password di input oleh user

// lalu kita query ke database
$sql = mysqli_query($KONEKSI, "SELECT name, password, level FROM tbl_user WHERE email='$email'");

list($nama, $paswd, $level)=mysqli_fetch_array($sql);

    //jika data ditemukan dalam database, maka akan melakukan proses validasi dengan menggunakan password_verify
    if (mysqli_num_rows($sql)>0) {
        /*jika ada data >0 maka kita lakukan validasi
        $userpass adalah diambil dari form input yang dilakukan oleh user
        $paswd adalah password yang ada di database dalam betuk HASH
        */
        if (password_verify($userpass, $paswd)) {
            //akan kita buatb session baru
            session_start();
            $_SESSION['email'] = $email;
            $_SESSION['level'] = $level;
            $_SESSION['nama'] = $nama;

            /*jika berhasil login maka user akan kita arahkan ke halaman admin sesuai dengan level user
            jika dia level admin maka akan diarahkan ke folder admin/index.php
            jika dia level petugas maka akan diarahkan ke folder petugas/index.php
            jika dia level penyewa maka akan diarahkan ke folder penyewa/index.php
            */
            if ($_SESSION['level'] == "Admin") {
                header("location:../admin/index.php");
            } elseif ($_SESSION['level'] == "Petugas") {
                header("location:../petugas/index.php");
            } elseif ($_SESSION['level'] == "Penyewa") {
                header("location:../penyewa/index.php");
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
<html lang="zxx">

<!-- Mirrored from demo.dashboardpack.com/analytic-html/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 08 Apr 2024 06:12:25 GMT -->

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Analytic</title>
    <link rel="icon" href="../assets/img/mini_logo.png" type="image/png">

    <link rel="stylesheet" href="../assets/css/bootstrap1.min.css" />

    <link rel="stylesheet" href="../assets/vendors/themefy_icon/themify-icons.css" />
    <link rel="stylesheet" href="../assets/vendors/font_awesome/css/all.min.css" />


    <link rel="stylesheet" href="../assets/vendors/scroll/scrollable.css" />

    <link rel="stylesheet" href="../assets/css/metisMenu.css">

    <link rel="stylesheet" href="../assets/css/style1.css" />
    <link rel="stylesheet" href="../assets/css/colors/default.css" id="colorSkinCSS">
</head>

<body class="crm_body_bg">

    <div class="main_content_iner ">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="white_box mb_30">
                        <div class="row justify-content-center">
                            <div class="col-lg-6">

                                <div class="modal-content cs_modal">
                                    <div class="modal-header justify-content-center theme_bg_1">
                                        <h5 class="modal-title text_white">Log in</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post">
                                            <div class="">
                                                <input type="email" name="email" class="form-control" placeholder="Enter your email">
                                            </div>
                                            <div class="">
                                                <input type="password" name="password" class="form-control" placeholder="Password">
                                            </div>
                                            <button type="submit" class="btn_1 full_width text-center" name="login">Log in</button>
                                            <p>Need an account? 
											<a href="register.php">Register</a></p>
                                            <div class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#forgot_password" data-bs-dismiss="modal" class="pass_forget_btn">Forget Password?</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery1-3.4.1.min.js"></script>

    <script src="../assets/js/popper1.min.js"></script>

    <script src="../assets/js/bootstrap1.min.js"></script>

    <script src="../assets/js/metisMenu.js"></script>

    <script src="../assets/vendors/scroll/perfect-scrollbar.min.js"></script>
    <script src="../assets/vendors/scroll/scrollable-custom.js"></script>

    <script src="../assets/js/custom.js"></script>
</body>

<!-- Mirrored from demo.dashboardpack.com/analytic-html/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 08 Apr 2024 06:12:25 GMT -->

</html>