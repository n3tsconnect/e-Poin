<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="web/css/select2.bootstrap4.min.css">

<div class="content mt-3">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <strong class="card-title">Bulk Insert</strong>
                </div>
                <div class="card-body">
                    <form id="form-insert">
                        <div class="col-md-2">
                            <div class="form-group">
                                <select id='kelas-select' name="kelas_pelajar" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            <select id='nama-select' name="input-nama" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="pelanggaran-select" name="jenis-pelanggaran" class="form-control">
                                <?php
                                    $data = $koneksi->query("SELECT * FROM tb_pelanggaran ORDER BY id_pelanggaran");
                                    while ($pelanggaran = $data->fetch_assoc()){
                                        echo "<option data-poin='$pelanggaran[poin_pelanggaran]' value='$pelanggaran[id_pelanggaran]'>$pelanggaran[nama_pelanggaran]</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button onclick='tambahPelanggaran("#form-insert")' type="button" class="btn btn-success">
                            <i class="fa fa-plus"></i>  Tambah</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button onclick="submitPelanggaran(tempData)" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-check"></i> Submit</button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <strong class="card-title">Daftar Submit</strong>
                </div>
                <div class="card-body">
                    <table id="tabel-pelanggaran" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th><th>Nama</th><th>Pelanggaran</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="web/js/lib/data-table/datatables.min.js"></script>
<script src="web/js/lib/data-table/dataTables.bootstrap.min.js"></script>
<script src="web/js/lib/data-table/dataTables.buttons.min.js"></script>
<script src="web/js/lib/data-table/buttons.bootstrap.min.js"></script>
<script src="web/js/lib/data-table/jszip.min.js"></script>
<script src="web/js/lib/data-table/pdfmake.min.js"></script>
<script src="web/js/lib/data-table/vfs_fonts.js"></script>
<script src="web/js/lib/data-table/buttons.html5.min.js"></script>
<script src="web/js/lib/data-table/buttons.print.min.js"></script>
<script src="web/js/lib/data-table/buttons.colVis.min.js"></script>
<script src="web/js/lib/data-table/datatables-init.js"></script>

<script type="text/javascript">
    // Array data untuk disubmit ke api.
    var tempData = [];

    // Array display untuk data source ke tabel pelanggaran.
    var tempDisplay = [];


    function tambahPelanggaran(formId){
        var form = $(formId);
        var formData = form.serializeArray();
        var pelanggaranData = [];
        var pelanggaranDisplay = [];
        
        pelanggaranData[0] = formData[0]['value'];
        pelanggaranData[1] = formData[1]['value'];
        pelanggaranData[2] = formData[2]['value'];

        pelanggaranDisplay[0] = jQuery('#kelas-select').select2('data')[0]['text'];
        pelanggaranDisplay[1] = jQuery('#nama-select').select2('data')[0]['text'];
        pelanggaranDisplay[2] = $('#pelanggaran-select option:selected').text();

        tempData.push(pelanggaranData);
        tempDisplay.push(pelanggaranDisplay);

        $('#tabel-pelanggaran').DataTable().ajax.reload();
    }

    function submitPelanggaran(pelanggaran){
        $.ajax({
            type: "POST",
            url: "api.php?halaman=piket&aksi=bulkinsert",
            data: {
                submit_pelanggaran: 1,
                data: tempData
            },
            success: function(data){
                alert("SUKSES!");
            }
        });
    }

    // Setiap kali kita memilih pada option kelas, opsi nama
    // siswa berubah sesuai kelas yang dipilih.
    $(document).ready(function() {
        jQuery('#kelas-select').on('select2:select', function (e) {
            $.ajax({
                type: "GET",
                url: "api.php?halaman=piket&aksi=bulkinsert",
                data: {
                    data_nama: 1,
                    kelas: e.params.data.id
                },
                success: function(data){
                    currentNamaData = JSON.parse(data);
                    // Delete semua instance opsi nama sebelumnya
                    $("#nama-select option").remove();
                    jQuery('#nama-select').select2({
                        theme: "bootstrap4",
                        data: currentNamaData
                    });
                }
            });
        });
    });
    
    // Inisialisasi DataTable dengan data source ajax yang merujuk
    // ke local array agar bisa menggunakan fungsi ajax.reload().
    $(document).ready(function() {
        $('#tabel-pelanggaran').DataTable({
            ajax: function(data, callback, settings){
                callback({ data: tempDisplay });
            },
            columns: [
                { title: "Kelas" },
                { title: "Nama Pelajar" },
                { title: "Pelanggaran"}
            ]
        });
    });

    // Inisialisasi Select2 data kelas menggunakan array,
    // karena kalau pakai ajax langsung ketika search
    // selalu membuat request ke url source data.
    $(document).ready(function() {      
        $.ajax({
            type: "GET",
            url: "api.php?halaman=piket&aksi=bulkinsert",
            data: {
                data_kelas: 1
            },
            success: function(data){
                kelasData = JSON.parse(data);
                jQuery('#kelas-select').select2({
                    theme: "bootstrap4",
                    data: kelasData
                });
                jQuery('#kelas-select').select2('focus');
            }
        });

        $.ajax({
            type: "GET",
            url: "api.php?halaman=piket&aksi=bulkinsert",
            data: {
                data_nama: 1
            },
            success: function(data){
                namaData = JSON.parse(data);
                jQuery('#nama-select').select2({
                    theme: "bootstrap4",
                    data: namaData
                });
            }
        });
    });
</script>
