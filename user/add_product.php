<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$u_id = $_SESSION['user_id'];
$error = "";

if(isset($_POST['post_now'])) {
    // Basic Fields - Inhain wapis add kiya gaya hai
    $cat = mysqli_real_escape_string($conn, $_POST['category']);
    $sub_cat = mysqli_real_escape_string($conn, $_POST['sub_category']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']); 
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $show_num = isset($_POST['show_num']) ? (int)$_POST['show_num'] : 1;

    $uploaded_images = [];
    $video_file = "";
    $target_dir = "../uploads/";

    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    // Multiple Images Handling
    if(!empty($_FILES['images']['name'][0])) {
        foreach($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $img_name = time() . "_img_" . $key . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["images"]["name"][$key]));
            if(move_uploaded_file($tmp_name, $target_dir . $img_name)) {
                $uploaded_images[] = $img_name;
            }
        }
    }

    // Video Handling
    if(!empty($_FILES['video']['name'])) {
        $video_file = time() . "_vid_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["video"]["name"]));
        move_uploaded_file($_FILES['video']['tmp_name'], $target_dir . $video_file);
    }

    if(!empty($uploaded_images)) {
        $all_images = implode(",", $uploaded_images); 
        $sql = "INSERT INTO animals (user_id, title, category, sub_category, brand, price, description, location, image, video, show_phone) 
                VALUES ('$u_id', '$title', '$cat', '$sub_cat', '$brand', '$price', '$desc', '$loc', '$all_images', '$video_file', '$show_num')";
        
        if(mysqli_query($conn, $sql)) {
            echo "<script>window.location.href='my_products.php?msg=success';</script>";
            exit();
        } else { $error = "Database Error: " . mysqli_error($conn); }
    } else { $error = "Minimum One Pic is must upload."; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Ad | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-teal: #14b8a6; --p-dark: #0f172a; --p-slate: #64748b; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; }
        .form-card { background: white; border-radius: 35px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.03); padding: 40px; }
        .sidebar-panel { background: var(--p-dark); color: white; border-radius: 35px; padding: 35px; position: sticky; top: 30px; }
        .section-tag { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--p-teal); background: #f0fdfa; padding: 6px 15px; border-radius: 10px; display: inline-block; margin-bottom: 20px; }
        .form-label { font-weight: 700; font-size: 0.9rem; color: #334155; margin-bottom: 10px; }
        .form-control, .form-select { border-radius: 16px; padding: 14px 20px; border: 2px solid #f1f5f9; background: #f8fafc; transition: 0.3s; font-weight: 600; }
        .form-control:focus { border-color: var(--p-teal); background: white; box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1); }
        .upload-area { border: 3px dashed #e2e8f0; border-radius: 25px; padding: 45px; text-align: center; transition: 0.3s; background: #fcfdfe; cursor: pointer; position: relative; overflow: hidden; }
        .upload-area:hover { border-color: var(--p-teal); background: #f0fdfa; }
        #preview-img { width: 100%; max-height: 300px; object-fit: contain; border-radius: 20px; display: none; margin-top: 10px; }
        .price-group { background: #f1f5f9; border-radius: 16px; border: 2px solid #f1f5f9; overflow: hidden; }
        .price-group .input-group-text { background: transparent; border: none; padding-left: 20px; font-weight: 800; color: var(--p-slate); }
        .price-group .form-control { border: none; background: transparent; }
        .toggle-box { display: flex; background: #f1f5f9; padding: 5px; border-radius: 15px; width: fit-content; }
        .toggle-box input { display: none; }
        .toggle-box label { padding: 10px 25px; border-radius: 12px; cursor: pointer; font-weight: 700; transition: 0.3s; margin-bottom: 0; }
        .toggle-box input:checked + label { background: white; color: var(--p-teal); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .btn-publish { background: var(--p-dark); color: white; border: none; padding: 18px; border-radius: 20px; font-weight: 800; width: 100%; transition: 0.4s; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2); }
        .btn-publish:hover { background: var(--p-teal); transform: translateY(-2px); box-shadow: 0 15px 35px rgba(20, 184, 166, 0.3); color: white; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row g-5">
        <div class="col-lg-8">
            <div class="mb-5 d-flex align-items-center gap-3">
                <a href="my_products.php" class="btn btn-white shadow-sm rounded-circle p-3"><i class="fa-solid fa-chevron-left text-muted"></i></a>
                <div>
                    <h2 class="fw-800 mb-1">Create Listing</h2>
                    <p class="text-muted small mb-0">Sell your animals on Pulse Mandi</p>
                </div>
            </div>

            <?php if($error): ?>
                <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 fw-bold shadow-sm">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="form-card">
                
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <span class="section-tag">01. Photos (Multiple)</span>
                    <label class="upload-area d-block" for="imgInput">
                        <i class="fa-solid fa-images fa-2x mb-2" style="color:var(--p-teal)"></i>
                        <h6 class="fw-800 mb-0">Select Images</h6>
                        <input type="file" name="images[]" id="imgInput" class="d-none" accept="image/*" multiple required>
                        <div id="gallery-preview" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </label>
                </div>
                <div class="col-md-6">
                    <span class="section-tag">02. Video (Optional)</span>
                    <label class="upload-area d-block" for="vidInput">
                        <i class="fa-solid fa-video fa-2x text-muted mb-2"></i>
                        <h6 class="fw-800 mb-0">Select Video</h6>
                        <input type="file" name="video" id="vidInput" class="d-none" accept="video/*">
                        <div id="video-info" class="small text-success mt-2 fw-bold"></div>
                    </label>
                </div>
            </div>

                <div class="mb-5">
                    <span class="section-tag">02. Animal Details</span>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Main Category</label>
                            <select name="category" id="mainCategory" class="form-select" required>
                                <option value="">Select Category</option>
                                <option value="Cows">Cows (Gaye)</option>
                                <option value="Goats">Goats (Bakra)</option>
                                <option value="Buffalo">Buffalo (Bhains)</option>
                                <option value="Sheep">Sheep (Bhair/Dumba)</option>
                                <option value="Camels">Camels (Oont)</option>
                                <option value="Donkey">Donkey (Gadha)</option>
                                <option value="Birds">Birds (Parindey)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Sub Category (Breed / Nasal)</label>
                            <select name="brand" id="subCategory" class="form-select" required>
                                <option value="">First Select Category</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Purpose</label>
                            <select name="sub_category" class="form-select" required>
                                <option value="Dairy">Dairy (Milk)</option>
                                <option value="Qurbani">Qurbani / Meat</option>
                                <option value="Breeding">Breeding Quality</option>
                                <option value="Pet">Pet / Hobby</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">City / Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Lahore, Jhang" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Short & Catchy Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Pure Gulabi Bakra for Qurbani" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description (Age, Weight, Teeth)</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe health, weight, and other special traits..." required></textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <span class="section-tag">03. Pricing & Privacy</span>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Asking Price</label>
                            <div class="input-group price-group">
                                <span class="input-group-text">PKR</span>
                                <input type="number" name="price" class="form-control" placeholder="00,000" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Visibility</label>
                            <div class="toggle-box">
                                <input type="radio" name="show_num" id="showPublic" value="1" checked>
                                <label for="showPublic">Show Phone</label>
                                <input type="radio" name="show_num" id="showPrivate" value="0">
                                <label for="showPrivate">Keep Private</label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" name="post_now" class="btn-publish">
                    <i class="fa-solid fa-rocket me-2"></i> PUBLISH LISTING
                </button>

            </form>
        </div>

        <div class="col-lg-4">
            <div class="sidebar-panel">
                <h4 class="fw-800 mb-4" style="color: var(--p-teal);">Selling Guide</h4>
                <div class="d-flex gap-3 mb-4">
                    <i class="fa-solid fa-lightbulb text-teal mt-1"></i>
                    <div>
                        <h6 class="fw-800 mb-1">Detailed Breed</h6>
                        <p class="small opacity-75 mb-0">Choosing the correct breed helps buyers find your ad faster.</p>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-4">
                    <i class="fa-solid fa-camera text-teal mt-1"></i>
                    <div>
                        <h6 class="fw-800 mb-1">Clear Photos</h6>
                        <p class="small opacity-75 mb-0">Ads with clear photos get 5x more calls.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Visual Preview Handler
    const imgInput = document.getElementById('imgInput');
    const previewImg = document.getElementById('preview-img');
    const placeholder = document.getElementById('upload-placeholder');

    imgInput.onchange = evt => {
        const [file] = imgInput.files;
        if (file) {
            previewImg.src = URL.createObjectURL(file);
            previewImg.style.display = 'block';
            placeholder.style.display = 'none';
        }
    }

    // Dynamic Sub-Category (Breed) Logic
    const categoryData = {
        "Cows": ["Sahiwal", "Cholistani", "Australian", "Friesian", "Cross Breed", "Dhanni"],
        "Goats": ["Beetal", "Gulabi", "Teddy", "Kamori", "Rajanpuri", "Barbari", "Tapra"],
        "Buffalo": ["Nili Ravi", "Kundi", "Azi Kheli", "Local Mix"],
        "Sheep": ["Kajla", "Mundra", "Dumba", "Balochi", "Thali"],
        "Camels": ["Marecha", "Brela", "Mountain Camel"],
        "Donkey": ["Sperki", "White Donkey", "Local Breed"],
        "Birds": ["Aseel Chicken", "Fancy Hen", "Parrots", "Pigeons", "Peacocks", "Ostrich"]
    };

    const mainCat = document.getElementById('mainCategory');
    const subCat = document.getElementById('subCategory');

    mainCat.addEventListener('change', function() {
        const selected = this.value;
        subCat.innerHTML = '<option value="">Select Breed</option>';
        
        if(categoryData[selected]) {
            categoryData[selected].forEach(breed => {
                const option = document.createElement('option');
                option.value = breed;
                option.textContent = breed;
                subCat.appendChild(option);
            });
        }
    });


    // Multiple Image Preview
document.getElementById('imgInput').onchange = function() {
    const gallery = document.getElementById('gallery-preview');
    gallery.innerHTML = ''; 
    [...this.files].forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '60px';
            img.style.height = '60px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '10px';
            gallery.appendChild(img);
        }
        reader.readAsDataURL(file);
    });
}

// Video selection feedback
document.getElementById('vidInput').onchange = function() {
    if(this.files[0]) {
        document.getElementById('video-info').innerText = "Video: " + this.files[0].name;
    }
}
</script>

</body>
</html>