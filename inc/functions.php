<?php
// Set waktu
date_default_timezone_set('Asia/Jakarta');
$tgl = date('Y-m-d H:i:s');

//Koneksi database
$HOSTNAME = "localhost";
$DATABASE = "db_apk2_bankmini";
$USERNAME = "root";
$PASSWORD = "";

$KONEKSI = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if (!$KONEKSI) {
    die("Koneksi Databas error boksku!!" . mysqli_connect_error($KONEKSI));
}


//fungsi autonumber
function autonumber($tabel, $kolom, $lebar = 0, $awalan)
{
    global $KONEKSI;

    $auto = mysqli_query($KONEKSI, "SELECT $kolom FROM  $tabel ORDER BY $kolom desc limit 1") or die(mysqli_error($KONEKSI));
    $jumlah_record = mysqli_num_rows($auto);

    if ($jumlah_record == 0) {
        $nomor = 1;
    } else {
        $row = mysqli_fetch_array($auto);
        $nomor = intval(substr($row[0], strlen($awalan))) + 1;
    }

    if ($lebar > 0) {
        $angka = $awalan . str_pad($nomor, $lebar, "0", STR_PAD_LEFT);
    } else {
        $angka = $awalan . $nomor;
    }
    return $angka;
}
// echo autonumber("tbl_users", "id_user", 7, "USR");

// Fungsi signup
function signup($data)
{
    global $KONEKSI;
    global $tgl;

    $id_user = stripslashes($data['id_user']);
    $nama = stripslashes($data['name']); // untuk cek form register dari input nama
    $email = strtolower(stripslashes($data['email'])); // memasikan form register mengirim input email berupa huruf kecil semua
    $password = mysqli_real_escape_string($KONEKSI, $data['password']);
    $password2 = mysqli_real_escape_string($KONEKSI, $data['password2']);


    // echo $nama ."|". $email ."|". $password ."|". $password2;

    //cek email yang diinpuy ada di database atau belum
    $result = mysqli_query($KONEKSI, "SELECT email from tbl_users WHERE email='$email'");
    if (mysqli_fetch_assoc($result)) {
        echo "<script>
    alert('Email yang km input dah ada!!');
    </script>";
        return false;
    }

    // cek konfirmasi password
    if ($password !== $password2) {
        echo "<script>
    alert('Password km beda!!');
    document.location.href='register.php';
    </script>";
        return false;
    }

    // enkripsi password yang akan kita masukkan
    $password_hash = password_hash($password, PASSWORD_DEFAULT); // menggunakan algoritma function dari hash

    // Ambil id_tipe_user tang ada di tabel tbl_tipe_user

    $tipe_user = "SELECT  * FROM tbl_tipe_user WHERE tipe_user='Admin' ";
    $hasil = mysqli_query($KONEKSI, $tipe_user);
    $row = mysqli_fetch_assoc($hasil);
    $id = $row ['id_tipe_user'];

    //tambahkan user baru ke tbl_users
    $sql_users = "INSERT INTO tbl_users SET
    id_user = '$id_user',
    role = '$id',
    email = '$email',
    password = '$password_hash',
    create_at = '$tgl'";

    mysqli_query($KONEKSI, $sql_users) or die("Gagal menambahkan user nih, bos!" . mysqli_error($KONEKSI));

    //tambahkan user baru ke tbl_admin
    $sql_admin = "INSERT INTO tbl_admin SET
    id_user = '$id_user',
    nama_admin = '$nama',
    create_at = '$tgl'";

    mysqli_query($KONEKSI, $sql_admin) or die("Gagal menambahkan user nih, bos!" . mysqli_error($KONEKSI));
    echo "<script>
    document.location.href='login.php';
    </script>";

    return mysqli_affected_rows($KONEKSI);
}

// membuat fungsi tampil data
function tampil($DATA){
    global $KONEKSI;

    $HASIL = mysqli_query($KONEKSI, $DATA);
    $data = []; //menyiapkan variabel/wadah yang masih kosong untuk nantinya akan kita gunakan untuk menyimpan data yang kita query/panggil dari database

    while ($row = mysqli_fetch_assoc($HASIL)) {
        $data[] = $row; // kita masukkan datanya di sini
    }
    return $data; // kita kembalikan nilainya, kita munculkan
}
?>