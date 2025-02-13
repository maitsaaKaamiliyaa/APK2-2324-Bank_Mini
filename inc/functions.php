<?php
// Set waktu
date_default_timezone_set('Asia/Jakarta');
$tgl = date('Y-m-d H:i:s');

//Koneksi database
$HOSTNAME = "localhost";
$DATABASE = "db_apartement_fix";
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

// Fungsi register
function registrasi($data)
{
    global $KONEKSI;
    global $tgl;

    $id_user = stripslashes($data['id_user']);
    $nama = stripslashes($data['nama']); // untuk cek form register dari input nama
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

// fungsi tambah data admin
function tambah_admin ($DATA){
    global $KONEKSI;
    global $tgl;

    $id_admin   = stripslashes($_POST['id_admin']);
    $nama_admin = stripslashes ($_POST['nama_admin']);
    $email      = strtolower (stripslashes($_POST['email']));
    $telepon    = stripslashes ($_POST['telepon']);
    $role       = stripslashes ($_POST['role']);
    $password   = mysqli_real_escape_string ($KONEKSI, $_POST['password']);
    $password2  = mysqli_real_escape_string ($KONEKSI, $_POST['password2']);

//cek email yang didaftar apakah sudah dipakai apa belum
    $result = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email='$email'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
        alert('email yang di-input sudah ada di database');
        document.location.href = '?pages=user_admin';
    </script>";
    return false;
    }

//cek konfirmasi password
    if ($password !== $password2) {
        echo "<script>
        alert('konfirmasi password yang di-input berbeda');
        document.location.href = '?pages=user_admin';
    </script>";
    return false;
    }

//kita lakukan enkripsi password yang di input
    $password_hash = password_hash ($password, PASSWORD_DEFAULT);

    
// pastikan gambar terupload
    $gambar_foto = upload_file();

//jika tidak upload foto proses kita hentikan
    if (!$gambar_foto) {
        return false;
    }


//tambahkan data user baru ke tbl_users
    $sql_user = "INSERT INTO tbl_users SET
    id_user = '$id_admin',
    email = '$email',
    password = '$password_hash',
    role = '$role',
    create_at = 'tgl' ";

    mysqli_query($KONEKSI, $sql_user) or die ("gagal menambahkan user") . mysqli_error($KONEKIS);

// tambah data user baru ke tbl_admin
    $sql_user = "INSERT INTO tbl_admin SET
    nama_admin = '$nama_admin',
    telepon_admin = '$telepon',
    path_photo_admin = '$gambar_foto',
    id_user = '$id_admin',    
    create_at = 'tgl' ";

    mysqli_query($KONEKSI, $sql_user) or die ("gagal menambahkan admin") . mysqli_error($KONEKIS);

    return mysqli_affected_rows($KONEKSI);
}

//fungsi upload file
function upload_file (){
//inisialisasi elemen dari photo/gambar
    $namaFile   = $_FILES['Photo']['name'];
    $ukuranFile = $_FILES['Photo']['size'];
    $error      = $_FILES['Photo']['error'];
    $tmpName    = $_FILES['Photo']['tmp_name'];
    $tipeFile   = $_FILES['Photo']['type'];
    $id_admin   = $_POST['id_admin'];

//kita pastikan user upload file
    if ($error == 4) { //4 atrinya tidak ada file yang di-upload
        echo "<script>
        alert('ga ada file yang di-upload');
    </script>";
    return false;
    }

//kita pastikan validasi ekstensi file
    $ekstensiValid = ['jpg', 'jpeg', 'bmp', 'png'];
    $ekstensiFile = explode('.',$namaFile);
    $ekstensiFile = strtolower(end($ekstensiFile));

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>
        alert('file yang diupload bunkan gambar');
    </script>";
    return false;
    }

//kita validasi ukuran maksimal gambar
    if ($ukuranFile > 1 * 1024*1024) {
        echo "<script>
        alert('ukurannya ga boleh lebih dari 1M. sori');
    </script>";
    return false;
    }

//membuat nama file baru yang unik
    $id_random = uniqid();
    $namaFileBaru = $id_admin . "_" . $id_random . "." . $ekstensiFile;

    $target = '../images/users/';
    $file_path = $target.$namaFileBaru;

//kita cek/debag apakah nama baru sudah termasuk, jika ada langsung upload 
    echo "Menyalin file ke : " . $file_path;

    if (move_uploaded_file($tmpName,$file_path)) {
        echo "<script>
        alert('berhasil di-upload');
    </script>";
    return $namaFileBaru;
    } else {
        echo "<script>
        alert('gagal upload gambar');
    </script>";
    return false;
    }
}

// fungsi edit admin
function edit_admin($data){
    global $KONEKSI;
    global $tgl;

    $id_admin   = htmlspecialchars($data['id_admin']);
    $nama_admin = htmlspecialchars($data['nama_admin']);
    $email      = htmlspecialchars($data['email']);
    $telepon    = htmlspecialchars($data['telepon']);
    $foto_lama  = htmlspecialchars($data['photo_db']); //foto lama

    $target = '../images/users/';
    $cek_file_lama = $target.$foto_lama; //lokasi file lama

    //cek apakah ada file baru yang di-upload oleh user
    if (isset($_FILES['Photo']) && $_FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE){
        // jika ada file baru, upload gambar
        $foto_edit = upload_file();

        //pastikan nama file terbaru ter-upload dulu(debuging)
        echo "file baru" .$foto_edit;

        //pastikan file lama dihapus (unlink)
        //cek dulu file lama di db apakah ada di folder target
        if ($foto_edit && file_exists($cek_file_lama)) {
            if (unlink($cek_file_lama)) {
            //true ==> berhasil hapus data lama
            echo "berhasil hapus file lama";
            } else {
                //false==? gagal hapus data lama
                echo "gagal hapus file lama, nih, bor!";
            }
        }
    } else {
        // jika tidak ada file gambar baru yang di-upload
        $foto_edit = $foto_lama;
        echo "menggunakan foto lama : ".$foto_lama;
    }
    
    //update(edit) data ke tbl_admin
    $QUERY = "UPDATE tbl_admin SET
    nama_admin = '$nama_admin',
    telepon_admin = '$telepon',
    path_photo_admin = '$foto_edit',   
    update_at = 'tgl' WHERE tbl_admin.id_user = '$id_admin' ";

    // cek apakah query update data berhasil
    if (mysqli_query($KONEKSI, $QUERY)) {
        echo "data dah berhasil di-update!";
    } else {
        echo "data gagal di-update!" . mysqli_affected_rows($KONEKSI);
    }
    
    return mysqli_affected_rows($KONEKSI);
} //kurung tutup function edit_admin

