<?php

namespace App\Services\Impl;

use App\Models\Dataset;
use App\Services\DatasetService;
use MathPHP\LinearAlgebra\MatrixFactory;

class DatasetServiceImpl implements DatasetService
{
    public function all()
    {
        return Dataset::all();
    }

    public function store(array $data)
    {
        return $this->save(new Dataset(), $data);
    }

    public function update(Dataset $dataset, array $data)
    {
        return $this->save($dataset, $data);
    }

    public function delete(Dataset $dataset)
    {
        $dataset->delete();
    }

    private function save(Dataset $dataset, array $data)
    {
        $dataset->nama_toko = $data['nama_toko'];
        $dataset->Y = $data['Y'];
        $dataset->X1 = $data['X1'];
        $dataset->X2 = $data['X2'];
        $dataset->X3 = $data['X3'];

        $dataset->X1Y = $dataset->X1 * $dataset->Y;
        $dataset->X2Y = $dataset->X2 * $dataset->Y;
        $dataset->X3Y = $dataset->X3 * $dataset->Y;

        $dataset->X1X2 = $dataset->X1 * $dataset->X2;
        $dataset->X1X3 = $dataset->X1 * $dataset->X3;
        $dataset->X2X3 = $dataset->X2 * $dataset->X3;

        $dataset->X1_square = pow($dataset->X1, 2);
        $dataset->X2_square = pow($dataset->X2, 2);
        $dataset->X3_square = pow($dataset->X3, 2);

        $dataset->save();

        return $dataset;
    }

