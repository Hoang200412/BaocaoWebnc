<?php

namespace App\Helpers;

class LocationHelper
{
    /**
     * Vietnam provinces/cities with districts and wards
     * Simplified data structure for common areas
     */
    public static function getProvinces()
    {
        return [
            '01' => [
                'name' => 'Hà Nội',
                'districts' => [
                    '001' => [
                        'name' => 'Hoàn Kiếm',
                        'wards' => ['Hàng Đồng', 'Hàng Gai', 'Hàng Bạc', 'Cửa Đông', 'Cửa Nam', 'Lý Thái Tổ', 'Tràng Tiền']
                    ],
                    '002' => [
                        'name' => 'Ba Đình',
                        'wards' => ['Phúc Tân', 'Trúc Bạch', 'Thụy Khuê', 'Đội Cấn', 'Điện Biên Phủ', 'Nguyễn Thái Học']
                    ],
                    '003' => [
                        'name' => 'Hai Bà Trưng',
                        'wards' => ['Hàng Mã', 'Máy Tơ', 'Thanh Nhàn', 'Tôn Đức Thắng', 'Minh Khai', 'Lê Đại Hành']
                    ],
                    '004' => [
                        'name' => 'Đống Đa',
                        'wards' => ['Văn Miếu', 'Liễu Giai', 'Láng Thượng', 'Láng Hạ', 'Ô Cho Dừa', 'Thổ Quan', 'Trường Chinh']
                    ],
                    '005' => [
                        'name' => 'Tây Hồ',
                        'wards' => ['Phú Thượng', 'Phương Canh', 'Quảng An', 'Nhật Tân', 'Tây Hồ', 'Thợ Nhuộm']
                    ]
                ]
            ],
            '02' => [
                'name' => 'TP. Hồ Chí Minh',
                'districts' => [
                    '701' => [
                        'name' => 'Quận 1',
                        'wards' => ['Bến Nghé', 'Bến Thành', 'Cầu Kho', 'Cầu Ông Lãnh', 'Đa Kao', 'Nguyễn Hữu Cảnh', 'Phạm Ngũ Lão']
                    ],
                    '702' => [
                        'name' => 'Quận 2',
                        'wards' => ['An Khánh', 'An Phú', 'Bình An', 'Bình Khánh', 'Bình Trưng Đông', 'Bình Trưng Tây', 'Cát Lái']
                    ],
                    '703' => [
                        'name' => 'Quận 3',
                        'wards' => ['Võ Thị Sáu', 'Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 6']
                    ],
                    '704' => [
                        'name' => 'Quận 4',
                        'wards' => ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 6', 'Phường 7']
                    ],
                    '705' => [
                        'name' => 'Quận 5',
                        'wards' => ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 6', 'Phường 7']
                    ]
                ]
            ],
            '03' => [
                'name' => 'Đà Nẵng',
                'districts' => [
                    '101' => [
                        'name' => 'Quận Hải Châu',
                        'wards' => ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 6']
                    ],
                    '102' => [
                        'name' => 'Quận Thanh Khê',
                        'wards' => ['Phường Thanh Khê Tây', 'Phường Thanh Khê Đông', 'Phường Tân Chính', 'Phường Chính Gian']
                    ]
                ]
            ]
        ];
    }

    /**
     * Get districts for a specific province
     */
    public static function getDistrictsByProvince($provinceCode)
    {
        $provinces = self::getProvinces();
        return $provinces[$provinceCode]['districts'] ?? [];
    }

    /**
     * Get wards for a specific district
     */
    public static function getWardsByDistrict($provinceCode, $districtCode)
    {
        $provinces = self::getProvinces();
        return $provinces[$provinceCode]['districts'][$districtCode]['wards'] ?? [];
    }

    /**
     * Calculate shipping fee based on distance/district
     * Simple calculation based on district codes
     */
    public static function calculateShippingFee($provinceCode, $districtCode)
    {
        $baseFee = 20000; // Base fee in VND

        // Distance-based fee multiplier
        $distanceMultiplier = 1;

        // Hanoi districts
        if ($provinceCode === '01') {
            $distanceMultiplier = match($districtCode) {
                '001', '002', '003', '004' => 1, // Inner districts
                default => 1.5 // Outer districts
            };
        }
        // Ho Chi Minh City districts
        elseif ($provinceCode === '02') {
            $distanceMultiplier = match($districtCode) {
                '701', '702', '703' => 1, // Central districts
                default => 1.5 // Peripheral districts
            };
        }
        // Da Nang
        elseif ($provinceCode === '03') {
            $distanceMultiplier = 1.2;
        }
        // Other provinces: higher fee
        else {
            $distanceMultiplier = 2;
        }

        return round($baseFee * $distanceMultiplier);
    }

    /**
     * Format location address from components
     */
    public static function formatAddress($provinceCode, $districtCode, $ward, $streetAddress = null)
    {
        $provinces = self::getProvinces();
        $province = $provinces[$provinceCode]['name'] ?? '';
        $district = $provinces[$provinceCode]['districts'][$districtCode]['name'] ?? '';

        $parts = array_filter([
            $streetAddress,
            $ward,
            $district,
            $province
        ]);

        return implode(', ', $parts);
    }
}