//fungsi hapus user admin
function hapus_admin(){
    global $KONEKSI;
    $id_user = $_GET['id'];

//hapus file gambar yang usernya kita hapus
    $sql = "SELECT * FROM tbl_admin WHERE id_user='$id_user'" or die("data tidak ditemukan" . mysqli_error($KONEKSI));
    $hasil = mysqli_query($KONEKSI, $sql);
    $row = mysqli_fetch_assoc($hasil);

    $photo = $row['path_photo_admin'];
    $target = '../images/users/';

    if (!$photo == "") {
        // jika ada, kita hapus
        unlink ($target.$photo);
    }

// hapus data di tbl admin
    $query_admin = "DELETE FROM tbl_admin WHERE id_user='$id_user'";
    mysqli_query($KONEKSI, $query_admin) or die ("gagal ngapus data user admin T-T" . mysqli_error($KONEKSI));

// hapus data di tbl users
    $query_user = "DELETE FROM tbl_users WHERE id_user='$id_user'";
    mysqli_query($KONEKSI, $query_user) or die ("gagal ngapus data user T-T" . mysqli_error($KONEKSI));
    return mysqli_affected_rows($KONEKSI);
}

//fungsi upload file menggunakan parameter
function upload_file_new($data, $file, $target){

    // inisialisasi elemen dari foto/file
    $namaFile   = $file['Photo']['name'];
    $ukuranFile = $file['Photo']['size'];
    $error      = $file['Photo']['error'];
    $tmpName    = $file['Photo']['tmp_name'];
    $tipeFile   = $file['Photo']['type'];

    $kode       = htmlspecialchars($data['kode']);

    //debug buat elemen $data dan $file
    // echo "<pre>";
    // print_r($data); //melihat data yang akan diterima
    // print_r($file); //melihat data yang akan diterima
    // "</pre>";

    // pastikan bahwa user melakukan upload file
    if ($error == UPLOAD_ERR_NO_FILE) {
        echo "<script>
        alert('tidak ada file yang di-upload!');
        </script>";
        return false; // kalau ada kata 'tidak', menggunakan false
    }

    // validasi ekstensi file
    $ekstensiValid = ['jpeg', 'jpg', 'bmp', 'png'];
    $ekstensiFile  = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    
    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>
        alert('file yang di-upload bukan gambar!');
        </script>";
        return false;
    }

    //validasi ukuran gambar
    if ($ukuranFile > 1 * 1024 * 1024) {
        echo "<script>
        alert('ukurannya ga boleh lebih dari 1M. sori');
    </script>";
        return false;
    }

    //membuat nama file baru yang uniq
    $id_random = uniqid();
    $namaFileBaru = $kode . "_" . $id_random . "." . $ekstensiFile;

    $file_path = $target . $namaFileBaru;

    //cek apakah file sudah terupload
    echo "Menyalin file ke : " . $file_path;

    if (move_uploaded_file($tmpName,$file_path)) {
        echo "<script>
        alert('berhasil di-upload');
    </script>";
    return $namaFileBaru;
    } else {
        echo "<script>
        alert('gagal upload gambar');
    </script>";
    return false;
    }
}

// fungsi tambah branch
function tambah_branch($data, $file, $target){
    global $KONEKSI;
    global $tgl;

    $kode        = htmlspecialchars($data['kode']);
    $nama_branch = htmlspecialchars($data['nama_cab']);
    $alamat      = htmlspecialchars($data['alamat']);
    $email       = htmlspecialchars($data['email']);
    $telepon     = htmlspecialchars($data['telepon']);
    $kecamatan   = htmlspecialchars($data['kecamatan']);
    $kota        = htmlspecialchars($data['kota']);
    $provinsi    = htmlspecialchars($data['provinsi']);
    $kodepos     = htmlspecialchars($data['kodepos']);

    // echo "<pre>";
    // print_r($data); //melihat data yang akan diterima
    // print_r($file); //melihat data yang akan diterima
    // "</pre>";

    //kita harus upload file
    $gambar_foto = upload_file_new($data, $file, $target);
    
    //kita input data ke tabel
    if ($gambar_foto) {
        // jika upload berhasil, maka dilanjut dengan insert data

        $sql = "INSERT INTO tbl_branch SET
        kode_branch = '$kode',
        nama_perusahaan = '$nama_branch',
        alamat_perusahaan = '$alamat',
        email_perusahaan = '$email',
        telepon_perusahaan = '$telepon',
        kecamatan_perusahaan = '$kecamatan',
        kota_perusahaan = '$kota',
        provinsi_perusahaan = '$provinsi',
        path_logo = '$gambar_foto',
        kode_pos = '$kodepos',
        create_at = 'tgl' ";

        // cek apakah query berhasil apa tidak
        if (mysqli_query($KONEKSI, $sql)) {
            echo "<script>
        alert('data dah berhasil ditambahkan!');
        </script>";
        return true;
        } else {
            echo "<script>
        alert('data gak berhasil ditambahkan!".mysqli_error($KONEKSI)."');
        </script>";
        return false;
        }
    } else {
        echo "<script>
        alert('gagal upload file!');
        </script>";
        return false;
    }

}

