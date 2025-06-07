<?php
session_start();
include 'admin/koneksi.php';

$id_produk = $_POST['id_produk'];
$id_user   = $_POST['id_user'];
$jumlah    = (int) $_POST['jumlah'];
$harga     = (int) $_POST['harga'];
$redirect  = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : 'belanja.php';

$cek = mysqli_query($koneksi, "SELECT stok FROM tb_produk WHERE id_produk = '$id_produk'");
$data = mysqli_fetch_assoc($cek);

if ($data && $data['stok'] >= $jumlah) {
    $total = $jumlah * $harga;


    $getLast = mysqli_query($koneksi, "SELECT id_pesanan FROM tb_pesanan 
    ORDER BY id_pesanan DESC LIMIT 1");
    $lastData = mysqli_fetch_assoc($getLast);

    if ($lastData) {
        $lastId = (int) substr($lastData['id_pesanan'], 1);
        $newId = $lastId + 1;
    } else {
        $newId = 1;
    }

    $id_pesanan = 'M' . str_pad($newId, 3, '0', STR_PAD_LEFT);


    $insert = mysqli_query($koneksi, "INSERT INTO tb_pesanan (id_pesanan, id_produk, qty, total, id_user) 
    VALUES (
        '$id_pesanan', '$id_produk', $jumlah, $total, '$id_user')");

    if ($insert) {
        
        mysqli_query($koneksi, "UPDATE tb_produk SET stok = stok - $jumlah WHERE id_produk = '$id_produk'");
        echo "<script>alert('Produk berhasil ditambahkan ke pesanan.'); 
        window.location.href='cart.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan pesanan.'); 
        window.location.href='$redirect';</script>";
    }
} else {
    echo "<script>alert('Stok tidak mencukupi.'); 
    window.location.href='$redirect';</script>";
}
?>

