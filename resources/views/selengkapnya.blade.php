<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parameters Explanation - LEO Satellite Orbit Simulation</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #007bff;
            --dark-bg: #1a1a1a;
            --light-text: #f0f0f0;
            --medium-gray: #e0e0e0;
            --dark-gray: #333;
            --card-bg: #ffffff;
            --border-radius: 8px;
            --transition-speed: 0.3s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--medium-gray);
            color: var(--dark-gray);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        /* Navbar Styling (copied from your original index.blade.php for consistency) */
        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            background-color: var(--dark-bg); /* Made solid for parameter page */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Always has shadow */
            position: sticky; /* Sticky, not fixed, for simpler scroll */
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .logo img {
            height: 55px;
            margin-right: 15px;
            transition: transform var(--transition-speed);
        }

        .logo img:hover {
            transform: scale(1.05);
        }

        .nav-links {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .nav-links li {
            margin-left: 40px;
        }

        .nav-links a {
            color: var(--light-text);
            text-decoration: none;
            font-size: 1.1em;
            font-weight: 500;
            transition: color var(--transition-speed), transform var(--transition-speed);
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--primary-blue);
            left: 50%;
            bottom: -5px;
            transform: translateX(-50%);
            transition: width var(--transition-speed);
        }

        .nav-links a:hover {
            color: var(--primary-blue);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* Common Title Styling (copied) */
        .common-title-section {
            padding: 80px 5% 40px;
            text-align: center;
            background-color: var(--medium-gray);
        }

        .common-title-section .title {
            margin-bottom: 60px;
        }

        .common-title-section .title h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 3em;
            color: var(--dark-gray);
            position: relative;
            display: inline-block;
            font-weight: 700;
        }

        .common-title-section .title h1::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 4px;
            background-color: var(--primary-blue);
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        /* Styling for Parameters Page */
        .parameter-section {
            padding: 80px 5%;
            background-color: var(--medium-gray);
            color: var(--dark-gray);
        }

        .parameter-section .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .parameter-item {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }

        .parameter-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .parameter-item h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2em;
            color: var(--primary-blue);
            margin-bottom: 20px;
        }

        .parameter-item img {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .parameter-item p {
            font-size: 1.05em;
            line-height: 1.7;
            color: #555;
            text-align: justify;
        }

        /* Styling for Parameters Page */
         .single-section {
            padding: 80px 5%;
            background-color: var(--medium-gray);
            color: var(--dark-gray);
        }

        .single-section .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .single-item {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }

        /* Styling baru untuk gambar dalam langkah-langkah */
.step-images {
    display: flex;         /* Menggunakan flexbox untuk penataan gambar */
    flex-direction: column; /* Menumpuk gambar secara vertikal */
    align-items: center;   /* Pusatkan gambar secara horizontal */
    width: 100%;           /* Pastikan container mengambil lebar penuh */
    margin-top: 15px;      /* Jarak dari teks poin di atasnya */
    margin-bottom: 15px;   /* Jarak ke elemen berikutnya */
}

.step-images img {
    max-width: 90%;        /* Maksimal lebar gambar 90% dari containernya (agar ada sedikit padding) */
    height: auto;          /* Menjaga rasio aspek gambar */
    border-radius: var(--border-radius);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 10px;   /* Jarak antar gambar jika ada lebih dari satu */
}

/* Hapus margin bawah untuk gambar terakhir dalam satu blok step-images */
.step-images img:last-child {
    margin-bottom: 0;
}
        /* Styling untuk Parameters Page dan Single Constellation - pastikan tidak ada konflik */
/* ... (kode CSS Anda yang sudah ada) ... */

/* Tambahkan/modifikasi styling berikut untuk daftar langkah-langkah */
.single-item .single-constellation-steps {
    list-style-type: none; /* Sembunyikan bullet default, kita akan buat kustom */
    padding-left: 0;       /* Atur ulang padding default UL */
    margin: 0;
    text-align: left;
    width: 100%;
}

.single-item .single-constellation-steps > li {
    margin-bottom: 15px;   /* Beri jarak antar poin utama */
    line-height: 1.6;      /* Tingkatkan jarak antar baris teks */
    display: flex;         /* Gunakan flexbox untuk LI */
    flex-direction: column; /* Tumpuk konten LI (teks dan gambar) secara vertikal */
    align-items: flex-start; /* Sejajarkan konten LI ke kiri */
    position: relative;    /* Diperlukan untuk penempatan bullet kustom */
    padding-left: 2em;     /* Beri ruang di kiri untuk bullet dan indentasi teks */
}

/* Menyembunyikan bullet default dan menambahkannya kembali dengan pseudo-element
   untuk kontrol posisi yang lebih baik */
.single-item .single-constellation-steps > li::before {
    content: "\2022"; /* Karakter bullet solid */
    color: var(--dark-gray);
    position: absolute;
    left: 0.5em; /* Sesuaikan posisi horizontal bullet */
    top: 0.2em; /* Sesuaikan posisi vertikal bullet */
    font-size: 1em;
}

/* Styling untuk sub-list (poin bersarang) */
.single-item .nested-steps {
    list-style-type: none; /* Sembunyikan bullet default */
    padding-left: 0;       /* Atur ulang padding UL bersarang */
    margin-top: 10px;      /* Jarak di atas sub-list */
    margin-bottom: 0;
    width: 100%;
}

.single-item .nested-steps > li {
    margin-bottom: 10px; /* Jarak antar sub-poin */
    line-height: 1.5;
    display: flex;
    flex-direction: column; /* Tumpuk konten LI secara vertikal */
    align-items: flex-start;
    position: relative;
    padding-left: 2em; /* Beri ruang di kiri untuk bullet dan indentasi teks */
}

.single-item .nested-steps > li::before {
    content: "\25E6"; /* Karakter bullet hollow (lingkaran kosong) */
    color: var(--dark-gray);
    position: absolute;
    left: 0.5em; /* Sesuaikan posisi horizontal bullet */
    top: 0.2em; /* Sesuaikan posisi vertikal bullet */
    font-size: 0.8em;
}

        .single-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .single-item h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2em;
            color: var(--primary-blue);
            margin-bottom: 20px;
        }

        .single-item img {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .single-item p { /* Jika ada p tags lain yang bukan list, pastikan stylingnya benar */
            text-align: justify; /* Kembali ke justify jika ini untuk paragraf normal */
            margin-bottom: 1em;
        }

        .single-item ul li {
            font-weight: normal; /* Pastikan teks tidak otomatis bold kecuali diatur secara spesifik */
        }

        /* Styling for Groundstation Page */
         .groundstation-section {
            padding: 80px 5%;
            background-color: var(--medium-gray);
            color: var(--dark-gray);
        }

        .groundstation-section .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .groundstation-item {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }

        /* Tambahkan/modifikasi styling berikut untuk daftar langkah-langkah */
.groundstation-item .groundstation-constellation-steps {
    list-style-type: none; /* Sembunyikan bullet default, kita akan buat kustom */
    padding-left: 0;       /* Atur ulang padding default UL */
    margin: 0;
    text-align: left;
    width: 100%;
}

.groundstation-item .groundstation-constellation-steps > li {
    margin-bottom: 15px;   /* Beri jarak antar poin utama */
    line-height: 1.6;      /* Tingkatkan jarak antar baris teks */
    display: flex;         /* Gunakan flexbox untuk LI */
    flex-direction: column; /* Tumpuk konten LI (teks dan gambar) secara vertikal */
    align-items: flex-start; /* Sejajarkan konten LI ke kiri */
    position: relative;    /* Diperlukan untuk penempatan bullet kustom */
    padding-left: 2em;     /* Beri ruang di kiri untuk bullet dan indentasi teks */
}

/* Menyembunyikan bullet default dan menambahkannya kembali dengan pseudo-element
   untuk kontrol posisi yang lebih baik */
.groundstation-item .groundstation-constellation-steps > li::before {
    content: "\2022"; /* Karakter bullet solid */
    color: var(--dark-gray);
    position: absolute;
    left: 0.5em; /* Sesuaikan posisi horizontal bullet */
    top: 0.2em; /* Sesuaikan posisi vertikal bullet */
    font-size: 1em;
}

/* Styling untuk sub-list (poin bersarang) */
.groundstation-item .nested-steps {
    list-style-type: none; /* Sembunyikan bullet default */
    padding-left: 0;       /* Atur ulang padding UL bersarang */
    margin-top: 10px;      /* Jarak di atas sub-list */
    margin-bottom: 0;
    width: 100%;
}

.groundstation-item .nested-steps > li {
    margin-bottom: 10px; /* Jarak antar sub-poin */
    line-height: 1.5;
    display: flex;
    flex-direction: column; /* Tumpuk konten LI secara vertikal */
    align-items: flex-start;
    position: relative;
    padding-left: 2em; /* Beri ruang di kiri untuk bullet dan indentasi teks */
}

.groundstation-item .nested-steps > li::before {
    content: "\25E6"; /* Karakter bullet hollow (lingkaran kosong) */
    color: var(--dark-gray);
    position: absolute;
    left: 0.5em; /* Sesuaikan posisi horizontal bullet */
    top: 0.2em; /* Sesuaikan posisi vertikal bullet */
    font-size: 0.8em;
}

        .groundstation-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .groundstation-item h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2em;
            color: var(--primary-blue);
            margin-bottom: 20px;
        }

        .groundstation-item img {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .groundstation-item p { /* Jika ada p tags lain yang bukan list, pastikan stylingnya benar */
            text-align: justify; /* Kembali ke justify jika ini untuk paragraf normal */
            margin-bottom: 1em;
        }

        .groundstation-item ul li {
            font-weight: normal; /* Pastikan teks tidak otomatis bold kecuali diatur secara spesifik */
        }

        /* Styling for Constelation 1 Page */
        .multi1-section {
            padding: 80px 5%;
            background-color: var(--medium-gray);
            color: var(--dark-gray);
        }

        .multi1-section .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .multi1-item {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }

        /* Tambahkan/modifikasi styling berikut untuk daftar langkah-langkah */
.multi1-item .multi1-constellation-steps {
    list-style-type: none; /* Sembunyikan bullet default, kita akan buat kustom */
    padding-left: 0;       /* Atur ulang padding default UL */
    margin: 0;
    text-align: left;
    width: 100%;
}

.multi1-item .multi1-constellation-steps > li {
    margin-bottom: 15px;   /* Beri jarak antar poin utama */
    line-height: 1.6;      /* Tingkatkan jarak antar baris teks */
    display: flex;         /* Gunakan flexbox untuk LI */
    flex-direction: column; /* Tumpuk konten LI (teks dan gambar) secara vertikal */
    align-items: flex-start; /* Sejajarkan konten LI ke kiri */
    position: relative;    /* Diperlukan untuk penempatan bullet kustom */
    padding-left: 2em;     /* Beri ruang di kiri untuk bullet dan indentasi teks */
}

/* Menyembunyikan bullet default dan menambahkannya kembali dengan pseudo-element
   untuk kontrol posisi yang lebih baik */
.multi1-item .multi1-constellation-steps > li::before {
    content: "\2022"; /* Karakter bullet solid */
    color: var(--dark-gray);
    position: absolute;
    left: 0.5em; /* Sesuaikan posisi horizontal bullet */
    top: 0.2em; /* Sesuaikan posisi vertikal bullet */
    font-size: 1em;
}

/* Styling untuk sub-list (poin bersarang) */
.multi1-item .nested-steps {
    list-style-type: none; /* Sembunyikan bullet default */
    padding-left: 0;       /* Atur ulang padding UL bersarang */
    margin-top: 10px;      /* Jarak di atas sub-list */
    margin-bottom: 0;
    width: 100%;
}

.multi1-item .nested-steps > li {
    margin-bottom: 10px; /* Jarak antar sub-poin */
    line-height: 1.5;
    display: flex;
    flex-direction: column; /* Tumpuk konten LI secara vertikal */
    align-items: flex-start;
    position: relative;
    padding-left: 2em; /* Beri ruang di kiri untuk bullet dan indentasi teks */
}

.multi1-item .nested-steps > li::before {
    content: "\25E6"; /* Karakter bullet hollow (lingkaran kosong) */
    color: var(--dark-gray);
    position: absolute;
    left: 0.5em; /* Sesuaikan posisi horizontal bullet */
    top: 0.2em; /* Sesuaikan posisi vertikal bullet */
    font-size: 0.8em;
}

        .multi1-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .multi1-item h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2em;
            color: var(--primary-blue);
            margin-bottom: 20px;
        }

        .multi1-item img {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .multi1-item p { /* Jika ada p tags lain yang bukan list, pastikan stylingnya benar */
            text-align: justify; /* Kembali ke justify jika ini untuk paragraf normal */
            margin-bottom: 1em;
        }

        .multi1-item ul li {
            font-weight: normal; /* Pastikan teks tidak otomatis bold kecuali diatur secara spesifik */
        }

        /* Styling for Constelation 1 Page */
        .multi2-section {
            padding: 80px 5%;
            background-color: var(--medium-gray);
            color: var(--dark-gray);
        }

        .multi2-section .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .multi2-item {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }

        /* Tambahkan/modifikasi styling berikut untuk daftar langkah-langkah */
.multi2-item .multi2-constellation-steps {
    list-style-type: none; /* Sembunyikan bullet default, kita akan buat kustom */
    padding-left: 0;       /* Atur ulang padding default UL */
    margin: 0;
    text-align: left;
    width: 100%;
}

.multi2-item .multi2-constellation-steps > li {
    margin-bottom: 15px;   /* Beri jarak antar poin utama */
    line-height: 1.6;      /* Tingkatkan jarak antar baris teks */
    display: flex;         /* Gunakan flexbox untuk LI */
    flex-direction: column; /* Tumpuk konten LI (teks dan gambar) secara vertikal */
    align-items: flex-start; /* Sejajarkan konten LI ke kiri */
    position: relative;    /* Diperlukan untuk penempatan bullet kustom */
    padding-left: 2em;     /* Beri ruang di kiri untuk bullet dan indentasi teks */
}

/* Menyembunyikan bullet default dan menambahkannya kembali dengan pseudo-element
   untuk kontrol posisi yang lebih baik */
.multi2-item .multi2-constellation-steps > li::before {
    content: "\2022"; /* Karakter bullet solid */
    color: var(--dark-gray);
    position: absolute;
    left: 0.5em; /* Sesuaikan posisi horizontal bullet */
    top: 0.2em; /* Sesuaikan posisi vertikal bullet */
    font-size: 1em;
}

/* Styling untuk sub-list (poin bersarang) */
.multi2-item .nested-steps {
    list-style-type: none; /* Sembunyikan bullet default */
    padding-left: 0;       /* Atur ulang padding UL bersarang */
    margin-top: 10px;      /* Jarak di atas sub-list */
    margin-bottom: 0;
    width: 100%;
}

.multi2-item .nested-steps > li {
    margin-bottom: 10px; /* Jarak antar sub-poin */
    line-height: 1.5;
    display: flex;
    flex-direction: column; /* Tumpuk konten LI secara vertikal */
    align-items: flex-start;
    position: relative;
    padding-left: 2em; /* Beri ruang di kiri untuk bullet dan indentasi teks */
}

.multi2-item .nested-steps > li::before {
    content: "\25E6"; /* Karakter bullet hollow (lingkaran kosong) */
    color: var(--dark-gray);
    position: absolute;
    left: 0.5em; /* Sesuaikan posisi horizontal bullet */
    top: 0.2em; /* Sesuaikan posisi vertikal bullet */
    font-size: 0.8em;
}

        .multi2-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .multi2-item h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2em;
            color: var(--primary-blue);
            margin-bottom: 20px;
        }

        .multi2-item img {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .multi2-item p { /* Jika ada p tags lain yang bukan list, pastikan stylingnya benar */
            text-align: justify; /* Kembali ke justify jika ini untuk paragraf normal */
            margin-bottom: 1em;
        }

        .multi2-item ul li {
            font-weight: normal; /* Pastikan teks tidak otomatis bold kecuali diatur secara spesifik */
        }
        
        /* Footer Styling - REPLICATED EXACTLY FROM HOMEPAGE */
        footer {
            background: var(--dark-bg);
            color: var(--light-text);
            padding: 60px 5% 25px;
        }

        footer .main-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 40px;
            margin-bottom: 40px;
        }

        footer .main-content div {
            flex: 1;
            min-width: 280px;
        }

        footer h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.8em;
            margin-bottom: 25px;
            color: var(--primary-blue);
            font-weight: 700;
        }

        footer .content p,
        footer .content .text {
            color: #ccc;
            line-height: 1.7;
            margin-bottom: 15px;
            font-size: 0.95em;
        }

        footer .social a {
            display: inline-block;
            margin-right: 18px;
            font-size: 2em;
            color: var(--light-text);
            transition: color var(--transition-speed), transform var(--transition-speed);
        }

        footer .social a:hover {
            color: var(--primary-blue);
            transform: translateY(-3px);
        }

        footer .place,
        footer .phone,
        footer .email {
            display: flex;
            align-items: flex-start; /* Ensure icons and text align at the top */
            margin-bottom: 20px;
        }

        footer .place span,
        footer .phone span,
        footer .email span {
            font-size: 1.3em;
            margin-right: 15px;
            color: var(--primary-blue);
            line-height: 1.5; /* Align icon better with text */
        }

        footer form .text {
            margin-bottom: 10px;
            display: block;
            color: #ccc;
            font-size: 0.9em;
        }

        footer form input[type="email"],
        footer form textarea {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: var(--border-radius);
            margin-bottom: 18px;
            background-color: #333;
            color: var(--light-text);
            box-sizing: border-box;
            font-size: 1em;
            resize: vertical;
            transition: border-color var(--transition-speed), box-shadow var(--transition-speed);
            border: 1px solid #555;
        }

        footer form input[type="email"]:focus,
        footer form textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.3);
        }

        footer form button {
            background-color: var(--primary-blue);
            color: var(--light-text);
            padding: 12px 25px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color var(--transition-speed), transform var(--transition-speed);
            font-weight: 600;
        }

        footer form button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        footer .bottom {
            margin-top: 40px;
            padding-top: 25px;
            border-top: 1px solid #333;
            text-align: center;
            font-size: 0.9em;
            color: #ccc;
            display: flex;
            flex-direction: column; /* Ensure stacked on smaller screens, can be row on larger */
            justify-content: center;
            align-items: center;
            gap: 15px;
        }

        footer .bottom .credit {
            display: flex;
            align-items: center;
            color: #ccc;
            flex-wrap: wrap; /* Allows text to wrap if too long */
            justify-content: center;
            gap: 10px;
        }

        footer .bottom .credit a {
            color: var(--primary-blue);
            text-decoration: none;
            transition: color var(--transition-speed);
        }

        footer .bottom .credit a:hover {
            color: #0056b3;
        }

        footer .bottom .credit img {
            height: 35px;
            margin-right: 0; /* Remove margin right for better centering */
            cursor: pointer;
            transition: transform var(--transition-speed);
        }

        footer .bottom .credit img:hover {
            transform: rotate(5deg);
        }


        /* Keyframe Animations (copied) */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }


        /* Responsive adjustments for parameter section */
        @media (max-width: 768px) {
            .navigation {
                flex-direction: column;
                align-items: center;
            }
            .nav-links {
                margin-top: 15px;
                flex-direction: column;
                width: 100%;
                text-align: center;
                gap: 10px;
            }
            .nav-links li {
                margin-left: 0;
            }
            .common-title-section {
                padding: 60px 5%;
            }
            .common-title-section .title h1 {
                font-size: 2.2em;
            }
            .parameter-section {
                padding: 40px 5%;
            }
            .parameter-item {
                padding: 20px;
                margin-bottom: 30px;
            }
            .parameter-item h2 {
                font-size: 1.8em;
            }
            .parameter-item p {
                font-size: 0.95em;
            }
            /* Footer responsive adjustments - ENSURING ALIGNMENT */
            footer .main-content {
                flex-direction: column;
                align-items: center; /* Center align blocks */
                text-align: center; /* Center text within blocks */
            }
            footer .main-content div {
                min-width: unset;
                width: 100%; /* Full width for stacked columns */
            }
            footer .place,
            footer .phone,
            footer .email {
                justify-content: center; /* Center content of each contact line */
                align-items: center; /* Vertically align items in contact line */
            }
            footer .bottom {
                flex-direction: column;
                gap: 10px;
            }
            footer .bottom .credit {
                flex-direction: column;
                gap: 5px;
            }
            footer .bottom .credit img {
                margin-right: 0;
            }
        }

        @media (max-width: 480px) {
            .common-title-section .title h1 { font-size: 1.8em; }
            .parameter-item h2 { font-size: 1.5em; }
            .parameter-item p { font-size: 0.9em; }
        }

        @media (max-width: 768px) {
    .single-item .single-constellation-steps {
        padding-left: 15px; /* Sesuaikan indentasi pada layar kecil */
    }
    .single-item .single-constellation-steps li {
        padding-left: 1.2em;
    }
    .single-item .nested-steps {
        padding-left: 15px;
    }
    .single-item .nested-steps li {
        padding-left: 1.2em;
    }
}
@media (max-width: 768px) {
    .step-images img {
        max-width: 100%; /* Gunakan lebar penuh pada layar kecil */
    }
}
    </style>