// fungsi edit branch
function edit_branch($data, $file, $target)
{
    global $KONEKSI;
    global $tgl;

    echo "<pre>";
    print_r($data); //melihat data yang akan diterima
    print_r($target); //melihat data yang akan diterima
    "</pre>";
    
    $kode = htmlspecialchars($data['kode']);
    $nama_branch = htmlspecialchars($data['nama_cab']);
    $alamat = htmlspecialchars($data['alamat']);
    $email = htmlspecialchars($data['email']);
    $telepon = htmlspecialchars($data['telepon']);
    $kecamatan = htmlspecialchars($data['kecamatan']);
    $kota = htmlspecialchars($data['kota']);
    $provinsi = htmlspecialchars($data['provinsi']);
    $kodepos = htmlspecialchars($data['kodepos']);
    $foto_lama  = htmlspecialchars($data['photo_db']); //foto lama

    $cek_file_lama = $target . $foto_lama; //lokasi file lama

    //cek apakah ada file baru yang di-upload oleh user
    if (isset($_FILES['Photo']) && $_FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        // jika ada file baru, upload gambar
        $gambar_foto = upload_file_new($data, $file, $target);
        echo $gambar_foto;

        //pastikan nama file terbaru ter-upload dulu(debuging)
        echo "file baru" . $gambar_foto . "terupload";

        //pastikan file lama dihapus (unlink)
        //cek dulu file lama di db apakah ada di folder target
        if ($gambar_foto && file_exists($cek_file_lama)) {
            if (unlink($cek_file_lama)) {
                //true ==> berhasil hapus data lama
                echo "berhasil hapus file lama";
            } else {
                //false==> gagal hapus data lama
                echo "gagal hapus file lama, nih, bor!";
            }
        }
    } else {
        // jika tidak ada file gambar baru yang di-upload
        $gambar_foto = $foto_lama;
        echo "menggunakan foto lama : " . $foto_lama;
    }

    //update(edit) data ke tbl_branch
    $QUERY = "UPDATE tbl_branch SET
    nama_perusahaan = '$nama_branch',
    alamat_perusahaan = '$alamat',
    email_perusahaan = '$email',
    telepon_perusahaan = '$telepon',
    path_logo = '$gambar_foto',
    kecamatan_perusahaan = '$kecamatan',
    kota_perusahaan = '$kota',
    provinsi_perusahaan = '$provinsi',
    kode_pos = '$kodepos',  
    update_at = '$tgl' WHERE kode_branch = '$kode' ";

    // cek apakah query update data berhasil
    if (mysqli_query($KONEKSI, $QUERY)) {
        echo "data dah berhasil di-update!";
    } else {
        echo "data gagal di-update!" . mysqli_affected_rows($KONEKSI);
    }

    return mysqli_affected_rows($KONEKSI);
} //kurung tutup function edit_admin

//fungsi hapus user branch
function hapus_branch($data, $target)
{
    global $KONEKSI;

    $kode_branch = htmlspecialchars($data['id']);
    echo $kode_branch;

    echo "<pre>";
    print_r($data); //melihat data yang akan diterima
    print_r($target); //melihat data yang akan diterima
    "</pre>";


    // ambil nama file gambar y6ang terkait dengan cabang yang akan dihapus
    $query = "SELECT path_logo FROM tbl_branch WHERE kode_branch = '$kode_branch'";
    $result = mysqli_query($KONEKSI, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $gambar_foto = $data['path_logo'];
        echo $gambar_foto;

        //hapus data dari database
        $deleteQuery = "DELETE FROM tbl_branch WHERE kode_branch = '$kode_branch'";
        if (mysqli_query($KONEKSI, $deleteQuery)) {
            // hapus file gambar dari folder jika ada
            if ($gambar_foto && file_exists($target . $gambar_foto)) {
                unlink($target . $gambar_foto);
            }
            echo "<script>alert('Data berhasil dihapus><');</script>";
            return true;
        } else {
            echo "<script>alert('Data tidak berhasil dihapusT.T');</script>";
            return false;
        }      
    } else {
        echo "<script>alert('Data tidak ditemukanT.T');</script>";
        var_dump(mysqli_num_rows($result));
        die;
        return false;
    }
}

//fungsi tambah petugas
function tambah_petugas($data, $file, $target)
{
    global $KONEKSI;
    global $tgl;

    $ID   = stripslashes($data['kode']);
    $nama_petugas = stripslashes($data['nama_petugas']);
    $email      = strtolower(stripslashes($data['email']));
    $telepon    = stripslashes($data['telepon']);
    $role    = stripslashes($data['role']);
    $jenkel    = stripslashes($data['jenkel']);
    $CABANG    = stripslashes($data['cabang']);
    $password   = mysqli_real_escape_string($KONEKSI, $data['password']);
    $password2  = mysqli_real_escape_string($KONEKSI, $data['password2']);


    //cek email yang didaftar apakah sudah dipakai apa belum
    $result = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email='$email'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
        alert('email yang di-input sudah ada di database');
        document.location.href = '?pages=user_admin';
    </script>";
        return false;
    }

    //cek konfirmasi password
    if ($password !== $password2) {
        echo "<script>
        alert('konfirmasi password yang di-input berbeda');
        document.location.href = '?pages=user_admin';
    </script>";
        return false;
    }

    //kita lakukan enkripsi password yang di input
    $password_hash = password_hash($password, PASSWORD_DEFAULT);


    // pastikan gambar terupload
    $gambar_foto = upload_file_new($data, $file, $target);

    //jika tidak upload foto proses kita hentikan
    if (!$gambar_foto) {
        return false;
    }


    //tambahkan data user baru ke tbl_users
    $sql_user = "INSERT INTO tbl_users SET
    id_user = '$ID',
    email = '$email',
    
    password = '$password_hash',
    role = '$role',
    create_at = '$tgl' ";

    mysqli_query($KONEKSI,
        $sql_user
    ) or die("gagal menambahkan user") . mysqli_error($KONEKIS);

    // tambah data user baru ke tbl_petugas
    $sql_user = "INSERT INTO tbl_petugas SET
    nama_petugas = '$nama_petugas',
    telepon_petugas = '$telepon',
    jenkel = '$jenkel',
    path_photo_petugas = '$gambar_foto',
    id_user = '$ID',
    branch_id = '$CABANG',
    create_at = '$tgl' ";

    mysqli_query($KONEKSI,
        $sql_user
    ) or die("gagal menambahkan admin") . mysqli_error($KONEKIS);

    return mysqli_affected_rows($KONEKSI);
}

