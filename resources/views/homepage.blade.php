<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage LSOS</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Rubik', sans-serif;
            background-color:rgb(255, 255, 255); /* Warna abu-abu terang */
            color: #333; /* Ubah warna teks default agar terbaca di background terang */
            margin: 0;
            padding: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }

        /* Navbar Styling */
        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 2%;
            background-color: transparent;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .navigation.sticky {
            background-color: #000; /* Warna solid HITAM saat di-scroll */
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .logo img {
            height: 50px;
            margin-right: 15px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .nav-links li {
            margin-left: 30px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 1.1em;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #007bff;
        }

        /* Header Styling */
        #home {
            /* The background-image is set directly in the HTML for PHP asset() usage */
            background-size: auto; /* Changed from 'cover' to 'auto' */
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding-top: 60px;
        }

        .header-content h1 {
            font-size: 3.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        .header-content h3 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #eee;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }

        .button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3;
        }

        
        /* Common Title Styling (used for About Us and About Website) */
        .common-title-section {
            padding: 80px 5% 30px; /* Reduced bottom padding for title sections */
            text-align: center;
            background-color: #f8f9fa; /* White background */
        }

        .common-title-section .title {
            margin-bottom: 50px; /* Kept for consistency if content follows */
        }

        .common-title-section .title h1 {
            font-size: 2.5em;
            color: #333;
            position: relative;
            display: inline-block;
        }

        .common-title-section .title h1::after {
            content: '';
            position: absolute;
            width: 70%;
            height: 3px;
            background-color: #007bff;
            bottom: -10px;
            left: 15%;
        }

        /* About Section (Content below About Us title) */
        .about-content {
            padding-bottom: 80px; /* Only for the content part */
            background-color: #f8f9fa; /* White background */
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .main-card {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .card .img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 15px;
            border: 5px solid #007bff;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card .img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card .details .name {
            font-size: 1.4em;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .card .details .jobdesk {
            font-size: 1em;
            color: #666;
            margin-bottom: 15px;
        }

        .card .media-icons a {
            display: inline-block;
            margin: 0 8px;
            font-size: 1.5em;
            color: #007bff;
            transition: color 0.3s ease;
        }

        .card .media-icons a:hover {
            color: #0056b3;
        }

        /* Website Section (example) */
        .website {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 200px 5%; /* Menambah padding atas */
            padding-bottom: 200px; /* Menambah padding bawah */
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        /* Overlay for the background image */
        .website::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6); /* Dark overlay */
            z-index: 1;
        }

        .website .content-wrapper {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .website .content-wrapper h4 {
            font-size: 1.8em;
            color: white;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }

        .website .content-wrapper p {
            font-size: 1.1em;
            line-height: 1.7;
            color: #ccc;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
        }

        /* Footer Styling */
        footer {
            background: #000;
            color: white;
            padding: 50px 5% 20px;
        }

        footer .main-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 30px;
        }

        footer .main-content div {
            flex: 1;
            min-width: 280px;
        }

        footer h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #007bff;
        }

        footer .content p,
        footer .content .text {
            color: #ccc;
            line-height: 1.7;
            margin-bottom: 15px;
        }

        footer .social a {
            display: inline-block;
            margin-right: 15px;
            font-size: 1.8em;
            color: white;
            transition: color 0.3s ease;
        }

        footer .social a:hover {
            color: #007bff;
        }

        footer .place, footer .phone, footer .email {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        footer .place span, footer .phone span, footer .email span {
            font-size: 1.2em;
            margin-right: 10px;
            color: #007bff;
        }

        footer form .text {
            margin-bottom: 5px;
            display: block;
            color: #ccc;
        }

        footer form input[type="email"],
        footer form textarea {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #555;
            color: white;
            box-sizing: border-box;
        }

        footer form button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        footer form button:hover {
            background-color: #0056b3;
        }

        footer .bottom {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #555;
            text-align: center;
            font-size: 0.9em;
            color: #ccc;
        }

        footer .bottom .credit a {
            color: #007bff;
            text-decoration: none;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navigation {
                flex-direction: column;
                padding: 10px 5%;
                align-items: flex-start;
            }
            .nav-links {
                margin-top: 10px;
                flex-direction: column;
                width: 100%;
                text-align: center;
            }
            .nav-links li {
                margin: 5px 0;
            }
            .header-content h1 {
                font-size: 2.5em;
            }
            .header-content h3 {
                font-size: 1.2em;
            }
            .common-title-section, .about-content, .website {
                padding: 50px 5%; /* Responsive padding for smaller screens */
            }
            .main-card, .cards {
                flex-direction: column;
                align-items: center;
            }
            .card {
                width: 90%;
            }
            footer .main-content {
                flex-direction: column;
                align-items: center;
            }
            footer .main-content div {
                min-width: unset;
                width: 100%;
            }
            .website .row2 {
                flex-direction: column;
                align-items: center;
            }
            .website .col2 {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <nav class="navigation">
        <h1 class="logo">
            <img src="<?php echo asset('images/Logo_TA.png'); ?>" alt="Logo TA">
        </h1>
        <ul class="nav-links">
            <li><a href="<?php echo url('/#home'); ?>">Home</a></li>
            <li><a href="<?php echo url('/#about'); ?>">About</a></li>
            <li><a href="<?php echo url('/app-main'); ?>">Project</a></li>
        </ul>
    </nav>
    <header id="home" style="
        background-image: url('<?php echo asset('images/Earth with satellite.jpg'); ?>');
        background-size: auto; /* Changed from 'cover' to 'auto' */
        background-position: center;
        background-repeat: no-repeat;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: white;
        padding-top: 60px;
    ">
        <div class="header-content">
            <h3>LEO Satellite Website</h3>
            <h1>Visual Simulation of LEO Orbit Satellite</h1>
            <a href="<?php echo url('/simulation'); ?>" class="button">LAUNCH</a>
        </div>
    </header>

    <section class="common-title-section" id="about" data-aos="fade-up">
        <div class="title">
            <h1>ABOUT US</h1>
        </div>
    </section>

    <section class="about-content" data-aos="fade-up">
        <div class="container">
            <div class="main-card">
                <div class="cards">
                    <div class="card">
                        <div class="content">
                            <div class="img">
                                <img src="<?php echo asset('images/Kevinn njir.jpg'); ?>" alt="Robby Kevin Putra S.">
                            </div>
                            <div class="details">
                                <div class="name">Robby Kevin Putra Sigit</div>
                                <div class="jobdesk">UI/UX Software</div>
                            </div>
                            <div class="media-icons">
                                <a href="https://mail.google.com/mail/u/0/?view=cm&fs=1&to=robbykevin80@gmail.com"><i class="fas fa-envelope"></i></a>
                                <a href="https://x.com/cvdvirus19"><i class="fab fa-twitter"></i></a>
                                <a href="https://instagram.com/kevinrobby__"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="content">
                            <div class="img">
                                <img src="<?php echo asset('images/Foto Viandra.jpg'); ?>" alt="I Dewa Made Raviandra W.">
                            </div>
                            <div class="details">
                                <div class="name">I Dewa Made Raviandra W.</div>
                                <div class="jobdesk">Simulation</div>
                            </div>
                            <div class="media-icons">
                                <a href="https://mail.google.com/mail/u/0/?view=cm&fs=1&to=raviandrawedagama01@gmail.com"><i class="fas fa-envelope"></i></a>
                                <a href="https://x.com/i_wedagama"><i class="fab fa-twitter"></i></a>
                                <a href="https://instagram.com/raviandra_wedagama"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="content">
                            <div class="img">
                                <img src="<?php echo asset('images/naahh.jpg'); ?>" alt="M. Haikal Puteh">
                            </div>
                            <div class="details">
                                <div class="name">Muhammad Haikal Puteh</div>
                                <div class="jobdesk">UI/UX Homepage</div>
                            </div>
                            <div class="media-icons">
                                <a href="https://mail.google.com/mail/u/0/?view=cm&fs=1&to=ethanhaikal@gmail.com"><i class="fas fa-envelope"></i></a>
                                <a href="https://x.com/62hityourmind"><i class="fab fa-twitter"></i></a>
                                <a href="https://instagram.com/622kal"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="common-title-section" data-aos="fade-up">
            <div class="title">
                <h1>ABOUT WEBSITE</h1>
            </div>
            <div style="background-color: #007bff; text-align: center; padding: 25px 5%; margin-bottom: 50px; border-radius: 15px;">
                <img src="images/website.png" alt="Website Design" style="max-width: 80%; height: auto; display: block; margin: 0 auto 15px;">
                <p style="color: white; font-size: 1.1em; line-height: 1.6; max-width: 80%; margin: 0 auto;">
                    Platform ini dirancang sebagai hub sentral untuk komunikasi dan kolaborasi yang dinamis dan efisien. Fokus utamanya adalah memfasilitasi interaksi real-time, memungkinkan tim dan individu untuk terhubung tanpa batas melintasi berbagai lokasi. Antarmuka yang intuitif menampilkan daftar kontak atau partisipan, lengkap dengan indikator status visual yang jelas, seperti 'online', 'sibuk', atau 'sedang berbagi layar'. Ini sangat penting untuk meningkatkan koordinasi tim dan memungkinkan pengguna untuk mengidentifikasi ketersediaan rekan kerja secara instan sebelum memulai komunikasi.
                </p>
                <p style="color: white; font-size: 1.1em; line-height: 1.6; max-width: 80%; margin: 0 auto;">
                    Fitur berbagi layar adalah inti dari pengalaman kolaborasi ini, memungkinkan pengguna untuk menampilkan presentasi, dokumen, atau bahkan aplikasi secara langsung kepada partisipan lain, menjadikan rapat virtual seefektif pertemuan tatap muka. Kemampuan untuk melihat siapa yang berbicara atau sedang aktif melalui indikator visual membantu menjaga alur diskusi tetap teratur, terutama dalam grup besar. Dengan penekanan pada fungsionalitas yang mulus dan penyampaian informasi yang efisien, platform ini bertujuan untuk merevolusi cara kerja tim berinteraksi, berdiskusi, dan mencapai tujuan bersama dalam lingkungan digital yang semakin terhubung.
                </p>
            </div>
        </section>

        <section class="website" id="project" data-aos="fade-up" style="background-image: url('<?php echo asset('images/satellitepanorama.jpg'); ?>');">
            <div class="content-wrapper">
                <h4>WEBSITE LOS</h4>
                <p>LEO Orbit Satellite (LOS) Website adalah platform simulasi interaktif yang dirancang untuk memvisualisasikan lintasan orbit satelit LEO (Low Earth Orbit) di sekitar Bumi. Tujuan utama kami adalah mengedukasi publik tentang cara kerja satelit ini. Pengguna dapat dengan mudah mengatur berbagai parameter orbit, seperti ketinggian satelit, untuk mengamati secara langsung bagaimana perubahan tersebut memengaruhi pergerakan satelit dan pancaran (coverage) yang dihasilkannya. </p>
            </div>
        </section>

        <footer>
            <div class="main-content">
                <div class="left box">
                    <h2>About us</h2>
                    <div class="content">
                        <p>Halo semuanya!!!!!
                            Kami adalah sekelompok individu yang bersemangat tentang antariksa dan teknologi. Melalui LEO Satellite Website, kami berupaya menjembatani kesenjangan antara konsep teoretis dan pemahaman visual tentang satelit Low Earth Orbit (LEO). Proyek ini dibangun dengan tujuan untuk menjadi alat edukasi yang intuitif, memungkinkan pengguna untuk tidak hanya melihat, tetapi juga berinteraksi dengan simulasi orbit satelit. Kami berharap dapat menginspirasi rasa ingin tahu dan memberikan wawasan tentang bagaimana satelit di atas kepala kita bekerja setiap hari.
                        </p>
                    </div>
                </div>
                <div class="center box">
                    <h2>Address</h2>
                    <div class="content">
                        <div class="place">
                            <span class="fas fa-map-marker-alt"></span>
                            <span class="text">Jl. Sukabirus No.A54, Kec. Dayeuhkolot, Kabupaten Bandung, Jawa Barat 40257</span>
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
                <div class="right box">
                    <h2>Contact us</h2>
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
                <center>
                    <span class="credit">Created By LOS TEAM</a> | </span>
                    <span class="far fa-copyright"></span><span> 2025 All rights reserved</span>
                </center>
                </div>
        </footer>

        <script>
            window.onscroll = function() {myFunction()};

            var navbar = document.querySelector(".navigation");
            // Mengubah ambang batas scroll menjadi 0 agar sticky segera aktif
            // atau tetap 50 jika Anda ingin sedikit scroll dulu baru sticky aktif
            var scrollThreshold = 50;

            function myFunction() {
              if (window.pageYOffset > 0) { // Selama tidak di paling atas halaman (posisi scroll > 0)
                navbar.classList.add("sticky");
              } else { // Saat di paling atas halaman (posisi scroll 0)
                navbar.classList.remove("sticky");
              }
            }
        </script>

        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                duration: 1500,
                once: false // Ubah ini menjadi false
            });
        </script>

    </body>
    </html>