</head>


<body>
    <nav class="navigation">
        <h1 class="logo">
            <img src="<?php echo asset('images/Logo_TA.png'); ?>" alt="LOS Logo" onclick="scrollToTop()" style="cursor: pointer;">
        </h1>
        <ul class="nav-links">
            <li><a href="<?php echo url('/#home'); ?>">Home</a></li>
            <li><a href="<?php echo url('/#about'); ?>">About</a></li>
            <li><a href="<?php echo url('3d-satellite'); ?>">Project</a></li>
        </ul>
    </nav>

    <section class="common-title-section" data-aos="fade-up">
        <div class="title">
            <h1>SATELLITE ORBIT PARAMETERS</h1>
        </div>
    </section>

    <section class="parameter-section">
        <div class="container">
            <div class="parameter-item" data-aos="fade-up" data-aos-delay="100">
                <h2>Semi-Major Axis (Km)</h2>
                <img src="<?php echo asset('images/Semi-Major Axis (Km).png'); ?>" alt="Altitude Parameter">
                <p>
                    The semi-major axis (a) is the key parameter that defines the size of a satellite orbit. For circular orbits (where eccentricity e=0), the semi-major axis is equal to the orbital radius. This formula calculates it by adding the Earth's radius (R_e) and the satellite's altitude (h) above the Earth's surface.
                </p>
            </div>

            <div class="parameter-item" data-aos="fade-up" data-aos-delay="200">
                <h2>Satellite Coverage Area</h2>
                <img src="<?php echo asset('images/Satellite Coverage Area.png'); ?>" alt="Inclination Parameter">
                <p>
                    Area cakupan satelit, sering juga disebut sebagai footprint, adalah wilayah di permukaan Bumi di mana sinyal dari sebuah satelit dapat diterima secara efektif oleh stasiun bumi (antena penerima).   
                </p>
            </div>

            <div class="parameter-item" data-aos="fade-up" data-aos-delay="200">
                <h2>Orbital Period (In seconds)</h2>
                <img src="<?php echo asset('images/Orbital Period (In seconds).png'); ?>" alt="Inclination Parameter">
                <p>
                    The orbital period (T) is the time it takes for a satellite to complete one full revolution around the Earth. This formula comes from Kepler's Third Law. The orbital period is directly proportional to the square root of the orbital radius (r). This means that the higher the satellite's orbit, the longer it takes to orbit the Earth. 
                </p>
            </div>

            <div class="parameter-item" data-aos="fade-up" data-aos-delay="200">
                <h2>Orbital Velocity (m/s)</h2>
                <img src="<?php echo asset('images/Orbital Velocity (ms).png'); ?>" alt="Inclination Parameter">
                <p>
                    The speed that a satellite must maintain in order to remain in a stable orbit. This speed is inversely proportional to the square root of the orbital radius (r). So, satellites in lower orbits (like LEO) move faster than satellites in higher orbits (like GEO). 
                </p>
            </div>

            <div class="parameter-item" data-aos="fade-up" data-aos-delay="300">
                <h2>Right Ascension of Ascending Node (RAAN) Drift (rad/s)</h2>
                <img src="<?php echo asset('images/Right Ascension of Ascending Node (RAAN) Drift (rads).png'); ?>" alt="RAAN Parameter">
                <p>
                    This formula calculates the rate of change of the RAAN, known as “RAAN drift”. This effect is very important for orbit design, especially for Sun-synchronous orbits that utilize this drift.
                </p>
            </div>

            <div class="parameter-item" data-aos="fade-up" data-aos-delay="400">
                <h2>Argument of Perigee Drift</h2>
                <img src="<?php echo asset('images/Argument of Perigee Drift.png'); ?>" alt="True Anomaly Parameter">
                <p>
                    In addition to rotating the orbital plane, the flakiness of the Earth also causes an elliptical rotation of the orbit within its plane. This phenomenon is called perigee argument shift (ω). This formula calculates the rate at which the orientation of the orbit changes. This shift can be positive (forward rotation) or negative (backward rotation) depending on the inclination of the orbit (i).
                </p>
            </div>

            <div class="parameter-item" data-aos="fade-up" data-aos-delay="500">
                <h2>Central Angle</h2>
                <img src="<?php echo asset('images/Central Angle.png'); ?>" alt="Beamwidth Parameter">
                <p>
                    This formula is based on the Law of Sines applied to the triangle formed by the center of the Earth, the satellite, and the edge of the coverage area. This angle is important for determining how much area on the Earth's surface the satellite can “see”.
                </p>
            </div>

            <div class="parameter-item" data-aos="fade-up" data-aos-delay="400">
                <h2>Ground Coverage Radius (R_f)</h2>
                <img src="<?php echo asset('images/Ground Coverage Radius (R_f).png'); ?>" alt="True Anomaly Parameter">
                <p>
                    This formula calculates the distance along the Earth's surface from a point directly below the satellite (sub-satellite) to the edge of its coverage area. This is essentially the arc length on the surface of the Earth.
                </p>
            </div>

            <div class="parameter-item" data-aos="fade-up" data-aos-delay="400">
                <h2>Coverage Area</h2>
                <img src="<?php echo asset('images/Coverage Area.png'); ?>" alt="True Anomaly Parameter">
                <p>
                    This formula is a simple calculation for the coverage area of a satellite on the ground, assuming it to be a flat circle with radius R_f.
                </p>
            </div>
        </div>
    </section>

    <section class="common-title-section" data-aos="fade-up">
        <div class="title">
            <h1>Contoh simulasi Single</h1>
        </div>
    </section>

    <section class="single-section">
    <div class="container">
        <div class="single-item" data-aos="fade-up" data-aos-delay="100">
            <h2>Langkah-langkah dalam membuat single simulasi</h2>
            <ul class="single-constellation-steps">
                <li>Klik menu New, lalu pilih single
                    <div class="step-images">
                        <img src="<?php echo asset('images/new2.png'); ?>" alt="Name2 Parameter">
                    </div><div class="step-images">
                        <img src="<?php echo asset('images/singl2.png'); ?>" alt="Name2 Parameter">
                    </div>
                </li>

                <li>
                    Masukan nama satelite bebas
                    <div class="step-images">
                        <img src="<?php echo asset('images/Name2.png'); ?>" alt="Name2 Parameter">
                    </div>
                </li>
                <li>
                    Masukan input altitude (dari 100km sampai 36000km)
                    <div class="step-images">
                        <img src="<?php echo asset('images/alt2.png'); ?>" alt="alt2 Parameter">
                    </div>
                </li>
                <li>
                    Masukan input inclination (dari 0° sampai 180°)
                    <div class="step-images">
                        <img src="<?php echo asset('images/inc2.png'); ?>" alt="inc2 Parameter">
                    </div>
                </li>
                <li>
                    Pilih eccentricity antara circular ataupun elliptical
                    <div class="step-images">
                        <img src="<?php echo asset('images/ecce.png'); ?>" alt="ecce Parameter">
                    </div>
                </li>
                <li>
                    Masukan input RAAN (dari 0° sampai 360°)
                    <div class="step-images">
                        <img src="<?php echo asset('images/raa2.png'); ?>" alt="RAAN Parameter">
                    </div>
                </li>
                <li>
                    Masukan input True Anomaly (dari 0° sampai 360°)
                    <div class="step-images">
                        <img src="<?php echo asset('images/tra2.png'); ?>" alt="True Anomaly Parameter">
                    </div>
                </li>
                <li>
                    Pilih waktu seperti tanggal, tahun, bulan, dan jam untuk memulai satelit berjalan
                    <div class="step-images">
                        <img src="<?php echo asset('images/date1.png'); ?>" alt="Epoch Parameter">
                        <img src="<?php echo asset('images/date2.png'); ?>" alt="Epoch Parameter">
                    </div>
                    <ul class="nested-steps">
                        <li>
                            Pilih UTC atau zona waktu yang diinginkan
                            <div class="step-images">
                                <img src="<?php echo asset('images/utc1.png'); ?>" alt="UTC Parameter">
                                <img src="<?php echo asset('images/utc2.png'); ?>" alt="UTC Parameter">
                            </div>
                        </li>
                        <li>
                            Masukan input beamwidth (dari 0° sampai 90°)
                            <div class="step-images">
                                <img src="<?php echo asset('images/bem2.png'); ?>" alt="Beamwidth Parameter">
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</section>