// fungsi edit petugas
function edit_petugas($data, $file, $target){
    global $KONEKSI;
    global $tgl;

    $id = stripslashes($data['kode']);
    $nama = stripslashes($data['nama_petugas']);
    $email = stripslashes($data['email']);
    $id_cabang = stripslashes($data['cabang']);
    $telp = stripslashes($data['telepon']);
    $jenkel = stripslashes($data['jenkel']);
    $foto_lama  = stripslashes($data['photo_db']); //foto lama

    $target = '../images/petugas/';
    $cek_file_lama = $target.$foto_lama; //lokasi file lama

    //cek apakah ada file baru yang di-upload oleh user
    if (isset($_FILES['Photo']) && $_FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE){
        // jika ada file baru, upload gambar
        $foto_edit = upload_file_new($data, $file, $target);
        echo $foto_edit;

        //pastikan nama file terbaru ter-upload dulu(debuging)
        echo "file baru" .$foto_edit;

        //pastikan file lama dihapus (unlink)
        //cek dulu file lama di db apakah ada di folder target
        if ($foto_edit && file_exists($cek_file_lama)) {
            if (unlink($cek_file_lama)) {
            //true ==> berhasil hapus data lama
            echo "berhasil hapus file lama";
            } else {
                //false==? gagal hapus data lama
                echo "gagal hapus file lama, nih, bor!";
            }
        }
    } else {
        // jika tidak ada file gambar baru yang di-upload
        $foto_edit = $foto_lama;
        echo "menggunakan foto lama : ".$foto_lama;
    }
    
    //update(edit) data ke tbl_admin
    $QUERY = "UPDATE tbl_petugas SET
    nama_petugas = '$nama',
    telepon_petugas = '$telp',
    path_photo_petugas = '$foto_edit',
    branch_id = '$id_cabang',
    jenkel = '$jenkel',
    update_at = '$tgl' WHERE tbl_petugas.id_user = '$id' ";

    // cek apakah query update data berhasil
    if (mysqli_query($KONEKSI, $QUERY)) {
        echo "data dah berhasil di-update!";
    } else {
        echo "data gagal di-update!" . mysqli_affected_rows($KONEKSI);
    }
    
    return mysqli_affected_rows($KONEKSI);
}

//fungsi hapus petugas
function hapus_petugas($data, $target){
    global $KONEKSI;
    $id_user = $data['id'];

//hapus file gambar yang usernya kita hapus
    $sql = "SELECT * FROM tbl_petugas WHERE id_user='$id_user'" or die("data tidak ditemukan" . mysqli_error($KONEKSI));
    $hasil = mysqli_query($KONEKSI, $sql);
    $row = mysqli_fetch_assoc($hasil);

    $photo = $row['path_photo_petugas'];
    $target = '../images/petugas/';

    if (!$photo == "") {
        // jika ada, kita hapus
        unlink ($target.$photo);
    }

// hapus data di tbl admin
    $query_petugas = "DELETE FROM tbl_petugas WHERE id_user='$id_user'";
    mysqli_query($KONEKSI, $query_petugas) or die ("gagal ngapus data user admin T-T" . mysqli_error($KONEKSI));

// hapus data di tbl users
    $query_user = "DELETE FROM tbl_users WHERE id_user='$id_user'";
    mysqli_query($KONEKSI, $query_user) or die ("gagal ngapus data user T-T" . mysqli_error($KONEKSI));
    return mysqli_affected_rows($KONEKSI);
}

//fungsi tambah jabatan
function tambah_jabatan($data)
{
    global $KONEKSI;
    global $tgl;

    $ID   = stripslashes($data['kode']);
    $nama_jabatan = stripslashes($data['nama_jabatan']);


    //cek email yang didaftar apakah sudah dipakai apa belum
    $result = mysqli_query($KONEKSI, "SELECT nama_jabatan FROM tbl_jabatan WHERE nama_jabatan='$nama_jabatan'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
        alert('email yang di-input sudah ada di database');
        document.location.href = '?pages=jabatan';
    </script>";
        return false;
    }

    //tambahkan data user baru ke tbl_jabatan
    $sql_user_jabatan = "INSERT INTO tbl_jabatan SET
    kode_jabatan = '$ID',
    nama_jabatan = '$nama_jabatan',
    create_at = '$tgl' ";

    mysqli_query(
        $KONEKSI,
        $sql_user_jabatan
    ) or die("gagal menambahkan user") . mysqli_error($KONEKIS);

    return mysqli_affected_rows($KONEKSI);
}

