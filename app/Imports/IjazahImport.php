<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Models\Blangko;
use App\Models\Sekolah;
use App\Models\Mst_wilayah;
/*use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;*/
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class IjazahImport implements ToCollection, WithStartRow, WithMapping, WithProgressBar
//ToCollection, WithStartRow, WithHeadingRow
{
    use Importable;

    /*public function startRow(): int
    {
        return 3;
    }

    */
    
    public function __construct(int $headingRow = 4, $tahun_ajaran_id = 2022)
    {
        $this->headingRow = $headingRow;
        $this->tahun_ajaran_id = $tahun_ajaran_id;
    }
    public function startRow(): int
    {
        return $this->headingRow;
    }
    public function map($row): array
    {
        
        return HeadingRowFormatter::format($row);
    }
    public function collection(Collection $rows)
    {
        $blangko_wilayah = [];
        foreach($rows as $item){
            $sekolah = Sekolah::where('npsn', $item[2])->first();
            if($sekolah){
                $data = [
                    'kabupaten_id' => $sekolah->kabupaten_id,
                    'provinsi_id' => $sekolah->provinsi_id,
                    'sekolah_id' => $sekolah->sekolah_id,
                    'start_3' => $item[3],
                    'end_3' => $item[4],
                    'jml_3' => $item[4] - $item[3] + 1,
                    'start_4' => $item[5],
                    'end_4' => $item[6],
                    'jml_4' => $item[6] - $item[5] + 1,
                ];
                $this->insert_data($data, 3);
                $this->insert_data($data, 4);
            }
        }
        //$this->insert_wilayah();
    }
    private function insert_data($data, $tahun){
        for($i=$data['start_'.$tahun];$i<=$data['end_'.$tahun];$i++){
            $urut = Str::padLeft($i, 7, 0);
            Blangko::updateOrCreate(
                [
                    'urut' => $urut,
                    'tahun' => $tahun,
                ],
                [
                    'kabupaten_id' => $data['kabupaten_id'],
                    'provinsi_id' => $data['provinsi_id'],
                    'nomor_seri' => 'M-SMK/K13-3/23/'.$urut,
                    'tahun_ajaran_id' => $this->tahun_ajaran_id,
                    'sekolah_id' => $data['sekolah_id'],
                    'start' => $i,
                    'end' => $i,
                ]
            );
        }
    }
    private function insert_wilayah(){
        $all_wilayah = Mst_wilayah::with('all_kabupaten')->whereNotNull('kode_dagri')->where('id_level_wilayah', 1)->orderBy('kode_wilayah')->get();
        foreach($all_wilayah as $wilayah){
            $start_blanko_prov_3 = Blangko::where($this->kondisi($wilayah, 3))->min('start');
            $end_blanko_prov_3 = Blangko::where($this->kondisi($wilayah, 3))->max('end');
            if($start_blanko_prov_3){
                Blangko::updateOrCreate(
                    [
                        'kode_wilayah' => $wilayah->kode_wilayah,
                        'tahun' => 3,
                        'nomor_seri' => '-',
                        'tahun_ajaran_id' => $this->tahun_ajaran_id,
                    ],
                    [
                        'jumlah' => $end_blanko_prov_3 - $start_blanko_prov_3 + 1,
                        'start' => $start_blanko_prov_3,
                        'end' => $end_blanko_prov_3,
                    ]
                );
            }
            $start_blanko_prov_4 = Blangko::where($this->kondisi($wilayah, 4))->min('start');
            $end_blanko_prov_4 = Blangko::where($this->kondisi($wilayah, 4))->max('end');
            if($start_blanko_prov_4){
                Blangko::updateOrCreate(
                    [
                        'kode_wilayah' => $wilayah->kode_wilayah,
                        'tahun' => 4,
                        'nomor_seri' => '-',
                        'tahun_ajaran_id' => $this->tahun_ajaran_id,
                    ],
                    [
                        'jumlah' => $end_blanko_prov_4 - $start_blanko_prov_4 + 1,
                        'start' => $start_blanko_prov_4,
                        'end' => $end_blanko_prov_4,
                    ]
                );
            }
        }
    }
    private function kondisi($wilayah, $tahun){
        return function($query) use ($wilayah, $tahun){
            $query->wherehas('sekolah', function($query) use ($wilayah){
                if($wilayah->id_level_wilayah == 1){
                    $query->where('provinsi_id', $wilayah->kode_wilayah);
                } else {
                    $query->where('kabupaten_id', $wilayah->kode_wilayah);
                }
            });
            $query->whereNotNull('jumlah');
            $query->where('tahun', $tahun);
        };
    }
}