    public function sums()
    {
        return Dataset::selectRaw('
            SUM(Y) as sum_Y,
            SUM(X1) as sum_X1,
            SUM(X2) as sum_X2,
            SUM(X3) as sum_X3,
            SUM(X1Y) as sum_X1Y,
            SUM(X2Y) as sum_X2Y,
            SUM(X3Y) as sum_X3Y,
            SUM(X1X2) as sum_X1X2,
            SUM(X1X3) as sum_X1X3,
            SUM(X2X3) as sum_X2X3,
            SUM(X1_square) as sum_X1_square,
            SUM(X2_square) as sum_X2_square,
            SUM(X3_square) as sum_X3_square
        ')->first();
    }

    public function count()
    {
        return Dataset::count();
    }

    public function matrixA()
    {
        $sums = $this->sums();
        $count = $this->count();

        $b1k1 = $count;
        $b1k2 = $sums->sum_X1;
        $b1k3 = $sums->sum_X2;
        $b1k4 = $sums->sum_X3;

        $b2k1 = $sums->sum_X1;
        $b2k2 = $sums->sum_X1_square;
        $b2k3 = $sums->sum_X1X2;
        $b2k4 = $sums->sum_X1X3;

        $b3k1 = $sums->sum_X2;
        $b3k2 = $sums->sum_X1X2;
        $b3k3 = $sums->sum_X2_square;
        $b3k4 = $sums->sum_X2X3;

        $b4k1 = $sums->sum_X3;
        $b4k2 = $sums->sum_X1X3;
        $b4k3 = $sums->sum_X2X3;
        $b4k4 = $sums->sum_X3_square;

        $matrixA = [
            'b1k1' => $b1k1,
            'b1k2' => $b1k2,
            'b1k3' => $b1k3,
            'b1k4' => $b1k4,
            'b2k1' => $b2k1,
            'b2k2' => $b2k2,
            'b2k3' => $b2k3,
            'b2k4' => $b2k4,
            'b3k1' => $b3k1,
            'b3k2' => $b3k2,
            'b3k3' => $b3k3,
            'b3k4' => $b3k4,
            'b4k1' => $b4k1,
            'b4k2' => $b4k2,
            'b4k3' => $b4k3,
            'b4k4' => $b4k4,
        ];

        return $matrixA;
    }

    public function matrixA1()
    {
        $sums = $this->sums();

        $b1k1 = $sums->sum_Y;
        $b1k2 = $sums->sum_X1;
        $b1k3 = $sums->sum_X2;
        $b1k4 = $sums->sum_X3;

        $b2k1 = $sums->sum_X1Y;
        $b2k2 = $sums->sum_X1_square;
        $b2k3 = $sums->sum_X1X2;
        $b2k4 = $sums->sum_X1X3;

        $b3k1 = $sums->sum_X2Y;
        $b3k2 = $sums->sum_X1X2;
        $b3k3 = $sums->sum_X2_square;
        $b3k4 = $sums->sum_X2X3;

        $b4k1 = $sums->sum_X3Y;
        $b4k2 = $sums->sum_X1X3;
        $b4k3 = $sums->sum_X2X3;
        $b4k4 = $sums->sum_X3_square;

        $matrixA1 = [
            'b1k1' => $b1k1,
            'b1k2' => $b1k2,
            'b1k3' => $b1k3,
            'b1k4' => $b1k4,
            'b2k1' => $b2k1,
            'b2k2' => $b2k2,
            'b2k3' => $b2k3,
            'b2k4' => $b2k4,
            'b3k1' => $b3k1,
            'b3k2' => $b3k2,
            'b3k3' => $b3k3,
            'b3k4' => $b3k4,
            'b4k1' => $b4k1,
            'b4k2' => $b4k2,
            'b4k3' => $b4k3,
            'b4k4' => $b4k4,
        ];

        return $matrixA1;
    }

    public function matrixA2()
    {
        $sums = $this->sums();
        $count = $this->count();

        $b1k1 = $count;
        $b1k2 = $sums->sum_Y;
        $b1k3 = $sums->sum_X2;
        $b1k4 = $sums->sum_X3;

        $b2k1 = $sums->sum_X1;
        $b2k2 = $sums->sum_X1Y;
        $b2k3 = $sums->sum_X1X2;
        $b2k4 = $sums->sum_X1X3;

        $b3k1 = $sums->sum_X2;
        $b3k2 = $sums->sum_X2Y;
        $b3k3 = $sums->sum_X2_square;
        $b3k4 = $sums->sum_X2X3;

        $b4k1 = $sums->sum_X3;
        $b4k2 = $sums->sum_X3Y;
        $b4k3 = $sums->sum_X2X3;
        $b4k4 = $sums->sum_X3_square;

        $matrixA2 = [
            'b1k1' => $b1k1,
            'b1k2' => $b1k2,
            'b1k3' => $b1k3,
            'b1k4' => $b1k4,
            'b2k1' => $b2k1,
            'b2k2' => $b2k2,
            'b2k3' => $b2k3,
            'b2k4' => $b2k4,
            'b3k1' => $b3k1,
            'b3k2' => $b3k2,
            'b3k3' => $b3k3,
            'b3k4' => $b3k4,
            'b4k1' => $b4k1,
            'b4k2' => $b4k2,
            'b4k3' => $b4k3,
            'b4k4' => $b4k4,
        ];

        return $matrixA2;
    }

    public function matrixA3()
    {
        $sums = $this->sums();
        $count = $this->count();

        $b1k1 = $count;
        $b1k2 = $sums->sum_X1;
        $b1k3 = $sums->sum_Y;
        $b1k4 = $sums->sum_X3;

        $b2k1 = $sums->sum_X1;
        $b2k2 = $sums->sum_X1_square;
        $b2k3 = $sums->sum_X1Y;
        $b2k4 = $sums->sum_X1X3;

        $b3k1 = $sums->sum_X2;
        $b3k2 = $sums->sum_X1X2;
        $b3k3 = $sums->sum_X2Y;
        $b3k4 = $sums->sum_X2X3;

        $b4k1 = $sums->sum_X3;
        $b4k2 = $sums->sum_X1X3;
        $b4k3 = $sums->sum_X3Y;
        $b4k4 = $sums->sum_X3_square;

        $matrixA3 = [
            'b1k1' => $b1k1,
            'b1k2' => $b1k2,
            'b1k3' => $b1k3,
            'b1k4' => $b1k4,
            'b2k1' => $b2k1,
            'b2k2' => $b2k2,
            'b2k3' => $b2k3,
            'b2k4' => $b2k4,
            'b3k1' => $b3k1,
            'b3k2' => $b3k2,
            'b3k3' => $b3k3,
            'b3k4' => $b3k4,
            'b4k1' => $b4k1,
            'b4k2' => $b4k2,
            'b4k3' => $b4k3,
            'b4k4' => $b4k4,
        ];

        return $matrixA3;
    }

    public function matrixA4()
    {
        $sums = $this->sums();
        $count = $this->count();

        $b1k1 = $count;
        $b1k2 = $sums->sum_X1;
        $b1k3 = $sums->sum_X2;
        $b1k4 = $sums->sum_Y;

        $b2k1 = $sums->sum_X1;
        $b2k2 = $sums->sum_X1_square;
        $b2k3 = $sums->sum_X1X2;
        $b2k4 = $sums->sum_X1Y;

        $b3k1 = $sums->sum_X2;
        $b3k2 = $sums->sum_X1X2;
        $b3k3 = $sums->sum_X2_square;
        $b3k4 = $sums->sum_X2Y;

        $b4k1 = $sums->sum_X3;
        $b4k2 = $sums->sum_X1X3;
        $b4k3 = $sums->sum_X2X3;
        $b4k4 = $sums->sum_X3Y;

        $matrixA4 = [
            'b1k1' => $b1k1,
            'b1k2' => $b1k2,
            'b1k3' => $b1k3,
            'b1k4' => $b1k4,
            'b2k1' => $b2k1,
            'b2k2' => $b2k2,
            'b2k3' => $b2k3,
            'b2k4' => $b2k4,
            'b3k1' => $b3k1,
            'b3k2' => $b3k2,
            'b3k3' => $b3k3,
            'b3k4' => $b3k4,
            'b4k1' => $b4k1,
            'b4k2' => $b4k2,
            'b4k3' => $b4k3,
            'b4k4' => $b4k4,
        ];

        return $matrixA4;
    }

    public function nilaiH()
    {
        $sums = $this->sums();

        $baris_1 = $sums->sum_Y;
        $baris_2 = $sums->sum_X1Y;
        $baris_3 = $sums->sum_X2Y;
        $baris_4 = $sums->sum_X3Y;

        $nilai_H = [
            'baris_1' => $baris_1,
            'baris_2' => $baris_2,
            'baris_3' => $baris_3,
            'baris_4' => $baris_4
        ];

        return $nilai_H;
    }

    public function calculateDeterminant($matrixData)
    {
        $matrix = [
            [$matrixData['b1k1'], $matrixData['b1k2'], $matrixData['b1k3'], $matrixData['b1k4']],
            [$matrixData['b2k1'], $matrixData['b2k2'], $matrixData['b2k3'], $matrixData['b2k4']],
            [$matrixData['b3k1'], $matrixData['b3k2'], $matrixData['b3k3'], $matrixData['b3k4']],
            [$matrixData['b4k1'], $matrixData['b4k2'], $matrixData['b4k3'], $matrixData['b4k4']],
        ];

        $matrixObject = MatrixFactory::create($matrix);
        $determinant = $matrixObject->det();

        return $determinant;
    }

    public function detA()
    {
        $matrixA = $this->matrixA();
        $determinant = $this->calculateDeterminant($matrixA);

        return $determinant;
    }

    public function detA1()
    {
        $matrixA1 = $this->matrixA1();
        $determinant = $this->calculateDeterminant($matrixA1);

        return $determinant;
    }

    public function detA2()
    {
        $matrixA2 = $this->matrixA2();
        $determinant = $this->calculateDeterminant($matrixA2);

        return $determinant;
    }

    public function detA3()
    {
        $matrixA3 = $this->matrixA3();
        $determinant = $this->calculateDeterminant($matrixA3);

        return $determinant;
    }

    public function detA4()
    {
        $matrixA4 = $this->matrixA4();
        $determinant = $this->calculateDeterminant($matrixA4);

        return $determinant;
    }

    public function nilaiB1()
    {
        $det_A1 = $this->detA1();
        $det_A = $this->detA();
        $nilai_b1 = $det_A1 / $det_A;

        return $nilai_b1;
    }

    public function nilaiB2()
    {
        $det_A2 = $this->detA2();
        $det_A = $this->detA();
        $nilai_b2 = $det_A2 / $det_A;

        return $nilai_b2;
    }

    public function nilaiB3()
    {
        $det_A3 = $this->detA3();
        $det_A = $this->detA();
        $nilai_b3 = $det_A3 / $det_A;

        return $nilai_b3;
    }

    public function nilaiB4()
    {
        $det_A4 = $this->detA4();
        $det_A = $this->detA();
        $nilai_b4 = $det_A4 / $det_A;

        return $nilai_b4;
    }
}
