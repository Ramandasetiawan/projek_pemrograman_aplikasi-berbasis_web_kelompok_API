<?php

function validate_and_save_image($file, $upload_dir = '../public/assets/images/', $max_size = 2097152) {
    $result = [
        'success' => false,
        'message' => '',
        'filename' => 'default.jpg'
    ];

    if (empty($file['name'])) {
        $result['message'] = 'Tidak ada file yang diupload';
        return $result;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'Error saat upload file: ' . $file['error'];
        return $result;
    }

    if ($file['size'] > $max_size) {
        $result['message'] = 'Ukuran file terlalu besar. Maksimal ' . ($max_size / 1024 / 1024) . 'MB';
        return $result;
    }

    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        $result['message'] = 'Tipe file tidak diizinkan. Hanya JPG, PNG, GIF, dan WEBP yang diperbolehkan';
        return $result;
    }

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        $result['message'] = 'Ekstensi file tidak diizinkan';
        return $result;
    }

    $safe_filename = uniqid('img_', true) . '.' . $file_extension;
    $upload_path = $upload_dir . $safe_filename;

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $result['success'] = true;
        $result['message'] = 'File berhasil diupload';
        $result['filename'] = $safe_filename;
    } else {
        $result['message'] = 'Gagal menyimpan file';
    }

    return $result;
}

function delete_image_safe($filename, $upload_dir = '../public/assets/images/') {

    if ($filename === 'default.jpg') {
        return false;
    }

    $file_path = $upload_dir . $filename;

    if (file_exists($file_path) && is_file($file_path)) {
        return unlink($file_path);
    }

    return false;
}

function sanitize_filename($filename) {

    $filename = basename($filename);

    $filename = str_replace(' ', '_', $filename);

    $filename = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $filename);

    return $filename;
}
?>
