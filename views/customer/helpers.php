<?php
if (!function_exists('getStatusColor')) {
    function getStatusColor($status) {
        switch ($status) {
            case 'Pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'Processing':
                return 'bg-blue-100 text-blue-800';
            case 'Completed':
                return 'bg-green-100 text-green-800';
            case 'Cancelled':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
}

if (!function_exists('formatRupiah')) {
    function formatRupiah($price) {
        return "IDR " . number_format($price, 0, ',', '.');
    }
} 