<section class="groundstation-section">
    <div class="container">
        <div class="groundstation-item" data-aos="fade-up" data-aos-delay="100">
            <h2>Membuat Ground Station untuk satelit dapat memancarkan sinyal</h2>
            <ul class="groundstation-constellation-steps">
                <li>Klik menu New, lalu pilih Groundstation
                    <div class="step-images">
                        <img src="<?php echo asset('images/grnd.png'); ?>" alt="grnd Parameter">
                    </div>
                </li>
                <li>
                    Masukan nama Groundstation bebas
                    <div class="step-images">
                        <img src="<?php echo asset('images/grnm.png'); ?>" alt="grnm Parameter">
                    </div>
                </li>
                <li>
                    Masukan input Latitude
                    <div class="step-images">
                        <img src="<?php echo asset('images/latt.png'); ?>" alt="latt Parameter">
                    </div>
                </li>
                <li>
                    Masukan input Logitude
                    <div class="step-images">
                        <img src="<?php echo asset('images/long.png'); ?>" alt="long Parameter">
                    </div>
                </li>
                <li>
                    Masukan input Minimum Elevation Angle
                    <div class="step-images">
                        <img src="<?php echo asset('images/minel.png'); ?>" alt="minel Parameter">
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>

<section class="common-title-section" data-aos="fade-up">
        <div class="title">
            <h1>Contoh simulasi Constelation</h1>
        </div>
    </section>

    <section class="multi1-section">
    <div class="container">
        <div class="multi1-item" data-aos="fade-up" data-aos-delay="100">
            <h2>Langkah-langkah dalam membuat constellation (Type : Train) simulasi</h2>
            <ul class="multi1-constellation-steps">
                <li>Klik menu New, lalu pilih constellation
                    <div class="step-images">
                        <img src="<?php echo asset('images/constnew.png'); ?>" alt="constnew Parameter">
                    </div>
                </li>

                <li>
                    Masukan nama constellation satellite bebas
                    <div class="step-images">
                        <img src="<?php echo asset('images/consnm.png'); ?>" alt="consnm Parameter">
                    </div>
                </li>
                <li>
                    Masukan input altitude (dari 100km sampai 36000km)
                    <div class="step-images">
                        <img src="<?php echo asset('images/consalt.png'); ?>" alt="consalt Parameter">
                    </div>
                </li>
                <li>
                    Masukan input inclination (dari 0° sampai 180°)
                    <div class="step-images">
                        <img src="<?php echo asset('images/consinc.png'); ?>" alt="consinc Parameter">
                    </div>
                </li>
                <li>
                    Pilih eccentricity antara circular ataupun elliptical
                    <div class="step-images">
                        <img src="<?php echo asset('images/ecce.png'); ?>" alt="ecce Parameter">
                    </div>
                    <div class="step-images">
                        <img src="<?php echo asset('images/ecce2.png'); ?>" alt="ecce2 Parameter">
                    </div>
                </li>
                <li>
                    Masukan input RAAN (dari 0° sampai 360°)
                    <div class="step-images">
                        <img src="<?php echo asset('images/consrn.png'); ?>" alt="consrn Parameter">
                    </div>
                </li>
                <li>
                    Masukan input True Anomaly (dari 0° sampai 360°)
                    <div class="step-images">
                        <img src="<?php echo asset('images/consta.png'); ?>" alt="consta Parameter">
                    </div>
                </li>
                <li>
                    Pilih waktu seperti tanggal, tahun, bulan, dan jam untuk memulai satelit berjalan
                    <div class="step-images">
                        <img src="<?php echo asset('images/date1.png'); ?>" alt="Epoch Parameter">
                        <img src="<?php echo asset('images/date2.png'); ?>" alt="Epoch Parameter">
                    </div>
                    <ul class="nested-steps">
                        <li>
                            Pilih UTC atau zona waktu yang diinginkan
                            <div class="step-images">
                                <img src="<?php echo asset('images/constutc.png'); ?>" alt="constutc Parameter">
                            </div>
                        </li>
                        <li>
                            Masukan input beamwidth (dari 0° sampai 90°)
                            <div class="step-images">
                                <img src="<?php echo asset('images/constbmw.png'); ?>" alt="constbmw Parameter">
                            </div>
                        </li>
                        <li>
                            Pilih Constellation Type Train
                            <div class="step-images">
                                <img src="<?php echo asset('images/consttype.png'); ?>" alt="consttype Parameter">
                            </div>
                        </li>
                        <li>
                            Masukan jumlah dari satellite
                            <div class="step-images">
                                <img src="<?php echo asset('images/constsat.png'); ?>" alt="constsat Parameter">
                            </div>
                        </li>
                        <li>
                            Pilih separation type dan valuenya
                            <div class="step-images">
                                <img src="<?php echo asset('images/constsep.png'); ?>" alt="constsep Parameter">
                            </div>
                        </li>
                        <li>
                            Pilih direction satellite
                            <div class="step-images">
                                <img src="<?php echo asset('images/constdir.png'); ?>" alt="constdir Parameter">
                            </div>
                        </li>
                        <li>
                            Pilih start location (jika memilih offset from seed, terdapat parameter tambahan lagi)
                            <div class="step-images">
                                <img src="<?php echo asset('images/constloc.png'); ?>" alt="constloc Parameter">
                            </div>
                            <div class="step-images">
                                <img src="<?php echo asset('images/constloc2.png'); ?>" alt="constloc2 Parameter">
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</section>

