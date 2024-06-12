<?php

namespace App\Services;

use Exception;

class PixelsService
{
    protected $baseUrl;
    protected $accessToken;

    public function __construct()
    {
        $this->baseUrl = 'https://api.pexels.com/v1/';
        $this->accessToken = env('PIXELS_ACCESS_TOKEN');
    }

    /**
     * =================================================================================================
     * Photos Search
     * =================================================================================================
     */
    public function searchPhotos($query, $page = 1, $per_page = 20)
    {
        try {
            $params = [
                'page' => $page,
                'per_page' => $per_page,
                'query' => $query,
                'size' => 'medium',
                'orientation' => 'landscape',
                'locale' => 'en-US',
            ];

            $url = $this->baseUrl . 'search?' . http_build_query($params);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $this->accessToken,
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);
            $data = json_decode($response, true);

            if ($httpCode != 200) throw new Exception('Pexels API error: ' . $response);

            return [
                'status' => '200',
                'data' => $data
            ];
        } catch (Exception $e) {
            return [
                'status' => '500',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * =================================================================================================
     * Single Photo
     * =================================================================================================
     */
    public function viewPhoto($id)
    {
        try {
            $params = [
                'id' => $id,
            ];

            $url = $this->baseUrl . 'photos?' . http_build_query($params);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $this->accessToken,
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            if ($httpCode != 200) throw new Exception('Pexels API error: ' . $response);
            $data = json_decode($response, true);

            return [
                'status' => '200',
                'data' => $data
            ];
        } catch (Exception $e) {
            return [
                'status' => '500',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * =================================================================================================
     * Video Search
     * =================================================================================================
     */
    public function searchVideo($query, $page = 1, $per_page = 20)
    {
        try {
            $params = [
                'page' => $page,
                'per_page' => $per_page,
                'query' => $query,
                'size' => 'medium',
                'orientation' => 'landscape',
                'locale' => 'en-US',
            ];

            $url = $this->baseUrl . 'videos/search?' . http_build_query($params);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $this->accessToken,
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            if ($httpCode != 200) throw new Exception('Pexels API error: ' . $response);
            $data = json_decode($response, true);

            return [
                'status' => '200',
                'data' => $data
            ];
        } catch (Exception $e) {
            return [
                'status' => '500',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * =================================================================================================
     * Single Video
     * =================================================================================================
     */
    public function viewVideo($id)
    {
        try {
            $params = [
                'id' => $id,
            ];

            $url = $this->baseUrl . 'videos/videos?' . http_build_query($params);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $this->accessToken,
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            if ($httpCode != 200) throw new Exception('Pexels API error: ' . $response);
            $data = json_decode($response, true);

            return [
                'status' => '200',
                'data' => $data
            ];
        } catch (Exception $e) {
            return [
                'status' => '500',
                'error' => $e->getMessage()
            ];
        }
    }
}
