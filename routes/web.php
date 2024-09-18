<?php

use App\Http\Controllers\CsvtoXmlconvertorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('csv-to-xml-convertor', [CsvtoXmlconvertorController::class, 'convertCsvToXml']);