<section class="multi2-section">
    <div class="container">
        <div class="multi2-item" data-aos="fade-up" data-aos-delay="100">
            <h2>Langkah-langkah dalam membuat constellation (Type : Walker) simulasi</h2>
            <ul class="multi2-constellation-steps">
                        <li>
                            Pilih Constellation Type Walker
                            <div class="step-images">
                                <img src="<?php echo asset('images/consttype2.png'); ?>" alt="consttype2 Parameter">
                            </div>
                        </li>
                        <li>
                            Masukan jumlah dari planes
                            <div class="step-images">
                                <img src="<?php echo asset('images/nop.png'); ?>" alt="nop Parameter">
                            </div>
                        </li>
                        <li>
                            Masukan jumlah dari satellite per plane
                            <div class="step-images">
                                <img src="<?php echo asset('images/spp.png'); ?>" alt="spp Parameter">
                            </div>
                        </li>
                        <li>
                            Masukan jumlah RAAN spread
                            <div class="step-images">
                                <img src="<?php echo asset('images/rs.png'); ?>" alt="rs Parameter">
                            </div>
                        </li>
                        <li>
                            Masukan jumlah phasing factor
                            <div class="step-images">
                                <img src="<?php echo asset('images/pf.png'); ?>" alt="pf Parameter">
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</section>

            <div class="center box" data-aos="fade-up">
                <h2>Address</h2>
                <div class="content">
                    <div class="place">
                        <span class="fas fa-map-marker-alt"></span>
                        <span class="text">Jl. Sukabirus No.A54, Kec. Dayeuhkolot, Kabupaten Bandung, Jawa Barat
                            40257</span>
                    </div>
                    <div class="phone">
                        <span class="fas fa-phone-alt"></span>
                        <span class="text">+6281284573675</span>
                    </div>
                    <div class="email">
                        <span class="fas fa-envelope"></span>
                        <span class="text">ethanhaikal@gmail.com</span>
                    </div>
                </div>
            </div>
            <div class="right box" data-aos="fade-left">
                <h2>Contact Us</h2>
                <div class="content">
                    <form action="#">
                        <div class="email">
                            <div class="text">Email *</div>
                            <input type="email" required>
                        </div>
                        <div class="msg">
                            <div class="text">Message *</div>
                            <textarea rows="2" cols="25" required></textarea>
                        </div>
                        <div class="btn">
                            <button type="submit">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="bottom">
            <span class="credit">
                <img src="<?php echo asset('images/Logo_TA.png'); ?>" alt="LOS Team Logo" onclick="scrollToTop()" style="cursor: pointer;">
                Created By LOS TEAM | © 2025 All rights reserved
            </span>
        </div>
        
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // AOS Initialization
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100,
            delay: 50,
        });

        // Scroll to top function for logo click
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>

</body>

</html>