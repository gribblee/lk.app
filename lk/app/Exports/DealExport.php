<?php

namespace App\Exports;

use App\Models\Deal;
use Maatwebsite\Excel\Concerns\FromCollection;

class DealExport implements FromCollection
{

    public function __construct($deals)
    {
        $this->deals = $deals;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->deals->get();
    }

    public function headings(): array
    {
        //Здесь заголовки для полей
        return [
            'id'
        ];
    }

    public function query()
    {
        //Здесь условия выборки
        return Deal::query();
    }

    public function map($deal): array
    {
        return [
            $deal->id
        ];
    }
}
