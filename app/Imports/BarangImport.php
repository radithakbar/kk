<?php
namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;

class BarangImport implements ToModel
{
    public function model(array $row)
    {
        return new Barang([
            'kode_barang' => $row[0],
            'nama' => $row[1],
            'merek' => $row[2],
            'qty' => $row[3],
            'kondisi' => $row[4],
        ]);
    }
}