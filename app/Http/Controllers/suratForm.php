<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\surat;
use App\Models\suratKematian;
use App\Models\jenisSurat;
use App\Models\suratMasuk;
use App\Http\Requests\suratReq;
use App\Http\Requests\jenisSuratReq;
use App\Http\Requests\suratMasukReq;

class suratForm extends Controller
{

    public function getData()
    {
        $data = surat::all();
        $response = $data;

        return response() -> json(['message' => 'all data', 'data' => $response ], 201);
    }

    public function addData(suratReq $request)
    {

        $data = surat::create($request -> all());
        $response = $data;

        return response() -> json(['message' => $response], 201);
    }

    public function addDataKematian($id)
    {
        $surat = surat::find($id);
        if (!$surat) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        $dataKematian = suratKematian::create([
            'nik' => $surat->nik,
            'nama' => $surat->nama,
            'keterangan' => $surat->keterangan,
        ]);
        $surat->delete();
        return response()->json(['message' => 'Data moved to suratKematian', 'data' => $dataKematian], 200);
    }

    public function addSurat(jenisSuratReq $request)
    {
        $data = jenisSurat::create($request -> all());
        $response = $data;

        return response() -> json(['message' => $response]);
    }

    public function addSuratMasuk(suratMasukReq $request)
    {
        $data = suratMasuk::create($request -> all());
        $response = $data;

        return response()->json(['message' => $response]);
    }


    public function approvedSurat($id)
    {
        $data = suratMasuk::find($id);

        if($data){
            $data->approved = true;
            $data->save();
        }

        return response()->json(['message' => $data]);
    }

    public function getSuratBelumApproved()
    {
        // Run the query using Eloquent
        $suratBelumApproved = SuratMasuk::select('surat_masuks.id', 'surat_masuks.nik', 'surat_masuks.nama','surat_masuks.created_at', 'surat_masuks.approved', 'jenis_surats.kategori_surat')
            ->leftJoin('jenis_surats', 'surat_masuks.id_kategori_surat', '=', 'jenis_surats.id')
            ->where('surat_masuks.approved', '=', 0)
            ->get();
            $totalData = $suratBelumApproved->count();
        // Return the result as a JSON response
        return response()->json([
            'total' => $totalData,
            'data' => $suratBelumApproved,

        ]);
    }

    public function getSuratApprovedKematian()
    {

        $suratApprovedKematian = SuratMasuk::select('surat_masuks.id', 'surat_masuks.nik', 'surat_masuks.nama', 'surat_masuks.approved', 'jenis_surats.kategori_surat')
            ->leftJoin('jenis_surats', 'surat_masuks.id_kategori_surat', '=', 'jenis_surats.id')
            ->where('surat_masuks.approved', '=', 1)
            ->where('surat_masuks.id_kategori_surat', '=', 1)
            ->get();

        $totalData = $suratApprovedKematian->count();


        return response()->json([
            'data' => $suratApprovedKematian,
            'total' => $totalData,
        ]);
    }


    public function getSuratApprovedNikah()
    {

        $suratApprovedKematian = SuratMasuk::select('surat_masuks.id', 'surat_masuks.nik', 'surat_masuks.nama', 'surat_masuks.approved', 'jenis_surats.kategori_surat')
            ->leftJoin('jenis_surats', 'surat_masuks.id_kategori_surat', '=', 'jenis_surats.id')
            ->where('surat_masuks.approved', '=', 1)
            ->where('surat_masuks.id_kategori_surat', '=', 2)
            ->get();

        $totalData = $suratApprovedKematian->count();


        return response()->json([
            'data' => $suratApprovedKematian,
            'total' => $totalData,
        ]);
    }

    public function getSuratApprovedCerai()
    {

        $suratApprovedKematian = SuratMasuk::select('surat_masuks.id', 'surat_masuks.nik', 'surat_masuks.nama', 'surat_masuks.approved', 'jenis_surats.kategori_surat')
            ->leftJoin('jenis_surats', 'surat_masuks.id_kategori_surat', '=', 'jenis_surats.id')
            ->where('surat_masuks.approved', '=', 1)
            ->where('surat_masuks.id_kategori_surat', '=', 3)
            ->get();

        $totalData = $suratApprovedKematian->count();


        return response()->json([
            'data' => $suratApprovedKematian,
            'total' => $totalData,
        ]);
    }


    public function getSuratApprovedOtomatis($id_kategori_surat)
    {
        // Run the query using Eloquent
        $suratApproved = SuratMasuk::select('surat_masuks.id', 'surat_masuks.nik', 'surat_masuks.nama', 'surat_masuks.approved', 'jenis_surats.kategori_surat')
            ->leftJoin('jenis_surats', 'surat_masuks.id_kategori_surat', '=', 'jenis_surats.id')
            ->where('surat_masuks.approved', '=', 1)
            ->where('surat_masuks.id_kategori_surat', '=', $id_kategori_surat)
            ->get();

        // Get the total count
        $totalData = $suratApproved->count();

        // Return the result as a JSON response with the total count
        return response()->json([
            'data' => $suratApproved,
            'total' => $totalData,
            'message' => "Successfully retrieved surat approved data for kategori_surat = $id_kategori_surat with total count."
        ]);
    }


    public function getSuratApprovedByCategory($kategori_surat)
    {
        // Find the JenisSurat by name
        $jenisSurat = JenisSurat::where('kategori_surat', $kategori_surat)->first();

        if (!$jenisSurat) {
            return response()->json(['message' => 'Invalid kategori_surat'], 404);
        }

        $id_kategori_surat = $jenisSurat->id;

        // Run the query using Eloquent
        $suratApproved = SuratMasuk::select('surat_masuks.id', 'surat_masuks.nik', 'surat_masuks.nama', 'surat_masuks.approved', 'jenis_surats.kategori_surat')
            ->leftJoin('jenis_surats', 'surat_masuks.id_kategori_surat', '=', 'jenis_surats.id')
            ->where('surat_masuks.approved', '=', 1)
            ->where('surat_masuks.id_kategori_surat', '=', $id_kategori_surat)
            ->get();

        // Get the total count
        $totalData = $suratApproved->count();

        // Return the result as a JSON response with the total count
        return response()->json([
            'data' => $suratApproved,
            'total' => $totalData,
            'message' => "Successfully retrieved surat approved data for kategori_surat = $kategori_surat with total count."
        ]);
    }
}
