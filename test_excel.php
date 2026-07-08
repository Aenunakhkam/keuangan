<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = [
    ['Name', 'Email'],
    ['John Doe', 'john@example.com'],
    ['Jane Smith', 'jane@example.com']
];

$export = new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
    private $data;
    public function __construct($data) { $this->data = $data; }
    public function array(): array { return $this->data; }
};

\Maatwebsite\Excel\Facades\Excel::store($export, 'test.xlsx');

$rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass, storage_path('app/test.xlsx'))[0];
print_r($rows);
