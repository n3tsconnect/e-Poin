<?php
    if(isset($_GET['data_kelas'])){
        $rowNo = 0;
        $data = $koneksi->query("SELECT * FROM tb_kelas;");
        $result = array();
        while($kelas = $data->fetch_assoc()){
            $result[$rowNo] = array();
            $result[$rowNo]['id'] = $kelas['id_kelas'];
            $result[$rowNo]['text'] = $kelas['nama_kelas'];
            $rowNo++;
        }

        echo json_encode($result);
    }

    if(isset($_GET['data_nama'])){
        $rowNo = 0;
        $kelas = esc($_GET['kelas']);
        $data = $koneksi->query("SELECT id_pelajar, nama_pelajar FROM tb_pelajar WHERE kelas_pelajar = '$kelas';");
        $result = array();
        while($pelajar = $data->fetch_assoc()){
            $result[$rowNo] = array();
            $result[$rowNo]['id'] = $pelajar['id_pelajar'];
            $result[$rowNo]['text'] = $pelajar['nama_pelajar'];
            $rowNo++;
        }

        echo json_encode($result);
    }

    if(isset($_POST['submit_pelanggaran'])){
        $data = $_POST['data'];
        $id_guru = esc($_SESSION['id']);
        $setting = new DateTime('NOW', new DateTimeZone('Asia/Jakarta'));
        $tanggal = $setting->format('Y-m-d H:i:s');
        $str = "";
        foreach($data as $pelanggaran){
            $lihat          = $koneksi->query("SELECT poin_pelanggaran FROM tb_pelanggaran
            WHERE id_pelanggaran = '$pelanggaran[2]'");
            $data           = $lihat->fetch_assoc();
            $poin           = $data['poin_pelanggaran'];

            $koneksi->query("INSERT INTO tb_datapelanggar (id_pelajar, id_pelanggaran, poin_pelanggaran, id_guru, tanggal_pelanggaran) VALUES ('$pelanggaran[1]', '$pelanggaran[2]', '$poin', '$id_guru', '$tanggal')");
            $koneksi->query("UPDATE tb_pelajar SET poin_pelajar=(poin_pelajar + $poin) WHERE id_pelajar='$pelanggaran[1]'");
        }
        echo $str;
    }
?>