<?php
require_once __DIR__ . "\koneksi.php";

$sql_select_prov = "SELECT LEFT(kode, 2) AS kode_provinsi, nama AS `nama_provinsi`
                        FROM wilayah WHERE LENGTH(kode) = 2";
$res_select_prov = $conn->query($sql_select_prov);

if ($res_select_prov->num_rows > 0) {
    $conn->query("TRUNCATE TABLE provinsi");
    $conn->query("TRUNCATE TABLE kabupaten");
    $conn->query("TRUNCATE TABLE kecamatan");
    $conn->query("TRUNCATE TABLE desa");
    while ($row = $res_select_prov->fetch_assoc()) {
        $sql_insert_prov = "INSERT INTO provinsi (kode_provinsi, nama_provinsi) 
                            VALUES ('{$row['kode_provinsi']}', '{$row['nama_provinsi']}');";
        $conn->query($sql_insert_prov);
        $prov_last_id = mysqli_insert_id($conn);

        // select insert kabupaten
        $sql_select_kab = "SELECT LEFT(kode, 2) as kode_provinsi, 
                            RIGHT(LEFT(kode, 5), 2) as kode_kabupaten, 
                            nama as nama_kabupaten
                            FROM wilayah WHERE LENGTH(kode) = 5 && kode LIKE '{$row['kode_provinsi']}%'";
        $res_select_kab = $conn->query($sql_select_kab);
        if ($res_select_kab->num_rows > 0) {
            while ($row_kab = $res_select_kab->fetch_assoc()) {
                $sql_insert_kab = "INSERT INTO kabupaten (provinsi_id, kode_provinsi, kode_kabupaten, nama_kabupaten)
                                    VALUES ('{$prov_last_id}','{$row_kab['kode_provinsi']}', '{$row_kab['kode_kabupaten']}', '{$row_kab['nama_kabupaten']}')";
                $conn->query($sql_insert_kab);
                $kab_last_id = mysqli_insert_id($conn);

                // select insert kecamatan
                $kode_kab = "{$row_kab['kode_provinsi']}" . "." . "{$row_kab['kode_kabupaten']}" .  "%";
                $sql_select_kec = "SELECT LEFT(kode, 2) as kode_provinsi, 
                                    RIGHT(LEFT(kode, 5), 2) as kode_kabupaten, 
                                    RIGHT(kode, 2) as kode_kecamatan,
                                    nama as nama_kecamatan
                                    FROM wilayah WHERE LENGTH(kode) = 8 && kode LIKE '{$kode_kab}'";
                $res_select_kec = $conn->query($sql_select_kec);
                if ($res_select_kec->num_rows > 0) {
                    while ($row_kec = $res_select_kec->fetch_assoc()) {
                        $current_prov = $row_kec['kode_provinsi'];
                        $current_kab = $row_kec['kode_kabupaten'];
                        $current_kec = $row_kec['kode_kecamatan'];
                        $current_kec_nama = $row_kec['nama_kecamatan'];
                        $sql_insert_kec = "INSERT INTO kecamatan (kabupaten_id, kode_provinsi, kode_kabupaten, kode_kecamatan, nama_kecamatan)
                                            VALUES ('$kab_last_id', '$current_prov', '$current_kab', '$current_kec', '$current_kec_nama');";
                        $result_insert_kec = $conn->query($sql_insert_kec);
                        $kec_last_id = mysqli_insert_id($conn);

                        // select insert desa
                        $kode_kec = "%" . "{$row_kec['kode_provinsi']}" . "." . "{$row_kec['kode_kabupaten']}" . "." . "{$row_kec['kode_kecamatan']}" . "%";
                        $sql_select_desa = "SELECT LEFT(kode, 2) as kode_provinsi, 
                                            RIGHT(LEFT(kode, 5), 2) as kode_kebupaten,
                                            RIGHT(LEFT(kode, 8), 2) as kode_kecamatan,
                                            RIGHT(kode, 4) as kode_desa,
                                            nama as nama_desa
                                            FROM wilayah WHERE LENGTH(kode) = 13 && kode LIKE '{$kode_kec}'";
                        $res_select_desa = $conn->query($sql_select_desa);
                        if ($res_select_desa->num_rows > 0) {
                            while ($row_desa = $res_select_desa->fetch_assoc()) {
                                $sql_insert_desa = "INSERT INTO desa (kecamatan_id, kode_provinsi, kode_kabupaten, kode_kecamatan, kode_desa, nama_desa)
                                                    VALUES ('{$kec_last_id}', 
                                                        '{$row_desa['kode_provinsi']}', 
                                                        '{$row_desa['kode_kebupaten']}', 
                                                        '{$row_desa['kode_kecamatan']}', 
                                                        '{$row_desa['kode_desa']}', 
                                                        '{$row_desa['nama_desa']}')";
                                $conn->query($sql_insert_desa);
                            }
                        } else {
                            echo "select error desa" . PHP_EOL;
                        }
                        
                        
                    }
                } else {
                    echo "select erro kecamatan" . PHP_EOL;
                }
                

            }
        } else {
            echo "select kab erro" . PHP_EOL;
        }
        

    }
} else {
    echo "0 results";
}

$conn->close();
