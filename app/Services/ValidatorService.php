<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ValidatorService
{
    public function validateRow(array $row, int $lineNumber): array
    {
        $validator = Validator::make($row, [
            '0' => 'required|integer|min:1|max:9999999999|unique:rows,developer_id',
            '1' => 'required|regex:/^[a-zA-Z\s]+$/',
            '2' => 'required|date_format:d.m.Y|before_or_equal:today',
        ], [
            '0.unique' => 'Line ' . $lineNumber . ': developer_id ' . $row[0] . ' already exists in the database.',
            '1.regex' => 'Line ' . $lineNumber . ': developer_id ' . $row[1] . ' does not match the format.',
            '2.date_format' => 'Line ' . $lineNumber . ': developer_id ' . $row[2] . ' does not match the format.',
        ]);

        if ($validator->fails()) {
            return [
                'is_valid' => false,
                'error' => $lineNumber . ' - ' . implode(', ', $validator->errors()->all()),
            ];
        }

        return [
            'is_valid' => true,
            'data' => [
                'developer_id' => $row[0],
                'name' => $row[1],
                'date' => Carbon::createFromFormat('d.m.Y', $row[2]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
    }
}
