<?php
include '../db.php';
// 1. Inputs Sanitization
$q = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$cat = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$breed = isset($_GET['breed']) ? mysqli_real_escape_string($conn, $_GET['breed']) : '';
$loc = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';
$min = isset($_GET['min_p']) && is_numeric($_GET['min_p']) ? $_GET['min_p'] : '';
$max = isset($_GET['max_p']) && is_numeric($_GET['max_p']) ? $_GET['max_p'] : '';

// 2. Dynamic Query (Using Secure Joins)
$sql = "SELECT a.*, u.full_name, u.address as user_city 
        FROM animals a 
        JOIN users u ON a.user_id = u.id 
        WHERE 1=1";

if(!empty($q)) { $sql .= " AND (a.title LIKE '%$q%' OR a.description LIKE '%$q%')"; }
if(!empty($cat)) { $sql .= " AND a.category = '$cat'"; }
if(!empty($breed)) { $sql .= " AND a.brand LIKE '%$breed%'"; }
if(!empty($loc)) { $sql .= " AND (a.location LIKE '%$loc%' OR u.address LIKE '%$loc%')"; }
if(!empty($min)) { $sql .= " AND a.price >= $min"; }
if(!empty($max)) { $sql .= " AND a.price <= $max"; }

$sql .= " ORDER BY a.id DESC";
$results = mysqli_query($conn, $sql);
$total_found = mysqli_num_rows($results);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Best Deals | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --secondary: #4f46e5;
            --accent-glow: rgba(37, 99, 235, 0.1);
            --glass: rgba(255, 255, 255, 0.8);
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f1f5f9; 
            color: var(--text-dark);
        }

        /* Hero Section Styling */
        .search-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 80px 0 60px;
            color: white;
            border-radius: 0 0 40px 40px;
            margin-bottom: -40px;
        }

        .text-gradient {
            background: linear-gradient(to right, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Glass Cards */
        .filter-card {
            background: var(--glass);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .listing-card {
            background: #fff;
            border-radius: 22px;
            border: none;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            position: relative;
        }

        .listing-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 30px 50px rgba(0, 0, 0, 0.1);
        }

        /* Image & Badges */
        .img-container {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.6s;
        }

        .listing-card:hover .img-container img {
            transform: scale(1.1);
        }

        .price-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            color: white;
            padding: 6px 14px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .category-pill {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 800;
        }

        /* Forms & Buttons */
        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px var(--accent-glow);
            border-color: var(--primary);
        }

        .btn-update {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 14px;
            padding: 12px;
            color: white;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-update:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        .btn-details {
            background: #f1f5f9;
            color: var(--text-dark);
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            transition: 0.3s;
            text-decoration: none;
            padding: 8px 15px;
            display: inline-block;
        }

        .btn-details:hover {
            background: var(--primary);
            color: white;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body>

<header class="search-header">
    <div class="container text-center">
        <h1 class="fw-800 display-5 mb-3">Find Your Perfect <span class="text-gradient">Match</span></h1>
        <p class="opacity-75 mb-0">Browsing <?= $total_found ?> premium livestock listings near you</p>
    </div>
</header>

<main class="container py-5 mt-4">
    <div class="row g-4">
        
        <div class="col-lg-3">
            <div class="filter-card mb-4">
                <div class="d-flex align-items-center mb-4">
                    <i class="fa-solid fa-sliders me-3 text-primary"></i>
                    <h5 class="fw-800 mb-0">Filters</h5>
                </div>
                
                <form action="" method="GET">
                    <div class="mb-3">
                        <label class="small fw-700 text-muted mb-2">SEARCH KEYWORD</label>
                        <input type="text" name="q" class="form-control" placeholder="Cow, Goat, Buffalo..." value="<?= $q ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="small fw-700 text-muted mb-2">MAX BUDGET (RS)</label>
                        <input type="number" name="max_p" class="form-control" placeholder="Any Price" value="<?= $max ?>">
                    </div>

                    <div class="mb-4">
                        <label class="small fw-700 text-muted mb-2">LOCATION</label>
                        <select name="location" class="form-control">
                            <option value="">All Regions</option>
                            <option value="Punjab" <?= $loc == 'Punjab' ? 'selected' : '' ?>>Punjab</option>
                            <option value="Sindh" <?= $loc == 'Sindh' ? 'selected' : '' ?>>Sindh</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-update w-100 shadow-sm">
                        Apply Filters
                    </button>
                </form>
            </div>

            <div class="bg-white p-4 rounded-4 shadow-sm border-start border-primary border-4">
                <h6 class="fw-800">Quick Tip!</h6>
                <p class="small text-muted mb-0">Ziada behtar results ke liye breed ka naam search karein.</p>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="row g-4">
                <?php if($total_found > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($results)): ?>
                        <div class="col-md-4 col-sm-6">
                            <article class="listing-card shadow-sm">
                                <div class="img-container">
                                    <img src="../uploads/<?= $row['image'] ?>" alt="animal image">
                                    <div class="price-badge">Rs. <?= number_format($row['price']) ?></div>
                                    <span class="category-pill"><?= $row['category'] ?></span>
                                </div>
                                
                                <div class="p-4">
                                    <h6 class="fw-800 text-truncate mb-2"><?= htmlspecialchars($row['title']) ?></h6>
                                    
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fa-solid fa-location-dot text-danger me-2 small"></i>
                                        <span class="small fw-600 text-muted"><?= htmlspecialchars($row['location'] ?? $row['user_city']) ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                        <div>
                                            <span class="text-success small fw-800"><i class="fa-solid fa-circle-check me-1"></i>VERIFIED</span>
                                        </div>
                                        <a href="product_details.php?id=<?= $row['id'] ?>" class="btn-details">
                                            Details
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5 bg-white rounded-5 shadow-sm">
                            <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" style="width: 120px; opacity: 0.3;" alt="No results">
                            <h4 class="fw-800 mt-4">No results found for "<?= $q ?>"</h4>
                            <p class="text-muted">Try changing your filters or searching for something else.</p>
                            <a href="search_results.php" class="btn btn-primary rounded-pill px-4 fw-bold">Reset All Filters</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>