<?php

if (!function_exists('timeAgoOrDate')) {
    function timeAgoOrDate($datetime)
    {
        if (empty($datetime)) {
            return '';
        }
        $timestamp = strtotime($datetime);
        if (!$timestamp) {
            return '';
        }
        $now = time();
        $diff = $now - $timestamp;

        if ($diff < 60) {
            return 'Baru saja';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "$minutes menit yang lalu";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "$hours jam yang lalu";
        } elseif ($diff < 172800) {
            return 'Kemarin';
        } elseif ($diff < 259200) {
            return '2 hari yang lalu';
        } else {
            $days = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];
            $months = [
                'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
            ];

            $day = $days[date('l', $timestamp)];
            $month = $months[date('F', $timestamp)];
            $dateNum = date('d', $timestamp);
            $year = date('Y', $timestamp);

            return "$day, $dateNum $month $year";
        }
    }
}
