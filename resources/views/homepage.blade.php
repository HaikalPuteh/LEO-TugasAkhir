<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LEO Satellite Orbit Simulation</title>

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

        /* Navbar Styling */
        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            background-color: transparent;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: background-color var(--transition-speed), box-shadow var(--transition-speed), top var(--transition-speed);
        }

        .navigation.sticky {
            background-color: var(--dark-bg);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            top: 0;
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

        /* Header Styling */
        #home {
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--light-text);
            padding-top: 60px;
            position: relative;
            overflow: hidden;
        }

        #home::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .header-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            padding: 0 20px;
        }

        .header-content h3 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.6em;
            margin-bottom: 15px;
            color: var(--light-text);
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.8);
            letter-spacing: 1px;
            animation: fadeInDown 1s ease-out;
        }

        .header-content h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 4.2em;
            margin-bottom: 25px;
            text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.9);
            line-height: 1.2;
            animation: fadeInUp 1s ease-out 0.3s forwards;
            opacity: 0;
        }

        .button {
            display: inline-block;
            padding: 14px 60px;
            background-color: transparent;
            color: var(--light-text);
            text-decoration: none;
            border-radius: var(--border-radius);
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color var(--transition-speed), border-color var(--transition-speed), transform var(--transition-speed);
            border: 2px solid var(--light-text);
            cursor: pointer;
            animation: zoomIn 1s ease-out 0.6s forwards;
            opacity: 0;
        }

        .button:hover {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }

        /* Common Title Styling */
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

        /* About Section (Content below About Us title) */
        .about-content {
            padding-bottom: 100px;
            background-color: var(--medium-gray);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .main-card {
            display: flex;
            justify-content: center;
            /* No flex-wrap here, 'cards' will handle it more granularly */
        }

        .cards {
            display: flex;
            flex-wrap: nowrap; /* FORCES them to be in a single row by default */
            justify-content: center;
            gap: 40px;
            /* Ensure there's enough space. If gap is too large, it might still overflow */
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 320px;
            min-width: 280px; /* Prevent cards from shrinking too much on smaller screens */
            text-align: center;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            position: relative;
            overflow: hidden;
            flex-shrink: 0; /* Prevent cards from shrinking if there's not enough space */
            flex-grow: 0; /* Prevent cards from growing */
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(0, 123, 255, 0.05), rgba(255, 255, 255, 0));
            opacity: 0;
            transition: opacity var(--transition-speed);
        }

        .card:hover::before {
            opacity: 1;
        }

        .card:hover {
            transform: translateY(-15px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        .card .content {
            position: relative;
            z-index: 1;
        }

        .card .img {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px;
            border: 6px solid var(--primary-blue);
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.2);
        }

        .card .img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card .details .name {
            font-size: 1.5em;
            font-weight: 700;
            color: var(--dark-gray);
            margin-bottom: 8px;
            font-family: 'Montserrat', sans-serif;
        }

        .card .details .jobdesk {
            font-size: 1.05em;
            color: #666;
            margin-bottom: 20px;
        }

        .card .media-icons a {
            display: inline-block;
            margin: 0 10px;
            font-size: 1.6em;
            color: var(--primary-blue);
            transition: color var(--transition-speed), transform var(--transition-speed);
        }

        .card .media-icons a:hover {
            color: #0056b3;
            transform: scale(1.2) translateY(-2px);
        }

        /* Styling for content in ABOUT WEBSITE section */
        .common-title-section div[style*="text-align: center"] {
            background-color: transparent !important;
            box-shadow: none;
            border-radius: 0;
            padding: 0;
        }

        .common-title-section div[style*="text-align: center"] img {
            border-radius: var(--border-radius);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            margin-bottom: 40px !important;
        }

        .common-title-section div[style*="text-align: center"] p {
            color: var(--dark-gray);
            font-family: 'Poppins', sans-serif;
            font-size: 1.15em;
            line-height: 1.8;
            max-width: 80% !important; /* CHANGED: Now matches the image's max-width */
            margin: 0 auto 20px !important;
            text-align: justify;
            padding: 0 20px;
        }

        .common-title-section .button {
            margin-top: 30px !important;
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            background-color: transparent;
            font-weight: 600;
        }

        .common-title-section .button:hover {
            background-color: var(--primary-blue);
            color: var(--light-text);
            border-color: var(--primary-blue);
        }


        /* Website Section */
        .website {
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            padding: 150px 5%;
            text-align: center;
            color: var(--light-text);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .website::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1;
        }

        .website .content-wrapper {
            position: relative;
            z-index: 2;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .website .content-wrapper h4 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.2em;
            color: var(--light-text);
            margin-bottom: 25px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
            font-weight: 700;
        }

        .website .content-wrapper p {
            font-size: 1.15em;
            line-height: 1.8;
            color: #eee;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
            text-align: justify;
        }

        /* Footer Styling */
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
            align-items: flex-start;
            margin-bottom: 20px;
        }

        footer .place span,
        footer .phone span,
        footer .email span {
            font-size: 1.3em;
            margin-right: 15px;
            color: var(--primary-blue);
            line-height: 1.5;
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
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 15px;
        }

        footer .bottom .credit {
            display: flex;
            align-items: center;
            color: #ccc;
            flex-wrap: wrap;
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
            margin-right: 0;
            cursor: pointer;
            transition: transform var(--transition-speed);
        }

        footer .bottom .credit img:hover {
            transform: rotate(5deg);
        }

        /* Keyframe Animations */
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


        /* Responsive adjustments */
        @media (max-width: 992px) {
            .header-content h1 {
                font-size: 3.5em;
            }

            .website .content-wrapper h4 {
                font-size: 1.8em;
            }

            .common-title-section .title h1 {
                font-size: 2.8em;
            }
        }

        @media (max-width: 768px) {
            .navigation {
                flex-direction: column;
                padding: 10px 5%;
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

            .header-content h1 {
                font-size: 2.8em;
            }

            .header-content h3 {
                font-size: 1.3em;
            }

            .button {
                padding: 12px 50px;
                font-size: 1em;
            }

            .common-title-section,
            .about-content,
            .website {
                padding: 60px 5%;
            }

            .common-title-section .title h1 {
                font-size: 2.2em;
            }

            /* Force cards to stack on smaller screens */
            .main-card,
            .cards {
                flex-direction: column;
                flex-wrap: wrap; /* Allows them to wrap again on small screens if necessary */
                align-items: center;
                gap: 30px; /* Adjusted gap for stacked layout */
            }

            .card {
                width: 90%; /* Almost full width when stacked */
                max-width: 350px; /* Limit max width when stacked */
            }

            footer .main-content {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            footer .main-content div {
                min-width: unset;
                width: 100%;
            }

            footer .place,
            footer .phone,
            footer .email {
                justify-content: center;
                align-items: center;
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
            .header-content h1 {
                font-size: 2.2em;
            }
            .header-content h3 {
                font-size: 1.1em;
            }
            .common-title-section .title h1 {
                font-size: 1.8em;
            }
            .website .content-wrapper h4 {
                font-size: 1.5em;
            }
            .common-title-section div[style*="text-align: center"] p,
            .website .content-wrapper p {
                font-size: 1em;
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

    <header id="home" style="
        background-image: url('<?php echo asset('images/Earth with satellite.jpg'); ?>');
    ">
        <div class="header-content">
            <h3>LEO Satellite Website</h3>
            <h1>Visual Simulation of LEO Orbit Satellite</h1>
            <a href="<?php echo url('/simulation'); ?>" class="button">LAUNCH SIMULATION</a>
        </div>
    </header>

    <section class="common-title-section" id="about" data-aos="fade-up">
        <div class="title">
            <h1>ABOUT US</h1>
        </div>
    </section>

    <section class="about-content">
        <div class="container">
            <div class="main-card">
                <div class="cards">
                    <div class="card" data-aos="fade-up" data-aos-delay="100">
                        <div class="content">
                            <div class="img">
                                <img src="<?php echo asset('images/Kevinn njir.jpg'); ?>" alt="Robby Kevin Putra S.">
                            </div>
                            <div class="details">
                                <div class="name">Robby Kevin Putra Sigit</div>
                                <div class="jobdesk">UI/UX Software</div>
                            </div>
                            <div class="media-icons">
                                <a href="https://mail.google.com/mail/u/0/?view=cm&fs=1&to=robbykevin80@gmail.com" target="_blank" aria-label="Email Robby Kevin"><i
                                        class="fas fa-envelope"></i></a>
                                <a href="https://x.com/cvdvirus19" target="_blank" aria-label="Robby Kevin's Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="https://instagram.com/kevinrobby__" target="_blank" aria-label="Robby Kevin's Instagram"><i
                                        class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card" data-aos="fade-up" data-aos-delay="200">
                        <div class="content">
                            <div class="img">
                                <img src="<?php echo asset('images/Foto Viandra.jpg'); ?>" alt="I Dewa Made Raviandra W.">
                            </div>
                            <div class="details">
                                <div class="name">I Dewa Made Raviandra W.</div>
                                <div class="jobdesk">Simulation Developer</div>
                            </div>
                            <div class="media-icons">
                                <a href="https://mail.google.com/mail/u/0/?view=cm&fs=1&to=raviandrawedagama01@gmail.com" target="_blank" aria-label="Email I Dewa Made Raviandra"><i
                                        class="fas fa-envelope"></i></a>
                                <a href="https://x.com/i_wedagama" target="_blank" aria-label="I Dewa Made Raviandra's Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="https://instagram.com/raviandra_wedagama" target="_blank" aria-label="I Dewa Made Raviandra's Instagram"><i
                                        class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card" data-aos="fade-up" data-aos-delay="300">
                        <div class="content">
                            <div class="img">
                                <img src="<?php echo asset('images/naahh.jpg'); ?>" alt="M. Haikal Puteh">
                            </div>
                            <div class="details">
                                <div class="name">Muhammad Haikal Puteh</div>
                                <div class="jobdesk">UI/UX Homepage</div>
                            </div>
                            <div class="media-icons">
                                <a href="https://mail.google.com/mail/u/0/?view=cm&fs=1&to=ethanhaikal@gmail.com" target="_blank" aria-label="Email Muhammad Haikal"><i
                                        class="fas fa-envelope"></i></a>
                                <a href="https://x.com/62hityourmind" target="_blank" aria-label="Muhammad Haikal's Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="https://instagram.com/622kal" target="_blank" aria-label="Muhammad Haikal's Instagram"><i
                                        class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="common-title-section" data-aos="fade-up" data-aos-delay="100">
        <div class="title">
            <h1>ABOUT WEBSITE</h1>
        </div>
        <div style="text-align: center; padding: 25px 0; margin-bottom: 50px;">
            <img src="<?php echo asset('images/website.png'); ?>" alt="Website Design"
                style="max-width: 80%; height: auto; display: block; margin: 0 auto 15px;">
            <p>
                Platform ini dirancang sebagai hub sentral untuk komunikasi dan kolaborasi yang dinamis dan efisien. Fokus utamanya adalah memfasilitasi interaksi real-time, memungkinkan tim dan individu untuk terhubung tanpa batas melintasi berbagai lokasi. Antarmuka yang intuitif menampilkan daftar kontak atau partisipan, lengkap dengan indikator status visual yang jelas, seperti 'online', 'sibuk', atau 'sedang berbagi layar'. Ini sangat penting untuk meningkatkan koordinasi tim dan memungkinkan pengguna untuk mengidentifikasi ketersediaan rekan kerja secara instan sebelum memulai komunikasi.
            </p>
            <p>
                Fitur berbagi layar adalah inti dari pengalaman kolaborasi ini, memungkinkan pengguna untuk menampilkan presentasi, dokumen, atau bahkan aplikasi secara langsung kepada partisipan lain, menjadikan rapat virtual seefektif pertemuan tatap muka. Kemampuan untuk melihat siapa yang berbicara atau sedang aktif melalui indikator visual membantu menjaga alur diskusi tetap teratur, terutama dalam grup besar. Dengan penekanan pada fungsionalitas yang mulus dan penyampaian informasi yang efisien, platform ini bertujuan untuk merevolusi cara kerja tim berinteraksi, berdiskusi, dan mencapai tujuan bersama dalam lingkungan digital yang semakin terhubung.
            </p>
            <a href="<?php echo url('/simulation'); ?>" class="button">LAUNCH SIMULATION</a>
        </div>
    </section>

    <section class="website" id="project" data-aos="fade-up"
        style="background-image: url('<?php echo asset('images/satellitepanorama.jpg'); ?>');">
        <div class="content-wrapper">
            <h4>LEO ORBIT SATELLITE</h4>
            <p>LEO Orbit Satellite (LOS) Website adalah platform simulasi interaktif yang dirancang untuk
                memvisualisasikan lintasan orbit satelit LEO (Low Earth Orbit) di sekitar Bumi. Tujuan utama kami adalah
                mengedukasi publik tentang cara kerja satelit ini. Pengguna dapat dengan mudah mengatur berbagai
                parameter orbit, seperti ketinggian satelit, untuk mengamati secara langsung bagaimana perubahan
                tersebut memengaruhi pergerakan satelit dan pancaran (coverage) yang dihasilkannya. </p>
        </div>
    </section>

    <footer>
        <div class="main-content">
            <div class="left box" data-aos="fade-right">
                <h2>About Us</h2>
                <div class="content">
                    <p>Halo semuanya!
                        Kami adalah sekelompok individu yang bersemangat tentang antariksa dan teknologi. Melalui LEO
                        Satellite Website, kami berupaya menjembatani kesenjangan antara konsep teoretis dan pemahaman
                        visual tentang satelit Low Earth Orbit (LEO). Proyek ini dibangun dengan tujuan untuk menjadi
                        alat edukasi yang intuitif, memungkinkan pengguna untuk tidak hanya melihat, tetapi juga
                        berinteraksi dengan simulasi orbit satelit. Kami berharap dapat menginspirasi rasa ingin tahu
                        dan memberikan wawasan tentang bagaimana satelit di atas kepala kita bekerja setiap hari.
                    </p>
                    <div class="social">
                        <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://youtube.com" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
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

        // Navbar Sticky and Hide on Scroll
        var navbar = document.querySelector(".navigation");
        var prevScrollpos = window.pageYOffset;
        var logoOriginalHeight = navbar.querySelector('.logo img').offsetHeight;
        var logoShrinkHeight = 40;

        window.onscroll = function() {
            var currentScrollPos = window.pageYOffset;

            // Add/remove sticky class based on scroll position
            if (currentScrollPos > 50) {
                navbar.classList.add("sticky");
                navbar.querySelector('.logo img').style.height = logoShrinkHeight + 'px';
            } else {
                navbar.classList.remove("sticky");
                navbar.querySelector('.logo img').style.height = logoOriginalHeight + 'px';
            }

            // Hide/show navbar on scroll up/down
            if (prevScrollpos > currentScrollPos || currentScrollPos < 50) {
                navbar.style.top = "0";
            } else {
                navbar.style.top = "-90px";
            }
            prevScrollpos = currentScrollPos;
        };

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