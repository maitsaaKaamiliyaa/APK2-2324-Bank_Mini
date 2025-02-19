<?php
@$pages = $_GET['pages'];
switch ($pages) {
    case 'tabungan':
        include '../pages/tabungan/table.php';
        break;
//     case 'user_petugas':
//         include '../pages/user petugas/user_petugas.php';
//         break;
//     case 'branch':
//         include '../pages/branch/branch.php';
//         break;
//     case 'user_karyawan':
//         include '../pages/karyawan/user_karyawan.php';
//         break;
//     case 'jabatan':
//         include '../pages/jabatan/jabatan.php';
//         break;
//     case 'user_pemilik':
//         include '../pages/user pemilik/user_pemilik.php';
//         break;
//     case 'bulan':
//         include '../pages/bulan/bulan.php';
//         break;
//     case 'tahun':
//         include '../pages/tahun/tahun.php';
//         break;
//     case 'currency':
//         include '../pages/currency/currency.php';
//         break;
//     case 'floor':
//         include '../pages/floor/floor.php';
//         break;
    default:
        include '../pages/tabungan/tampil.php';
        break;
    }
?>