//edit jabatan
function edit_jabatan(){
    global $KONEKSI;
    global $tgl;

    $kode_jabatan = stripslashes($_POST['kode']);
    $nama_jabatan = stripslashes($_POST['nama_jabatan']);

    //update data ke tbl_jabatan
    $sql = "UPDATE tbl_jabatan SET
    kode_jabatan = '$kode_jabatan',
    nama_jabatan = '$nama_jabatan',   
    update_at = '$tgl' WHERE tbl_jabatan.kode_jabatan = '$kode_jabatan' ";

    // cek apakah query update data berhasil
    if (mysqli_query($KONEKSI, $sql)) {
        echo "data dah berhasil di-update!";
    } else {
        echo "data gagal di-update!" . mysqli_affected_rows($KONEKSI);
    }

    return mysqli_affected_rows($KONEKSI);
}

//fungsi hapus user admin
function hapus_jabatan()
{
    global $KONEKSI;
    $kode_jabatan = $_GET['id'];

    //hapus file gambar yang usernya kita hapus
    $sql = "SELECT * FROM tbl_jabatan WHERE kode_jabatan='$kode_jabatan' " or die("data tidak ditemukan" . mysqli_error($KONEKSI));
    $hasil = mysqli_query($KONEKSI, $sql);
    $row = mysqli_fetch_assoc($hasil);

    // hapus data di tbl_jabatan
    $query_jabatan = "DELETE FROM tbl_jabatan WHERE kode_jabatan='$kode_jabatan'";
    mysqli_query($KONEKSI, $query_jabatan) or die("gagal ngapus data user admin T-T" . mysqli_error($KONEKSI));

    return mysqli_affected_rows($KONEKSI);
}

//fungsi tambah karyawan
function tambah_karyawan($data, $file, $target)
{
    global $KONEKSI;
    global $tgl;

    $ID   = stripslashes($data['kode']);
    $nama_karyawan = stripslashes($data['nama_kar']);
    $jabatan    = stripslashes($data['jabatan']);
    $cabang    = stripslashes($data['cabang']);
    $email      = strtolower(stripslashes($data['email']));
    $telepon    = stripslashes($data['telepon']);
    $jenkel = stripslashes($data['jenkel']);
    $noKtp = stripslashes($data['noKtp']);
    $role    = stripslashes($data['role']);
    $alamatDom = stripslashes($data['alamatDom']);
    $alamatKtp = stripslashes($data['alamatKtp']);
    $date_start = stripslashes($data['dateS']);
    $date_finish = stripslashes($data['dateF']);
    $password   = mysqli_real_escape_string($KONEKSI, $_POST['password']);
    $password2  = mysqli_real_escape_string($KONEKSI, $_POST['password2']);

    if ($date_finish == "" || !$date_finish == "0000-00-00") {
        $status = "Active";
    } else {
        $status = "Inactive";
    }
    //cek email yang didaftar apakah sudah dipakai apa belum
    $result = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email='$email'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
        alert('email yang di-input sudah ada di database');
        document.location.href = '?pages=user_karyawan';
    </script>";
        return false;
    }

    //cek konfirmasi password
    if ($password !== $password2) {
        echo "<script>
        alert('konfirmasi password yang di-input berbeda');
        document.location.href = '?pages=user_karyawan';
    </script>";
        return false;
    }

    //kita lakukan enkripsi password yang di input
    $password_hash = password_hash($password, PASSWORD_DEFAULT);


    // pastikan gambar terupload
    $gambar_foto = upload_file_new($data, $file, $target);

    //jika tidak upload foto proses kita hentikan
    if (!$gambar_foto) {
        return false;
    }

    //tambahkan data user baru ke tbl_karyawan
    $sql_user_karyawan = "INSERT INTO tbl_users SET
    id_user = '$ID',
    email = '$email',
    password = '$password_hash',
    role = '$role',
    create_at = '$tgl' ";

    mysqli_query($KONEKSI, $sql_user_karyawan
    ) or die("gagal menambahkan user") . mysqli_error($KONEKIS);

    // tambah data user baru ke tbl_karyawan
    $sql_karyawan = "INSERT INTO tbl_karyawan SET
    nama_karyawan = '$nama_karyawan',
    kode_jabatan = '$jabatan',
    branch_id = '$cabang',
    telepon_karyawan = '$telepon',
    jenkel = '$jenkel',
    no_ktp = $noKtp,
    alamat_domisili_karyawan = '$alamatDom',
    alamat_ktp_karyawan = '$alamatKtp',
    date_start = '$date_start',
    date_finish = '$date_finish',
    status_karyawan = 'Active',
    path_photo_karyawan = '$gambar_foto',
    id_user = '$ID',
    create_at = '$tgl' ";

    mysqli_query(
        $KONEKSI,
        $sql_karyawan
    ) or die("gagal menambahkan admin") . mysqli_error($KONEKIS);

    return mysqli_affected_rows($KONEKSI);
}

