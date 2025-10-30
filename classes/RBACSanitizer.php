<?php
namespace App\Classes;

class RBACSanitizer {
    public static function normalize(string $value, string $normalize = ''): string {
        $data = trim((string)$value);

        switch ($normalize) {
            case 'upper'    : return mb_strtoupper($data, 'UTF-8');
            case 'lower'    : return mb_strtolower($data, 'UTF-8');
            case 'title'    : return mb_convert_case(mb_strtolower($data, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
            case 'numeric'  : return preg_replace('/[^0-9.]/', '', $data);
            case 'mobile'   : return preg_replace('/\D/', '', $data);
            case 'alnum'    : return preg_replace('/[^a-zA-Z0-9]/', '', $data);
            case 'password' :
                return preg_match(
                    '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.@$!%*?&])[A-Za-z\d.@$!%*?&]{8,}$/',
                    $data
                ) ? $data : '';
            case 'date'     :
                try {
                    $dt = new \DateTime($data);
                    return $dt->format('m/d/Y'); // local-only format
                } catch (\Exception $e) {
                    return ''; // invalid input
                }
            case 'string'   : return (string)$data;
            default         : return $data;
        }
    }
}


// use App\Classes\Sanitizer;
// $_SESSION['rbac']['username'] = RBACSanitizer::normalize($_POST['varUsername'], 'alnum');
// $_SESSION['rbac']['birthday'] = RBACSanitizer::normalize($_POST['varDateOfBirth'], 'string');