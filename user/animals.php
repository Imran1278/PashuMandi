<?php 
include '../db.php'; 

// 1. Filtering Logic
$category_filter = "";
$display_title = "All Registered Animals";

if(isset($_GET['cat']) && !empty($_GET['cat'])) {
    $cat = mysqli_real_escape_string($conn, $_GET['cat']);
    $cat_decoded = urldecode($cat);
    // Filtering sirf tab apply hogi jab category select ho
    $category_filter = " WHERE (category LIKE '%$cat%' OR category LIKE '%" . explode(' ', $cat_decoded)[0] . "%')";
    $display_title = htmlspecialchars($cat_decoded);
} else {
    // Agar koi category nahi hai, toh koi filter nahi lagega (Sare show honge)
    $category_filter = ""; 
}

// 2. Fetching ALL animals (Status hata diya hai taake har qism ke animals show hon)
$query = "SELECT * FROM animals $category_filter ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $display_title ?> | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-teal: #14b8a6; --p-dark: #0f172a; --p-bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--p-bg); color: var(--p-dark); }

        .page-header { background: white; padding: 60px 0; border-bottom: 1px solid #f1f5f9; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        
        .animal-card { 
            background: white; border-radius: 30px; overflow: hidden; 
            border: 1px solid #f1f5f9; transition: 0.4s; height: 100%;
            position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }
        .animal-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        
        .img-container { height: 240px; overflow: hidden; position: relative; }
        .img-container img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s ease; }
        .animal-card:hover .img-container img { transform: scale(1.1); }

        .price-badge { 
            position: absolute; top: 15px; right: 15px; 
            background: white; color: var(--p-dark); padding: 8px 18px; 
            border-radius: 15px; font-weight: 800; font-size: 0.95rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); z-index: 2;
        }

        /* Video Indicator Badge */
        .video-badge {
            position: absolute; bottom: 15px; left: 15px;
            background: rgba(239, 68, 68, 0.9); color: white;
            padding: 5px 12px; border-radius: 10px; font-size: 0.7rem;
            font-weight: 800; text-transform: uppercase; z-index: 2;
        }

        .category-tag { 
            background: rgba(20, 184, 166, 0.1); color: var(--p-teal); 
            padding: 5px 14px; border-radius: 10px; font-size: 0.7rem; 
            font-weight: 800; text-transform: uppercase;
        }

        .card-body { padding: 25px; }
        .animal-title { font-weight: 800; font-size: 1.15rem; color: var(--p-dark); margin-bottom: 5px; }
        .location { font-size: 0.85rem; color: #64748b; margin-bottom: 20px; }
        
        .view-btn { 
            width: 100%; background: var(--p-dark); color: white; 
            padding: 14px; border-radius: 18px; font-weight: 700; transition: 0.3s;
            text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .view-btn:hover { background: var(--p-teal); color: white; box-shadow: 0 10px 20px rgba(20, 184, 166, 0.2); }

        .empty-state { text-align: center; padding: 100px 0; }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container text-center">
        <span class="badge bg-teal-subtle text-success px-3 py-2 rounded-pill mb-3 fw-bold border border-success-subtle">LIVE INVENTORY</span>
        <h1 class="fw-800 display-5"><?= $display_title ?></h1>
        <p class="text-muted fs-5">Showing all verified livestock available in the mandi</p>
        
        <?php if(!empty($category_filter)): ?>
            <a href="animals.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 mt-2 fw-bold">
                <i class="fa fa-times-circle me-1"></i> Clear Filters
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): 
                // Agar images comma separated hain toh pehli image lein
                $imgs = explode(',', $row['image']);
                $main_img = !empty($imgs[0]) ? $imgs[0] : 'default.jpg';
            ?>
                <div class="col-lg-3 col-md-6">
                    <div class="animal-card">
                        <div class="img-container">
                            <span class="price-badge">Rs <?= number_format($row['price']) ?></span>
                            
                            <?php if(!empty($row['video'])): ?>
                                <span class="video-badge"><i class="fa fa-play-circle me-1"></i> Video</span>
                            <?php endif; ?>

                            <img src="../uploads/<?= $main_img ?>" alt="<?= htmlspecialchars($row['title']) ?>" onerror="this.src='../pics/default_animal.jpg'">
                        </div>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="category-tag"><?= htmlspecialchars($row['category']) ?></span>
                                <small class="text-muted fw-bold"><?= htmlspecialchars($row['sub_category']) ?></small>
                            </div>
                            
                            <h5 class="animal-title text-truncate"><?= htmlspecialchars($row['title']) ?></h5>
                            
                            <p class="location">
                                <i class="fa-solid fa-location-dot text-teal me-1"></i> <?= htmlspecialchars($row['location']) ?>
                            </p>
                            
                            <a href="../user/animal_details.php?id=<?= $row['id'] ?>" class="view-btn">
                                View Full Details <i class="fa-solid fa-arrow-right-long"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 empty-state">
                <div class="bg-white d-inline-block p-5 rounded-5 shadow-sm">
                    <i class="fa-solid fa-cow fa-4x text-muted opacity-25 mb-4"></i>
                    <h3 class="fw-800">No Listings Found</h3>
                    <p class="text-muted">Currently, there are no animals matching your criteria.</p>
                    <a href="animals.php" class="btn btn-dark rounded-pill px-4 mt-2">Refresh Mandi</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>