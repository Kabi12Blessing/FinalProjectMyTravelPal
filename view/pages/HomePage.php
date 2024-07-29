<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelPal</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            scroll-behavior: smooth;
            background-color: #f5f7fa;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
            background-color: rgba(0, 0, 0, 0.3);
        }
        .header .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        .header .user-menu {
            display: flex;
            align-items: center;
            position: relative;
        }
        .header .dashboard {
            color: white;
            font-weight: bold;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
            padding: 10px 20px;
            border: none;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 5px;
        }
        .header .dashboard:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .header .username {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            position: relative;
        }
        .header .dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            display: none;
            border-radius: 5px;
            overflow: hidden;
            min-width: 100%;
        }
        .header .dropdown a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }
        .header .dropdown a:hover {
            background-color: #f1f1f1;
        }
        .hero {
            height: 100vh;
            background-image: url('https://rawcdn.githack.com/BlessingLeslie/TravelPalImages/f3fa5a30650b6390c7e41a26ff7550836e3a6f24/DALL%C2%B7E%202024-06-11%2000.17.43%20-%20A%20highly%20realistic%20airport%20scene%20featuring%20two%20Black%20people.%20One%20person%20is%20sharing%20an%20item%20with%20another%20traveler%20who%20has%20extra%20luggage.%20The%20traveler%20i.webp');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero h1 {
            font-size: 56px;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 24px;
            margin-bottom: 30px;
        }
        .search-box {
            display: flex;
            justify-content: center;
        }
        .search-box input[type="text"] {
            padding: 10px;
            font-size: 15px;
            border: none;
            border-radius: 5px 0 0 5px;
            outline: none;
            width: 300px;
        }
        .search-box button {
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
        }
        .search-box button:hover {
            background-color: #0056b3;
        }
        .arrow-down {
            font-size: 32px;
            color: white;
            margin-top: 30px;
            animation: bounce 2s infinite;
            cursor: pointer;
        }
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        .content {
            padding: 50px 20px;
            background-color: #f5f7fa;
            text-align: center;
        }
        .section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            
            /* border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); */
            margin-bottom: 50px;
        }
        .section h2 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #333;
        }
        .section p {
            font-size: 18px;
            color: #666;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .stat {
            flex: 1;
            margin: 10px;
            padding: 20px;
            border-radius: 10px;
            background-color: #eef6ff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .stat i {
            font-size: 50px;
            color: #007BFF;
            margin-bottom: 10px;
        }
        .stat h3 {
            font-size: 36px;
            margin: 10px 0;
            color: #333;
        }
        .stat p {
            font-size: 18px;
            color: #555;
        }
        .categories, .travelling-this-week {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .category, .travelling-item {
            flex: 1 1 calc(20% - 20px);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            background-color: #fff;
        }
        .category img, .travelling-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .category:hover, .travelling-item:hover {
            transform: translateY(-10px);
        }
        .category h3, .travelling-item h3 {
            font-size: 20px;
            margin: 15px;
            color: #333;
        }
        .travelling-item p {
            font-size: 16px;
            margin: 0 15px 15px 15px;
            color: #555;
        }
        .testimonial-section, .newsletter-section {
            padding: 50px 20px;
            background-color: #f5f5f5;
            text-align: center;
        }
        .testimonial-slider {
            display: flex;
            justify-content: center;
            overflow: hidden;
        }
        .testimonial {
            max-width: 300px;
            margin: 0 10px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .testimonial:hover {
            transform: translateY(-10px);
        }
        .testimonial img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        .testimonial p {
            font-size: 16px;
            color: #555;
            margin-bottom: 15px;
        }
        .testimonial h3 {
            font-size: 18px;
            color: #333;
        }
        .newsletter {
            max-width: 600px;
            margin: 0 auto;
            background-color: #eef6ff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .newsletter h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        .newsletter p {
            font-size: 16px;
            margin-bottom: 20px;
            color: #666;
        }
        .newsletter form {
            display: flex;
            justify-content: center;
        }
        .newsletter input[type="email"] {
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px 0 0 5px;
            outline: none;
            width: 70%;
        }
        .newsletter button {
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            transition: background-color 0.3s;
        }
        .newsletter button:hover {
            background-color: #0056b3;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
        }
        .footer a {
            color: #007BFF;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>
<body>
    <div class="header">
        <div class="logo">TravelPal</div>
        <?php if (isset($_SESSION['username'])): ?>
            <div class="user-menu">
                <a href="Dashboard.php" class="dashboard">Dashboard</a>
                <div class="username" onclick="toggleDropdown()">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></div>
                <div class="dropdown">
                    <a href="Dashboard.php">Dashboard</a>
                    <a href="/MyTravelPal/action/logout.php">Log Out</a>
                </div>
            </div>
        <?php else: ?>
            <a href="../LandingPage.php"></a>
        <?php endif; ?>
    </div>
    <div class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Welcome to TravelPal</h1>
            <p>Connect with fellow travelers, share extra luggage space, and make new friends on your journeys.</p>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search for travel destination">
                <button onclick="searchCountry()">Search</button>
            </div>
            <div id="searchResult" style="color: white; margin-top: 20px;"></div>
            <div id="arrow-down" class="arrow-down">&#x2193;</div>
        </div>
    </div>
    <div id="acontent" class="content">
        <div class="section about">
            <h2>About TravelPal</h2>
            <p>TravelPal connects you with fellow travelers who have extra luggage space, allowing you to send packages easily and securely. Our community is built on trust, and every traveler is verified to ensure a safe experience.</p>
        </div>
        <div class="section stats">
            <div class="stat">
                <i class="fas fa-suitcase"></i>
                <h3>1000k+</h3>
                <p>Travel matches made</p>
            </div>
            <div class="stat">
                <i class="fas fa-map-marker-alt"></i>
                <h3>5k+</h3>
                <p>Shipping Destinations</p>
            </div>
            <div class="stat">
                <i class="fas fa-shield-alt"></i>
                <h3>4.0</h3>
                <p>Trust score</p>
            </div>
        </div>
        <div style= "background: linear-gradient(to right, #1E90FF, #00BFFF); border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" class="section">
            <h2>Focus Countries</h2>
            <div class="categories">
                <div class="category">
                    <img src="https://rawcdn.githack.com/Kabi12Blessing/images/d7d6a83e4b8a3ee80df571c0f3299086d2b99847/CAMEROON.png" alt="Cameroon">
                    <h3>Cameroon</h3>
                </div>
                <div class="category">
                    <img src="https://rawcdn.githack.com/Kabi12Blessing/images/d7d6a83e4b8a3ee80df571c0f3299086d2b99847/CANADA.png" alt="Canada">
                    <h3>Canada</h3>
                </div>
                <div class="category">
                    <img src="https://rawcdn.githack.com/Kabi12Blessing/images/d7d6a83e4b8a3ee80df571c0f3299086d2b99847/FRANCE.png" alt="France">
                    <h3>France</h3>
                </div>
                <div class="category">
                    <img src="https://rawcdn.githack.com/Kabi12Blessing/images/d7d6a83e4b8a3ee80df571c0f3299086d2b99847/GHANA.png" alt="Ghana">
                    <h3>Ghana</h3>
                </div>
                <div class="category">
                    <img src="https://rawcdn.githack.com/Kabi12Blessing/images/d7d6a83e4b8a3ee80df571c0f3299086d2b99847/KENYA.png" alt="Kenya">
                    <h3>Kenya</h3>
                </div>
                <div class="category">
                    <img src="https://rawcdn.githack.com/Kabi12Blessing/images/d7d6a83e4b8a3ee80df571c0f3299086d2b99847/LONDON.png" alt="London">
                    <h3>London</h3>
                </div>
                <div class="category">
                    <img src="https://rawcdn.githack.com/Kabi12Blessing/images/d7d6a83e4b8a3ee80df571c0f3299086d2b99847/NIGERIA.png" alt="Nigeria">
                    <h3>Nigeria</h3>
                </div>
                <div class="category">
                    <img src="https://rawcdn.githack.com/Kabi12Blessing/images/d7d6a83e4b8a3ee80df571c0f3299086d2b99847/NORWAY.png" alt="Norway">
                    <h3>Norway</h3>
                </div>
                <div class="category">
                    <img src="https://rawcdn.githack.com/Kabi12Blessing/images/d7d6a83e4b8a3ee80df571c0f3299086d2b99847/SOUTHAFRICA.png" alt="South Africa">
                    <h3>South Africa</h3>
                </div>
                <div class="category">
                    <img src="https://rawcdn.githack.com/Kabi12Blessing/images/d7d6a83e4b8a3ee80df571c0f3299086d2b99847/USA.png" alt="USA">
                    <h3>USA</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="newsletter-section">
        <div class="newsletter">
            <h2>Subscribe to Our Newsletter</h2>
            <p>Stay updated with the latest news and offers from TravelPal. Subscribe to our newsletter now!</p>
            <form>
                <input type="email" placeholder="Enter your email">
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 TravelPal. All rights reserved. | <a href="/view/privacy-policy.php">Privacy Policy</a> | <a href="/view/terms-of-service.php">Terms of Service</a></p>
    </div>
    <script>
        document.getElementById('arrow-down').addEventListener('click', function() {
            document.getElementById('acontent').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        function toggleDropdown() {
            var dropdown = document.querySelector('.dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.username')) {
                var dropdowns = document.querySelectorAll('.dropdown');
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === 'block') {
                        openDropdown.style.display = 'none';
                    }
                }
            }
        }

        function searchCountry() {
            var searchInput = document.getElementById('searchInput').value.toLowerCase();
            var countries = ["cameroon", "canada", "france", "ghana", "kenya", "london", "nigeria", "norway", "south africa", "usa"];
            var resultDiv = document.getElementById('searchResult');
            if (countries.includes(searchInput)) {
                resultDiv.innerText = "Great! We have travel options and experiences available for " + searchInput.charAt(0).toUpperCase() + searchInput.slice(1) + ".";
            } else {
                resultDiv.innerText = "We're expanding our destinations! Unfortunately, " + searchInput.charAt(0).toUpperCase() + searchInput.slice(1) + " isn't available yet.";
            }
        }
    </script>
</body>
</html>