// fungsi edit karyawan
function edit_karyawan($data, $file, $target)
{
    global $KONEKSI;
    global $tgl;

    $id_karyawan = stripslashes($data['kode']);
    $nama_karyawan = stripslashes($data['nama_kar']);
    $jabatan    = stripslashes($data['jabatan']);
    $cabang_id    = stripslashes($data['cabang']);
    $email    = stripslashes($data['email']);
    $telepon    = stripslashes($data['telepon']);
    $jenkel    = stripslashes($data['jenkel']);
    $noKtp    = stripslashes($data['noKtp']);
    $alamatDom = stripslashes($data['alamatDom']);
    $alamatKtp = stripslashes($data['alamatKtp']);
    $date_start = stripslashes($data['dateS']);
    $date_finish = stripslashes($data['dateF']);
    $status = stripslashes($data['status']);
    $foto_lama  = stripslashes($data['photo_db']); //foto lama

    $target = '../images/karyawan/';
    $cek_file_lama = $target . $foto_lama; //lokasi file lama

    //cek apakah ada file baru yang di-upload oleh user
    if (isset($_FILES['Photo']) && $_FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        // jika ada file baru, upload gambar
        $foto_edit = upload_file_new($data, $file, $target);
        echo $foto_edit;

        //pastikan nama file terbaru ter-upload dulu(debuging)
        echo "file baru" . $foto_edit;

        //pastikan file lama dihapus (unlink)
        //cek dulu file lama di db apakah ada di folder target
        if ($foto_edit && file_exists($cek_file_lama)) {
            if (unlink($cek_file_lama)) {
                //true ==> berhasil hapus data lama
                echo "berhasil hapus file lama";
            } else {
                //false==? gagal hapus data lama
                echo "gagal hapus file lama, nih, bor!";
            }
        }
    } else {
        // jika tidak ada file gambar baru yang di-upload
        $foto_edit = $foto_lama;
        echo "menggunakan foto lama : " . $foto_lama;
    }

    //update(edit) data ke tbl_karyawan
    $QUERY = "UPDATE tbl_karyawan SET
    nama_karyawan = '$nama_karyawan',
    kode_jabatan = '$jabatan',
    branch_id = '$cabang_id',
    telepon_karyawan = '$telepon',
    jenkel = '$jenkel',
    no_ktp = $noKtp,
    alamat_domisili_karyawan = '$alamatDom',
    alamat_ktp_karyawan = '$alamatKtp',
    date_start = '$date_start',
    date_finish = '$date_finish',
    path_photo_karyawan = '$foto_edit',
    status_karyawan = '$status',
    update_at = '$tgl' WHERE tbl_karyawan.id_user = '$id_karyawan' ";

    // cek apakah query update data berhasil
    if (mysqli_query($KONEKSI, $QUERY)) {
        echo "data dah berhasil di-update!";
    } else {
        echo "data gagal di-update!" . mysqli_affected_rows($KONEKSI);
    }

    return mysqli_affected_rows($KONEKSI);
}

//fungsi hapus karyawan
function hapus_karyawan($data, $target)
{
    global $KONEKSI;
    $id_user = $data['id'];

    //hapus file gambar yang usernya kita hapus
    $sql = "SELECT * FROM tbl_karyawan WHERE id_user='$id_user'" or die("data tidak ditemukan" . mysqli_error($KONEKSI));
    $hasil = mysqli_query($KONEKSI, $sql);
    $row = mysqli_fetch_assoc($hasil);

    $photo = $row['path_photo_karyawan'];
    $target = '../images/karyawan/';

    if (!$photo == "") {
        // jika ada, kita hapus
        unlink($target . $photo);
    }

    // hapus data di tbl admin
    $query_karyawan = "DELETE FROM tbl_karyawan WHERE id_user='$id_user'";
    mysqli_query($KONEKSI, $query_karyawan) or die("gagal ngapus data user admin T-T" . mysqli_error($KONEKSI));

    // hapus data di tbl users
    $query_user = "DELETE FROM tbl_users WHERE id_user='$id_user'";
    mysqli_query($KONEKSI, $query_user) or die("gagal ngapus data user T-T" . mysqli_error($KONEKSI));
    return mysqli_affected_rows($KONEKSI);
}

//fungsi tambah pemilik
function tambah_pemilik($data, $file, $target)
{
    global $KONEKSI;
    global $tgl;

    $ID   = stripslashes($data['kode']);
    $nama_own = stripslashes($data['nama_pem']);
    $email      = strtolower(stripslashes($data['email']));
    $telepon    = stripslashes($data['telepon']);
    $noKtp = stripslashes($data['noKtp']);
    $role    = stripslashes($data['role']);
    $alamatKtp =  stripslashes($data['alamatKtp']);
    $alamatDom = stripslashes($data['alamatDom']);
    $jenkel = stripslashes($data['jenkel']);
    $cabang    = stripslashes($data['cabang']);
    $password   = mysqli_real_escape_string($KONEKSI, $_POST['password']);
    $password2  = mysqli_real_escape_string($KONEKSI, $_POST['password2']);


    //cek email yang didaftar apakah sudah dipakai apa belum
    $result = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email='$email'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
        alert('email yang di-input sudah ada di database');
        document.location.href = '?pages=user_pemilik';
    </script>";
        return false;
    }

    //cek konfirmasi password
    if ($password !== $password2) {
        echo "<script>
        alert('konfirmasi password yang di-input berbeda');
        document.location.href = '?pages=user_pemilik';
    </script>";
        return false;
    }

    //kita lakukan enkripsi password yang di input
    $password_hash = password_hash($password, PASSWORD_DEFAULT);


    // pastikan gambar terupload
    $gambar_foto = upload_file_new($data, $file, $target);

    //jika tidak upload foto proses kita hentikan
    if (!$gambar_foto) {
        return false;
    }

    //tambahkan data user baru ke tbl_karyawan
    $sql_user_karyawan = "INSERT INTO tbl_users SET
    id_user = '$ID',
    email = '$email',
    password = '$password_hash',
    role = '$role',
    create_at = '$tgl' ";

    mysqli_query($KONEKSI, $sql_user_karyawan
    ) or die("gagal menambahkan user") . mysqli_error($KONEKIS);

    // tambah data user baru ke tbl_karyawan
    $sql_pemilik = "INSERT INTO tbl_pemilik SET
    nama_pemilik = '$nama_own',
    branch_id = '$cabang',
    telepon_pemilik = '$telepon',
    jenkel = '$jenkel',
    no_ktp = $noKtp,
    alamat_domisili_pemilik = '$alamatDom',
    alamat_ktp_pemilik = '$alamatKtp',
    path_photo_pemilik = '$gambar_foto',
    id_user = '$ID',
    create_at = '$tgl' ";

    mysqli_query(
        $KONEKSI,
        $sql_pemilik
    ) or die("gagal menambahkan admin") . mysqli_error($KONEKIS);

    return mysqli_affected_rows($KONEKSI);
}

