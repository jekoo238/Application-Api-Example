<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = array(); // Membuat array kosong untuk data POST

    foreach ($_POST as $key => $value) {
        // Menambahkan data ke dalam array
        $data[$key] = $value;
    }

    // Mengubah array menjadi format JSON
    $json_data = json_encode($data);

    // Cetak hasilnya sebagai JSON
    echo $json_data;
}
?>