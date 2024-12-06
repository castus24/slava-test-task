<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RowResource;
use App\Models\Row;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetImportedController extends Controller
{
    public function getRows(): AnonymousResourceCollection
    {
        $data = Row::query()
            ->orderBy('date')
            ->paginate(15);

        return RowResource::collection($data);
    }
}
