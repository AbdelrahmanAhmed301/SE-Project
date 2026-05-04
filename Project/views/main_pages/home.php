


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worklio - Freelance Marketplace</title>
    <link rel="stylesheet" href="../../public/assets/css/home.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2>Worklio</h2>
            </div>
            <ul class="nav-links">
                <li><a href="../../views/main_pages/home.php">Home...</a></li>
                <li><a href="../../views/main_pages/about.php">About...</a></li>
                <li><a href="../../Auth/login.php" class="btn-login">Login...</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Connect with Specialized Professionals</h1>
            <p>Find expert freelancers in Data Science, Legal Consulting, Translation, and more</p>
            <div class="cta-buttons">
                <a href="client.html" class="btn btn-primary">Hire Specialists</a>
                <a href="freelancer.html" class="btn btn-secondary">Find Work</a>
            </div>
        </div>
    </section>

    <!-- User Type Cards -->
    <section class="user-types">
        <div class="container">
            <h2>Join as a Client or Freelancer</h2>
            <div class="cards">
                <div class="card">
                    <div class="card-icon">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3>I'm a Client</h3>
                    <p>Post projects and hire specialized professionals for your business needs</p>
                    <a href="client.html" class="card-link">Get Started →</a>
                </div>

                <div class="card">
                    <div class="card-icon">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                    </div>
                    <h3>I'm a Freelancer</h3>
                    <p>Showcase your expertise and work on high-value specialized projects</p>
                    <a href="freelancer.html" class="card-link">Get Started →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2>Why Worklio?</h2>
            <div class="feature-grid">
                <div class="feature">
                    <h3>Verified Specialists</h3>
                    <p>All professionals are verified with credentials and certifications</p>
                </div>
                <div class="feature">
                    <h3>Milestone-Based Payments</h3>
                    <p>Secure escrow system releasing payments as project phases complete</p>
                </div>
                <div class="feature">
                    <h3>Dispute Resolution</h3>
                    <p>Professional arbitration to ensure fair outcomes for all parties</p>
                </div>
                <div class="feature">
                    <h3>Niche Expertise</h3>
                    <p>Focus on specialized fields requiring professional qualifications</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Worklio</h3>
                    <p>The marketplace for specialized professional services</p>
                </div>
                <div class="footer-section">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="../../views/main_pages/about.php">About Us</a></li>
                        <li><a href="#">How It Works</a></li>
                        <li><a href="../../views/main_pages/Contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>For Clients</h4>
                    <ul>
                        <li><a href="../../views/Client/client-dashboard.php">Post a Project</a></li>
                        <li><a href="#">Browse Specialists</a></li>
                        <li><a href="#">Pricing</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>For Freelancers</h4>
                    <ul>
                        <li><a href="../../views/Freelancer/freelancer-dashboard.php">Find Work</a></li>
                        <li><a href="#">Get Verified</a></li>
                        <li><a href="#">Resources</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Worklio. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>