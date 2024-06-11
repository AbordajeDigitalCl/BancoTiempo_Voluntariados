<?php

namespace App\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Validator;

trait Suplementary {

    function getExternalData($url) {
        $response = Http::withOptions(['verify' => false])->get($url);
        if ($response->ok()) {
            $data = $response->json();
            return $data;
        } else {
            return null;
        }
    }

    function sendJsonData($url, $jsonData, $token) {
        $curl = curl_init();  
           
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        
        $headers = [];
        $headers[] = 'Content-Type:application/json';    
        $headers[] = "Authorization: Bearer ".$token;

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    /*function sendJsonData($url, $jsonData) {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1lIjoiamF2aWVyc290b2FudGlodWFsQGdtYWlsLmNvbSIsImh0dHA6Ly9zY2hlbWFzLnhtbHNvYXAub3JnL3dzLzIwMDUvMDUvaWRlbnRpdHkvY2xhaW1zL25hbWVpZGVudGlmaWVyIjoiOTBiMDBmZjYtMjYxYS00NzI5LThjNWUtOTNjOTBkNDgxZmY0IiwibmJmIjoiMTY4NDA2Njk0MSIsImV4cCI6IjE2ODQxNTMzNDEifQ.XwfOVs4SXQ4ibxFj3BurOGMgNT713xoXzg0-cfnC-Ls',
            'Content-Length' => strlen($jsonData),
            'Host' => 'integracion.fedok.cl:455'
        ];
    
        $response = Http::withHeaders($headers)->withOptions(['verify' => false])->post($url, $jsonData);
        if ($response->ok()) {
            $data = $response->json();
            return $data;
        } else {
            return null;
        }
    }*/
}