<?php
@$pages = $_GET['aksi'];
switch ($pages) {
    case 'tampil':
        include 'tampil.php';
        break;

    case 'tambah':
        include 'tambah.php';
        break;

    case 'edit':
        include 'edit.php';
        break;

    case 'hapus':
        include 'hapus.php';
        break;

    case 'proses_hapus':
        include 'proses_hapus.php';
        break;
    default:
        include 'tampil.php';
        break;
}