// fungsi edit pemilik
function edit_pemilik($data, $file, $target)
{
    global $KONEKSI;
    global $tgl;

    $id_pemilik = stripslashes($data['kode']);
    $nama_pemilik = stripslashes($data['nama_pem']);
    $cabang_id    = stripslashes($data['cabang']);
    $email    = stripslashes($data['email']);
    $telepon    = stripslashes($data['telepon']);
    $jenkel    = stripslashes($data['jenkel']);
    $noKtp    = stripslashes($data['noKtp']);
    $alamatDom = stripslashes($data['alamatDom']);
    $alamatKtp = stripslashes($data['alamatKtp']);
    $foto_lama  = mysqli_real_escape_string($KONEKSI, $data['photo_db']); //foto lama

    $target = '../images/pemilik/';
    $cek_file_lama = $target . $foto_lama; //lokasi file lama

    //cek apakah ada file baru yang di-upload oleh user
    if (isset($_FILES['Photo']) && $_FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        // jika ada file baru, upload gambar
        $foto_edit = upload_file_new($data, $file, $target);
        echo $foto_edit;

        //pastikan nama file terbaru ter-upload dulu(debuging)
        echo "file baru" . $foto_edit;

        //pastikan file lama dihapus (unlink)
        //cek dulu file lama di db apakah ada di folder target
        if ($foto_edit && file_exists($cek_file_lama)) {
            if (unlink($cek_file_lama)) {
                //true ==> berhasil hapus data lama
                echo "berhasil hapus file lama";
            } else {
                //false==? gagal hapus data lama
                echo "gagal hapus file lama, nih, bor!";
            }
        }
    } else {
        // jika tidak ada file gambar baru yang di-upload
        $foto_edit = $foto_lama;
        echo "menggunakan foto lama : " . $foto_lama;
    }

    //update(edit) data ke tbl_pemilik
    $QUERY = "UPDATE tbl_pemilik SET
    nama_pemilik = '$nama_pemilik',
    branch_id = '$cabang_id',
    telepon_pemilik = '$telepon',
    jenkel = '$jenkel',
    no_ktp = $noKtp,
    alamat_domisili_pemilik = '$alamatDom',
    alamat_ktp_pemilik = '$alamatKtp',
    path_photo_pemilik = '$foto_edit',
    update_at = '$tgl' WHERE tbl_pemilik.id_user = '$id_pemilik' ";

    // cek apakah query update data berhasil
    if (mysqli_query($KONEKSI, $QUERY)) {
        echo "data dah berhasil di-update!";
    } else {
        echo "data gagal di-update!" . mysqli_affected_rows($KONEKSI);
    }

    return mysqli_affected_rows($KONEKSI);
}

//fungsi hapus karyawan
function hapus_pemilik($data, $target)
{
    global $KONEKSI;
    $id_user = $data['id'];

    //hapus file gambar yang usernya kita hapus
    $sql = "SELECT * FROM tbl_pemilik WHERE id_user='$id_user'" or die("data tidak ditemukan" . mysqli_error($KONEKSI));
    $hasil = mysqli_query($KONEKSI, $sql);
    $row = mysqli_fetch_assoc($hasil);

    $photo = $row['path_photo_pemilik'];
    $target = '../images/pemilik/';

    if (!$photo == "") {
        // jika ada, kita hapus
        unlink($target . $photo);
    }

    // hapus data di tbl pemilik
    $query_pemilik = "DELETE FROM tbl_pemilik WHERE id_user='$id_user'";
    mysqli_query($KONEKSI, $query_pemilik) or die("gagal ngapus data user admin T-T" . mysqli_error($KONEKSI));

    // hapus data di tbl users
    $query_user = "DELETE FROM tbl_users WHERE id_user='$id_user'";
    mysqli_query($KONEKSI, $query_user) or die("gagal ngapus data user T-T" . mysqli_error($KONEKSI));
    return mysqli_affected_rows($KONEKSI);
}

//fungsi tambah bulan
function tambah_bulan($data)
{
    global $KONEKSI;
    global $tgl;

    $no_bulan   = stripslashes($_POST['no']);
    $nama_bulan = stripslashes($_POST['nama_bulan']);


    //cek email yang didaftar apakah sudah dipakai apa belum
    $result = mysqli_query($KONEKSI, "SELECT nama_bulan FROM tbl_bulan WHERE nama_bulan='$nama_bulan'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
        alert('email yang di-input sudah ada di database');
        document.location.href = '?pages=bulan';
    </script>";
        return false;
    }

    //tambahkan data user baru ke tbl_bulan
    $sql_user_bulan = "INSERT INTO tbl_bulan SET
    no_bulan = '$no_bulan',
    nama_bulan = '$nama_bulan',
    create_at = '$tgl' ";

    mysqli_query(
        $KONEKSI,
        $sql_user_bulan
    ) or die("gagal menambahkan user") . mysqli_error($KONEKIS);

    return mysqli_affected_rows($KONEKSI);
}

//edit bulan
function edit_bulan()
{
    global $KONEKSI;
    global $tgl;

    $no_bulan = stripslashes($_POST['no']);
    $nama_bulan = stripslashes($_POST['nama_bulan']);
    $id = stripslashes($_POST['id']);

    //update data ke tbl_bulan
    $sql = "UPDATE tbl_bulan SET
    no_bulan = '$no_bulan',
    nama_bulan = '$nama_bulan',   
    update_at = '$tgl' WHERE tbl_bulan.id_bulan = '$id' ";

    // cek apakah query update data berhasil
    if (mysqli_query($KONEKSI, $sql)) {
        echo "data dah berhasil di-update!";
    } else {
        echo "data gagal di-update!" . mysqli_affected_rows($KONEKSI);
    }

    return mysqli_affected_rows($KONEKSI);
}

