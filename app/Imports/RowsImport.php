<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class RowsImport implements ToArray
{
    public function array(array $array): array
    {
        return $array;
    }
}
