<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ShoreKeeper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      body {
        background-color: #0F1C2C;
        color: #fff;
      }

      .search-bar {
        background-color: #fff;
        border: 2px solid #F8EBDD;
        border-radius: 50px;
        padding: 6px 12px;
        width: 600px;
        max-width: 90%;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      }
      .search-bar input {
        background: transparent;
        border: none;
        outline: none;
        color: black;
        flex-grow: 1;
        font-size: 1rem;
        padding-left: 10px;
      }
      .search-bar input::placeholder {
        color: #666;
        opacity: 0.9;
      }
      .search-bar button {
        background-color: #2E3D54;
        border: none;
        color: #fff;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: all 0.2s ease;
      }
      .search-bar button:hover {
        background-color: #e60023;
        color: #fff;
      }
   
      .main-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 60px;
        gap: 20px;
      }

      .login {
        background-color: #fff;
        color: #000;
        border: 2px solid #fff;
        border-radius: 10px;
      }

      .login:hover {
        background-color: #e60023;
        color: #fff;
      }

      .scroll-container {
        display: flex;
        overflow-x: auto;
        gap: 1rem;
        padding: 2rem;
        scroll-behavior: smooth;
      }
      .scroll-container::-webkit-scrollbar {
        height: 8px;
      }
      .scroll-container::-webkit-scrollbar-thumb {
        background-color: rgba(255,255,255,0.2);
        border-radius: 10px;
      }

      .custom-card {
        min-width: 250px;
        background-color: #69B7FF;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
        transition: transform 0.3s ease;
      }
      .custom-card:hover {
        transform: translateY(-5px);
      }
      .card-img-top {
        height: 150px;
        object-fit: cover;
      }
      .category-tag {
        background-color: #FF004D;
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 3px;
        display: inline-block;
        margin-bottom: 6px;
      }
        .card-body h6 {
        color: white;
        font-weight: 700;
      }

        .top-wikis-section {
        background-color: #2E3D54;
        padding: 2rem 3rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
      }
        .top-wikis-title {
        background-color: #FF4B3E;
        color: #fff;
        font-weight: 700;
        padding: 10px 18px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-radius: 2px;
      }
      .top-wikis-title i {
        font-size: 1.2rem;
      }
      .wikis-columns {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        align-items: start;
      }
      .wiki-category {
        border-left: 1px solid rgba(255,255,255,0.2);
        padding-left: 1.5rem;
      }
      .wiki-category:first-child {
        border-left: none;
        padding-left: 0;
      }
      .wiki-category h6 {
        font-weight: 700;
        color: #FF4B3E;
        margin-bottom: 0.5rem;
      }
      .wiki-category ul {
        list-style: none;
        padding: 0;
        margin: 0;
      }
      .wiki-category ul li {
        margin-bottom: 4px;
      }
      .wiki-category ul li a {
        color: white;
        text-decoration: none;
        font-weight: 600;
      }
      .wiki-category ul li a:hover {
        text-decoration: underline;
      }

      .explore-more {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        border-left: 1px solid rgba(255,255,255,0.2);
        padding-left: 1.5rem;
      }
      .explore-more .arrow-btn {
        background-color: #FF4B3E;
        color: #fff;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 2px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.3rem;
      }

      .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 6px solid #FF4B3E;
        padding: 1.2rem 0;
        margin-bottom: 1rem;
      }

      .section-header .category-title {
        background-color: #FF4B3E;
        color: #fff;
        font-weight: 700;
        font-size: 1.4rem;
        padding: 8px 16px;
        letter-spacing: 1px;
      }

      .section-header .view-all {
        color: #000;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
      }

      .section-header .view-all i {
        margin-left: 5px;
        font-size: 1rem;
      }

      .subheading {
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
      }

      .movie-card {
        border: none;
        background: transparent;
        text-align: left;
      }

      .movie-card img {
        border-bottom: 3px solid #FF4B3E;
        border-radius: 0;
        transition: transform 0.3s ease;
      }

      .movie-card img:hover {
        transform: scale(1.03);
      }

      .movie-card .card-title {
        font-weight: 700;
        color: #fff;
        font-size: 1rem;
        margin-top: 0.5rem;
        margin-bottom: 0.2rem;
      }

      .movie-card .card-subtitle {
        font-weight: 500;
        font-size: 0.9rem;
        color: #444;
      }

      .movie-card .card-subtitle i {
        margin-right: 5px;
      }
   
    .section-title {
      color: #fff;
      font-weight: 700;
      margin-bottom: 1rem;
    }
    .news-card {
      border: none;
      border-radius: 10px;
      overflow: hidden;
      background-color: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    .news-card:hover {
      transform: translateY(-5px);
    }
    .card-img-top {
      height: 250px;
      object-fit: cover;
    }
    .badge-gs {
      background-color: #e60023;
      font-weight: 600;
      font-size: 0.9rem;
      position: absolute;
      bottom: 10px;
      left: 10px;
      padding: 0.4rem 0.8rem;
    }
    .card-title {
      font-weight: 700;
      font-size: 1.25rem;
      line-height: 1.4;
    }
    .card-text {
      color: #6c757d;
      font-size: 0.95rem;
    }

    .underline-nav {
        display: flex;
        gap: 30px;
    }

    .underline-nav a {
        position: relative;
        color: #fff;
        text-decoration: none;
        font-size: 18px;
        padding: 10px 0;
        transition: .5s ease;
    }

    .underline-nav a:hover {
        color: #FF004D;
    }

    .underline-nav a::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 3px;
        background-color: #FF004D;
        transition: width 0.3s ease;
    }

    .underline-nav a:hover::after {
        width: 100%;
    } 

    .avatar-img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      cursor: pointer;
      transition: 0.2s;
    }

    .avatar-img:hover {
      box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }

    .user-dropdown {
      
      position: relative;
    }
    .dropdown-menu {
      background-color: #2E3D54;
      color: #fff;
      min-width: 150px;
      border-radius: 10px;
      padding: 10px 0;
    }

    .dropdown-menu a:hover {
      background-color: #FF004D;
      color: #fff;
    }
    .dropdown-submenu {
      position: relative;
    }
    .dropdown-submenu .dropdown-menu {
      display: none;
      position: absolute;
      top: 0;
      left: 100%;       
      margin-left: 0;
      margin-top: 0;
    }
    .dropdown-submenu:hover .dropdown-menu {
      display: block;
    }

  </style>
</head>
<body>