//fungsi hapus bulan
function hapus_bulan()
{
    global $KONEKSI;
    $no_bulan = $_GET['id'];

    //hapus file gambar yang usernya kita hapus
    $sql = "SELECT * FROM tbl_bulan WHERE id_bulan='$no_bulan' " or die("data tidak ditemukan" . mysqli_error($KONEKSI));
    $hasil = mysqli_query($KONEKSI, $sql);
    $row = mysqli_fetch_assoc($hasil);

    // hapus data di tbl_bulan
    $query_bulan = "DELETE FROM tbl_bulan WHERE id_bulan='$no_bulan'";
    mysqli_query($KONEKSI, $query_bulan) or die("gagal ngapus data user admin T-T" . mysqli_error($KONEKSI));

    return mysqli_affected_rows($KONEKSI);
}

//fungsi tambah tahun
function tambah_tahun($data)
{
    global $KONEKSI;
    global $tgl;

    $nama_tahun = stripslashes($_POST['nama']);


    //cek email yang didaftar apakah sudah dipakai apa belum
    $result = mysqli_query($KONEKSI, "SELECT nama_tahun FROM tbl_tahun WHERE nama_tahun='$nama_tahun'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
        alert('email yang di-input sudah ada di database');
        document.location.href = '?pages=tahun';
    </script>";
        return false;
    }

    //tambahkan data user baru ke tbl_tahun
    $sql_user_tahun = "INSERT INTO tbl_tahun SET
    nama_tahun = '$nama_tahun',
    create_at = '$tgl' ";

    mysqli_query(
        $KONEKSI,
        $sql_user_tahun
    ) or die("gagal menambahkan tahun") . mysqli_error($KONEKIS);

    return mysqli_affected_rows($KONEKSI);
}

//edit tahun
function edit_tahun()
{
    global $KONEKSI;
    global $tgl;

    $id_tahun = stripslashes($_POST['kode']);
    $nama_tahun = stripslashes($_POST['nama_tahun']);

    //update data ke tbl_tahun
    $sql = "UPDATE tbl_tahun SET
    id_tahun = '$id_tahun',
    nama_tahun = '$nama_tahun',   
    update_at = '$tgl' WHERE tbl_tahun.id_tahun = '$id_tahun' ";

    // cek apakah query update data berhasil
    if (mysqli_query($KONEKSI, $sql)) {
        echo "data dah berhasil di-update!";
    } else {
        echo "data gagal di-update!" . mysqli_affected_rows($KONEKSI);
    }

    return mysqli_affected_rows($KONEKSI);
}

//fungsi hapus tahun
function hapus_tahun()
{
    global $KONEKSI;
    $id_tahun = $_GET['id'];

    //hapus file gambar yang usernya kita hapus
    $sql = "SELECT * FROM tbl_tahun WHERE id_tahun='$id_tahun' " or die("data tidak ditemukan" . mysqli_error($KONEKSI));
    $hasil = mysqli_query($KONEKSI, $sql);
    $row = mysqli_fetch_assoc($hasil);

    // hapus data di tbl_tahun
    $query_tahun = "DELETE FROM tbl_tahun WHERE id_tahun='$id_tahun'";
    mysqli_query($KONEKSI, $query_tahun) or die("gagal ngapus data tahun T-T" . mysqli_error($KONEKSI));

    return mysqli_affected_rows($KONEKSI);
}

//fungsi tambah currency
function tambah_currency($data)
{
    global $KONEKSI;
    global $tgl;

    $symbol_currency   = stripslashes($_POST['symbol_mata']);
    $nama_currency = stripslashes($_POST['nama_mata']);

    //cek email yang didaftar apakah sudah dipakai apa belum
    $result = mysqli_query($KONEKSI, "SELECT nama_currency FROM tbl_currency WHERE nama_currency='$nama_currency'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
        alert('email yang di-input sudah ada di database');
        document.location.href = '?pages=currency';
    </script>";
        return false;
    }

    //tambahkan data user baru ke tbl_currency
    $sql_user_currency = "INSERT INTO tbl_currency SET
    symbol_currency = '$symbol_currency',
    nama_currency = '$nama_currency',
    create_at = '$tgl' ";

    mysqli_query(
        $KONEKSI,
        $sql_user_currency
    ) or die("gagal menambahkan currency") . mysqli_error($KONEKIS);

    return mysqli_affected_rows($KONEKSI);
}

//edit currency
function edit_currency()
{
    global $KONEKSI;
    global $tgl;

    $kode_currency = stripslashes($_POST['symbol_mata']);
    $nama_currency = stripslashes($_POST['nama_mata']);
    $id = stripslashes($_POST['id']);

    //update data ke tbl_currency
    $sql = "UPDATE tbl_currency SET
    symbol_currency = '$kode_currency',
    nama_currency = '$nama_currency',   
    update_at = '$tgl' WHERE id_currency = '$id' ";

    // cek apakah query update data berhasil
    if (mysqli_query($KONEKSI, $sql)) {
        echo "data dah berhasil di-update!";
    } else {
        echo "data gagal di-update!" . mysqli_affected_rows($KONEKSI);
    }

    return mysqli_affected_rows($KONEKSI);
}

//fungsi hapus currency
function hapus_currency()
{
    global $KONEKSI;
    $id_currency = $_GET['id'];

    //hapus file gambar yang usernya kita hapus
    $sql = "SELECT * FROM tbl_currency WHERE id_currency='$id_currency' " or die("data tidak ditemukan" . mysqli_error($KONEKSI));
    $hasil = mysqli_query($KONEKSI, $sql);
    $row = mysqli_fetch_assoc($hasil);

    // hapus data di tbl_currency
    $query_currency = "DELETE FROM tbl_currency WHERE id_currency='$id_currency'";
    mysqli_query($KONEKSI, $query_currency) or die("gagal ngapus data currency T-T" . mysqli_error($KONEKSI));

    return mysqli_affected_rows($KONEKSI);
}