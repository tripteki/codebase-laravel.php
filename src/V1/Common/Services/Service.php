<?php

namespace Src\V1\Common\Services;

class Service
{
    /**
     * @param mixed $importable
     * @param array $datas
     * @return \Maatwebsite\Excel\Excel
     */
    protected function importable($importable, $datas)
    {
        $data = null;

        if ($datas["file"]->getClientOriginalExtension() == "csv" || $datas["file"]->getClientOriginalExtension() == "txt") {

            $data = \Maatwebsite\Excel\Facades\Excel::import($importable, $datas["file"], null, \Maatwebsite\Excel\Excel::CSV);

        } else if ($datas["file"]->getClientOriginalExtension() == "xls") {

            $data = \Maatwebsite\Excel\Facades\Excel::import($importable, $datas["file"], null, \Maatwebsite\Excel\Excel::XLS);

        } else if ($datas["file"]->getClientOriginalExtension() == "xlsx") {

            $data = \Maatwebsite\Excel\Facades\Excel::import($importable, $datas["file"], null, \Maatwebsite\Excel\Excel::XLSX);
        }

        return $data;
    }

    /**
     * @param string $name
     * @param mixed $exportable
     * @param array $datas
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function exportable($name, $exportable, $datas)
    {
        $data = null;

        if ($datas["file"] == "csv") {

            $data = \Maatwebsite\Excel\Facades\Excel::download($exportable, $name.".csv", \Maatwebsite\Excel\Excel::CSV);

        } else if ($datas["file"] == "xls") {

            $data = \Maatwebsite\Excel\Facades\Excel::download($exportable, $name.".xls", \Maatwebsite\Excel\Excel::XLS);

        } else if ($datas["file"] == "xlsx") {

            $data = \Maatwebsite\Excel\Facades\Excel::download($exportable, $name.".xlsx", \Maatwebsite\Excel\Excel::XLSX);
        }

        return $data;
    }
};
